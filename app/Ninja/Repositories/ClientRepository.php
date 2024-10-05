<?php

namespace App\Ninja\Repositories;

use App\Events\ClientWasCreated;
use App\Events\ClientWasUpdated;
use App\Jobs\PurgeClientData;
use App\Models\Client;
use App\Models\Contact;

class ClientRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\Client::class;
    }

    public function all()
    {
        return Client::scope()
            ->with('user', 'contacts', 'country')
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($filter = null, $userId = false)
    {
        $query = \Illuminate\Support\Facades\DB::table('clients')
            ->join('accounts', 'accounts.id', '=', 'clients.account_id')
            ->join('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('clients.account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->where('contacts.is_primary', '=', true)
            ->where('contacts.deleted_at', '=', null)
                    //->whereRaw('(clients.name != "" or contacts.first_name != "" or contacts.last_name != "" or contacts.email != "")') // filter out buy now invoices
            ->select(
                \Illuminate\Support\Facades\DB::raw('COALESCE(clients.currency_id, accounts.currency_id) currency_id'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(clients.country_id, accounts.country_id) country_id'),
                \Illuminate\Support\Facades\DB::raw("CONCAT(COALESCE(contacts.first_name, ''), ' ', COALESCE(contacts.last_name, '')) contact"),
                'clients.public_id',
                'clients.name',
                'clients.private_notes',
                'contacts.first_name',
                'contacts.last_name',
                'clients.balance',
                'clients.last_login',
                'clients.created_at',
                'clients.created_at as client_created_at',
                'clients.work_phone',
                'contacts.email',
                'clients.deleted_at',
                'clients.is_deleted',
                'clients.user_id',
                'clients.id_number'
            );

        $this->applyFilters($query, ENTITY_CLIENT);

        if ($filter) {
            $query->where(function ($query) use ($filter): void {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('clients.id_number', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%');
            });
        }

        if ($userId) {
            $query->where('clients.user_id', '=', $userId);
        }

        return $query;
    }

    public function purge($client): void
    {
        dispatch(new PurgeClientData($client));
    }

    public function save($data, $client = null)
    {
        $publicId = $data['public_id'] ?? false;

        if ($client) {
            // do nothing
        } elseif ( ! $publicId || (int) $publicId < 0) {
            $client = Client::createNew();
        } else {
            $client = Client::scope($publicId)->with('contacts')->firstOrFail();
        }

        // auto-set the client id number
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->account->client_number_counter && ! $client->id_number && empty($data['id_number'])) {
            $data['id_number'] = \Illuminate\Support\Facades\Auth::user()->account->getNextNumber();
        }

        if ($client->is_deleted) {
            return $client;
        }

        // convert currency code to id
        if (isset($data['currency_code']) && $data['currency_code']) {
            $currencyCode = mb_strtolower($data['currency_code']);
            $currency = \Illuminate\Support\Facades\Cache::get('currencies')->filter(fn ($item): bool => mb_strtolower($item->code) === $currencyCode)->first();
            if ($currency) {
                $data['currency_id'] = $currency->id;
            }
        }

        // convert country code to id
        if (isset($data['country_code'])) {
            $countryCode = mb_strtolower($data['country_code']);
            $country = \Illuminate\Support\Facades\Cache::get('countries')->filter(fn ($item): bool => mb_strtolower($item->iso_3166_2) === $countryCode || mb_strtolower($item->iso_3166_3) === $countryCode)->first();
            if ($country) {
                $data['country_id'] = $country->id;
            }
        }

        // convert shipping country code to id
        if (isset($data['shipping_country_code'])) {
            $countryCode = mb_strtolower($data['shipping_country_code']);
            $country = \Illuminate\Support\Facades\Cache::get('countries')->filter(fn ($item): bool => mb_strtolower($item->iso_3166_2) === $countryCode || mb_strtolower($item->iso_3166_3) === $countryCode)->first();
            if ($country) {
                $data['shipping_country_id'] = $country->id;
            }
        }

        // set default payment terms
        if (auth()->check() && ! isset($data['payment_terms'])) {
            $data['payment_terms'] = auth()->user()->account->payment_terms;
        }

        $client->fill($data);
        $client->save();

        /*
        if ( ! isset($data['contact']) && ! isset($data['contacts'])) {
            return $client;
        }
        */

        $first = true;
        $contacts = isset($data['contact']) ? [$data['contact']] : ($data['contacts'] ?? [[]]);
        $contactIds = [];

        // If the primary is set ensure it's listed first
        usort($contacts, function ($left, $right): int|float {
            if (isset($right['is_primary'], $left['is_primary'])) {
                return $right['is_primary'] - $left['is_primary'];
            }

            return 0;
        });

        foreach ($contacts as $contact) {
            $contact = $client->addContact($contact, $first);
            $contactIds[] = $contact->public_id;
            $first = false;
        }

        if ( ! $client->wasRecentlyCreated) {
            foreach ($client->contacts as $contact) {
                if ( ! in_array($contact->public_id, $contactIds)) {
                    $contact->delete();
                }
            }
        }

        if ( ! $publicId || (int) $publicId < 0) {
            event(new ClientWasCreated($client));
        } else {
            event(new ClientWasUpdated($client));
        }

        return $client;
    }

    public function findPhonetically($clientName)
    {
        $clientNameMeta = metaphone($clientName);

        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $clientId = 0;

        $clients = Client::scope()->get(['id', 'name', 'public_id']);

        foreach ($clients as $client) {
            $map[$client->id] = $client;

            if ( ! $client->name) {
                continue;
            }

            $similar = similar_text($clientNameMeta, metaphone($client->name), $percent);

            if ($percent > $max) {
                $clientId = $client->id;
                $max = $percent;
            }
        }

        $contacts = Contact::scope()->get(['client_id', 'first_name', 'last_name', 'public_id']);

        foreach ($contacts as $contact) {
            if ( ! $contact->getFullName()) {
                continue;
            }
            if ( ! isset($map[$contact->client_id])) {
                continue;
            }
            $similar = similar_text($clientNameMeta, metaphone($contact->getFullName()), $percent);

            if ($percent > $max) {
                $clientId = $contact->client_id;
                $max = $percent;
            }
        }

        return ($clientId && isset($map[$clientId])) ? $map[$clientId] : null;
    }
}

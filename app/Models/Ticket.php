<?php

namespace App\Models;

use App\Constants\Domain;
use App\Libraries\Utils;
use App\Ninja\Presenters\TicketPresenter;
use App\Services\TicketTemplateService;
use DateTime;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Ticket.
 */
class Ticket extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = TicketPresenter::class;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'subject',
        'description',
        'private_notes',
        'due_at',
        'ccs',
        'priority_id',
        'agent_id',
        'category_id',
        'is_deleted',
        'is_internal',
        'status_id',
        'contact_key',
        'ticket_number',
        'reopened',
        'closed',
        'merged_parent_ticket_id',
        'parent_ticket_id',
        'user_id',
    ];

    /**
     * @return array{invoice: mixed[]|Translator|string|null, quote: mixed[]|Translator|string|null, payment: mixed[]|Translator|string|null, credit: mixed[]|Translator|string|null, expense: mixed[]|Translator|string|null, task: mixed[]|Translator|string|null, project: mixed[]|Translator|string|null}
     */
    public static function relationEntities(): array
    {
        return [
            'invoice' => trans('texts.invoice'),
            'quote'   => trans('texts.quote'),
            'payment' => trans('texts.payment'),
            'credit'  => trans('texts.credit'),
            'expense' => trans('texts.expense'),
            'task'    => trans('texts.task'),
            'project' => trans('texts.project'),
        ];
    }

    /**
     * @return array{invoice: mixed[]|Translator|string|null, quote: mixed[]|Translator|string|null, payment: mixed[]|Translator|string|null}
     */
    public static function clientRelationEntities(): array
    {
        return [
            'invoice' => trans('texts.invoice'),
            'quote'   => trans('texts.quote'),
            'payment' => trans('texts.payment'),
        ];
    }

    /**
     * Used for ticket autocomplete.
     *
     * @return string
     */
    public static function templateVariables()
    {
        $arr[]['description'] = '$ticketNumber';
        $arr[]['description'] = '$ticketStatus';
        $arr[]['description'] = '$client';
        $arr[]['description'] = '$contact';
        $arr[]['description'] = '$priority';
        $arr[]['description'] = '$dueDate';
        $arr[]['description'] = '$agent';
        $arr[]['description'] = '$status';
        $arr[]['description'] = '$subject';
        $arr[]['description'] = '$description';
        $arr[]['description'] = '$signature';

        return json_encode($arr);
    }

    public static function getPriorityArray(): array
    {
        return [
            ['id' => TICKET_PRIORITY_LOW, 'name' => trans('texts.low')],
            ['id' => TICKET_PRIORITY_MEDIUM, 'name' => trans('texts.medium')],
            ['id' => TICKET_PRIORITY_HIGH, 'name' => trans('texts.high')],
        ];
    }

    /**
     * @param bool $entityType
     */
    public static function getStatuses($entityType = false): array
    {
        return [
            TICKET_STATUS_NEW    => trans('texts.new'),
            TICKET_STATUS_OPEN   => trans('texts.open'),
            TICKET_STATUS_CLOSED => trans('texts.closed'),
            TICKET_STATUS_MERGED => trans('texts.merged'),
        ];
    }

    /**
     * @return array|Translator|null|string
     */
    public static function getStatusNameById($statusId)
    {
        switch ($statusId) {
            case TICKET_STATUS_NEW:
                return trans('texts.new');
            case TICKET_STATUS_OPEN:
                return trans('texts.open');
            case TICKET_STATUS_CLOSED:
                return trans('texts.closed');
            case TICKET_STATUS_MERGED:
                return trans('texts.merged');
        }
    }

    /**
     * @return int|mixed
     */
    public static function getNextTicketNumber($companyId): string
    {
        $ticket = self::whereCompanyId($companyId)->withTrashed()->orderBy('id', 'DESC')->first();

        $company = Company::where('id', '=', $companyId)->first();

        if ($ticket) {
            return str_pad($ticket->company->company_ticket_settings->ticket_number_start, $ticket->company->invoice_number_padding, '0', STR_PAD_LEFT);
        }

        return str_pad(1, $company->invoice_number_padding, '0', STR_PAD_LEFT);
    }

    public static function buildTicketBody(self $ticket, string $response): string
    {
        $ticketVariables = TicketTemplateService::getVariables($ticket);

        return str_replace(array_keys($ticketVariables), array_values($ticketVariables), $response);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * @return mixed
     */
    public function templates()
    {
        return $this->hasMany(TicketTemplate::class);
    }

    /**
     * @return mixed
     */
    public function documents(): Builder
    {
        return $this->hasMany(Document::class)->orderBy('id');
    }

    /**
     * @return mixed
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_key', 'contact_key');
    }

    /**
     * @return mixed
     */
    public function invitations(): Builder
    {
        return $this->hasMany(TicketInvitation::class)->orderBy('ticket_invitations.contact_id');
    }

    /**
     * @return mixed
     */
    public function parent_ticket()
    {
        return $this->belongsTo(static::class, 'parent_ticket_id');
    }

    /**
     * @return mixed
     */
    public function child_tickets()
    {
        return $this->hasMany(static::class, 'parent_ticket_id');
    }

    /**
     * @return mixed
     */
    public function merged_ticket_parent()
    {
        return $this->belongsTo(static::class, 'merged_parent_ticket_id');
    }

    /**
     * @return mixed
     */
    public function merged_children()
    {
        return $this->hasMany(static::class, 'merged_parent_ticket_id');
    }

    /**
     * @return mixed
     */
    public function relations()
    {
        return $this->hasMany(TicketRelation::class);
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TICKET;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/tickets/{$this->public_id}";
    }

    /**
     * @return string
     */
    public function getContactName()
    {
        $contact = Contact::withTrashed()->where('contact_key', '=', $this->contact_key)->first();
        if (! $contact) {
            return;
        }
        if ($contact->is_deleted) {
            return;
        }

        return $contact->getFullName();
    }

    /**
     * @return string
     */
    public function getPriorityName()
    {
        switch ($this->priority_id) {
            case TICKET_PRIORITY_LOW:
                return trans('texts.low');
            case TICKET_PRIORITY_MEDIUM:
                return trans('texts.medium');
            case TICKET_PRIORITY_HIGH:
                return trans('texts.high');
        }
    }

    /**
     * @return string
     */
    public function getDueDate()
    {
        if (! $this->due_date) {
            return trans('texts.no_due_date');
        }
        if ($this->due_date == '0000-00-00 00:00:00') {
            return trans('texts.no_due_date');
        }

        return Utils::fromSqlDateTime($this->due_date);
    }

    /**
     * @return DateTime|string
     */
    public function getMinDueDate()
    {
        return Utils::fromSqlDateTime($this->created_at);
    }

    /**
     * @return string
     */
    public function agentName()
    {
        if (! $this->agent) {
            return trans('texts.unassigned');
        }
        if (! $this->agent->getName()) {
            return trans('texts.unassigned');
        }

        return $this->agent->getName();
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return $this->getStatus();
    }

    /**
     * @return array|Translator|null|string
     */
    public function getStatus()
    {
        switch ($this->status_id) {
            case TICKET_STATUS_NEW:
                return trans('texts.new');
            case TICKET_STATUS_OPEN:
                return trans('texts.open');
            case TICKET_STATUS_CLOSED:
                return trans('texts.closed');
            case TICKET_STATUS_MERGED:
                return trans('texts.merged');
        }
    }

    /**
     * @return mixed
     */
    public function agent()
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    /**
     * @return string
     */
    public function getCCs()
    {
        $ccEmailArray = [];
        $ccs = json_decode($this->ccs, true);

        if (! is_array($ccs)) {
            return;
        }

        foreach ($ccs as $contact_key) {
            $c = Contact::where('contact_key', '=', $contact_key)->first();
            array_push($ccEmailArray, strtolower($c->email));
        }

        return implode(', ', $ccEmailArray);
    }

    /**
     * @return mixed
     */
    public function getTicketFromName()
    {
        return config('ninja.tickets.ticket_support_email_name');
    }

    /**
     * @return mixed
     */
    public function getTicketFromEmail()
    {
        return config('ninja.tickets.ticket_support_email');
    }

    /**
     * @return mixed
     */
    public function getTicketTemplate($templateId)
    {
        return TicketTemplate::where('id', '=', $templateId)->first();
    }

    public function getTicketEmailFormat(): string
    {
        if (! Utils::isNinjaProd()) {
            $domain = config('ninja.tickets.ticket_support_domain');
        } else {
            $domain = Domain::getSupportDomainFromId($this->company->domain_id);
        }

        if ($this->is_internal == true) {
            return $this->company->company_ticket_settings->support_email_local_part . '+' . $this->ticket_number . '@' . $domain;
        }

        return $this->ticket_number . '+' . $this->getContactTicketHash() . '@' . $domain;
    }

    /**
     * @return mixed
     */
    public function getContactTicketHash()
    {
        $ticketInvitation = TicketInvitation::whereTicketId($this->id)->whereContactId($this->contact->id)->first();

        return $ticketInvitation->ticket_hash;
    }

    /**
     * @return mixed
     */
    public function getClientMergeableTickets()
    {
        $getInternal = false;

        if ($this->is_internal == true) {
            $getInternal = true;
        }

        return self::scope()
            ->where('client_id', '=', $this->client_id)
            ->where('public_id', '!=', $this->public_id)
            ->where('merged_parent_ticket_id', '=', null)
            ->where('status_id', '!=', 3)
            ->where('is_internal', '=', $getInternal)
            ->get();
    }

    public function isMergeAble(): bool
    {
        if ($this->status_id == 3) {
            return false;
        }
        if ($this->is_deleted) {
            return false;
        } elseif ($this->merged_parent_ticket_id != null) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getLastComment()
    {
        return $this->comments()->first();
    }

    /**
     * @return mixed
     */
    public function comments(): Builder
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'DESC');
    }
}

Ticket::creating(
    function ($ticket): void {
    }
);

Ticket::created(
    function ($ticket): void {
        $company_ticket_settings = $ticket->company->company_ticket_settings;
        $company_ticket_settings->increment('ticket_number_start', 1);
        $company_ticket_settings->save();
    }
);

Ticket::updating(
    function ($ticket): void {
    }
);

Ticket::updated(
    function ($ticket): void {
    }
);

Expense::deleting(
    function ($ticket): void {
    }
);

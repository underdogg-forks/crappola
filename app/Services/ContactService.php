<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Repositories\ContactRepository;

/**
 * Class ContactService.
 */
class ContactService extends BaseService
{
    /**
     * @var ContactRepository
     */
    protected $contactRepo;

    /**
     * ContactService constructor.
     */
    public function __construct(ContactRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    /**
     * @return mixed|null
     */
    public function save($data, $contact = null)
    {
        if (isset($data['client_id']) && $data['client_id']) {
            $data['client_id'] = Client::getPrivateId($data['client_id']);
        }

        return $this->contactRepo->save($data, $contact);
    }

    /**
     * @return ContactRepository
     */
    protected function getRepo()
    {
        return $this->contactRepo;
    }
}

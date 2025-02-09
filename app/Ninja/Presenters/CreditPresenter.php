<?php

namespace App\Ninja\Presenters;

use App\Libraries\Utils;
use DateTime;

/**
 * Class CreditPresenter.
 */
class CreditPresenter extends EntityPresenter
{
    public function client()
    {
        return $this->entity->client ? $this->entity->client->getDisplayName() : '';
    }

    /**
     * @return DateTime|string
     */
    public function credit_date()
    {
        return Utils::fromSqlDate($this->entity->credit_date);
    }
}

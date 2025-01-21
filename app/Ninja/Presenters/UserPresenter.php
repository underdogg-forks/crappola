<?php

namespace App\Ninja\Presenters;

class UserPresenter extends EntityPresenter
{
    public function email()
    {
        return htmlentities(sprintf('%s <%s>', $this->fullName(), $this->entity->email));
    }

    public function fullName()
    {
        return $this->entity->first_name . ' ' . $this->entity->last_name;
    }

    public function statusCode()
    {
        $status = '';
        $user = $this->entity;
        $company = $user->company;

        if ($user->confirmed) {
            $status .= 'C';
        } elseif ($user->registered) {
            $status .= 'R';
        } else {
            $status .= 'N';
        }

        if ($company->isTrial()) {
            $status .= 'T';
        } elseif ($company->isEnterprise()) {
            $status .= 'E';
        } elseif ($company->isPro()) {
            $status .= 'P';
        } else {
            $status .= 'H';
        }

        return $status;
    }
}

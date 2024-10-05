<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListExpenseIntent extends BaseIntent
{
    public function process(): void
    {
        $this->loadStates(ENTITY_EXPENSE);

        return redirect('/expenses');
    }
}

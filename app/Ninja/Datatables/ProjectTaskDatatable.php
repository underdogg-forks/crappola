<?php

namespace App\Ninja\Datatables;

class ProjectTaskDatatable extends TaskDatatable
{
    public function columns(): array
    {
        $columns = parent::columns();

        unset($columns[1]);

        return $columns;
    }
}

<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\EntityDatatable;
use Exception;
use Yajra\DataTables\Services\DataTable;

/**
 * Class DatatableService.
 */
class DatatableService
{
    public function createDatatable(EntityDatatable $datatable, $query)
    {
        $table = datatables()->eloquent($query);

        if ($datatable->isBulkEdit) {
            $table->addColumn('checkbox', function ($model) use ($datatable) {
                return '<input type="checkbox" name="ids[]" value="' . $model->id
                    . '" ' . Utils::getEntityRowClass($model) . '>';
            });
        }

        foreach ($datatable->columns() as $column) {
            // set visible to true by default
            if (count($column) == 2) {
                $column[] = true;
            }

            [$field, $value, $visible] = $column;

            if ($visible) {
                $table->addColumn($field, $value);
                $orderColumns[] = $field;
            }
        }

        if (count($datatable->actions())) {
            $this->createDropdown($datatable, $table);
        }

        return $table->orderColumns($orderColumns)->make();
    }

    public function createDropdown($datatable, $table)
    {
        return $table->addColumn('dropdown', function ($model) use ($datatable) {
            $hasAction = false;
            $str = '<center style="min-width:100px">';

            if (property_exists($model, 'is_deleted') && $model->is_deleted) {
                $str .= '<button type="button" class="btn btn-sm btn-danger tr-status">' . trans('texts.deleted') . '</button>';
            } elseif ($model->deleted_at && $model->deleted_at !== '0000-00-00') {
                $str .= '<button type="button" class="btn btn-sm btn-warning tr-status">' . trans('texts.archived') . '</button>';
            } else {
                $str .= '<div class="tr-status"></div>';
            }

            $dropdown_contents = '';

            $lastIsDivider = false;
            if (! property_exists($model, 'is_deleted') || ! $model->is_deleted) {
                foreach ($datatable->actions() as $action) {
                    if (count($action)) {
                        // if show function isn't set default to true
                        if (count($action) == 2) {
                            $action[] = function () {
                                return true;
                            };
                        }
                        [$value, $url, $visible] = $action;
                        if ($visible($model)) {
                            if ($value == '--divider--') {
                                $dropdown_contents .= '<li class="divider"></li>';
                                $lastIsDivider = true;
                            } else {
                                $urlVal = $url($model);
                                $urlStr = is_string($urlVal) ? $urlVal : $urlVal['url'];
                                $attributes = '';
                                if (! empty($urlVal['attributes'])) {
                                    $attributes = ' ' . $urlVal['attributes'];
                                }

                                $dropdown_contents .= "<li><a href=\"$urlStr\"{$attributes}>{$value}</a></li>";
                                $hasAction = true;
                                $lastIsDivider = false;
                            }
                        }
                    } elseif (! $lastIsDivider) {
                        $dropdown_contents .= '<li class="divider"></li>';
                        $lastIsDivider = true;
                    }
                }

                if (! $hasAction) {
                    return '';
                }

                if ($lastIsDivider) {
                    $dropdown_contents .= '<li class="divider"></li>';
                }

                if (! $model->deleted_at || $model->deleted_at == '0000-00-00') {
                    if (($datatable->entityType != ENTITY_USER || $model->id)) {
                        $dropdown_contents .= "<li><a href=\"javascript:submitForm_{$datatable->entityType}('archive', {$model->id})\">"
                            . mtrans($datatable->entityType, "archive_{$datatable->entityType}") . '</a></li>';
                    }
                }
            }

            if ($model->deleted_at && $model->deleted_at != '0000-00-00') {
                $dropdown_contents .= "<li><a href=\"javascript:submitForm_{$datatable->entityType}('restore', {$model->id})\">"
                    . mtrans($datatable->entityType, "restore_{$datatable->entityType}") . '</a></li>';
            }

            if (property_exists($model, 'is_deleted') && ! $model->is_deleted) {
                $dropdown_contents .= "<li><a href=\"javascript:submitForm_{$datatable->entityType}('delete', {$model->id})\">"
                    . mtrans($datatable->entityType, "delete_{$datatable->entityType}") . '</a></li>';
            }

            if (! empty($dropdown_contents)) {
                $str .= '<div class="btn-group tr-action" style="height:auto;display:none">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" style="width:100px">
                        ' . trans('texts.select') . ' <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">';
                $str .= $dropdown_contents . '</ul>';
            }

            return $str . '</div></center>';
        });
    }
}

<?php

namespace App\Jobs;

use App\Libraries\Utils;
use Excel;
use Exception;

class ExportReportResults extends Job
{
    public $user;

    /**
     * @var lowercase-string
     */
    public $format;

    public $reportType;

    public $params;

    public function __construct($user, $format, $reportType, $params)
    {
        $this->user = $user;
        $this->format = mb_strtolower($format);
        $this->reportType = $reportType;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( ! $this->user->hasPermission('view_reports')) {
            return false;
        }

        $format = $this->format;
        $reportType = $this->reportType;
        $params = $this->params;

        $data = $params['displayData'];
        $columns = $params['columns'];
        $totals = $params['reportTotals'];
        $report = $params['report'];

        $filename = sprintf('%s-%s_invoiceninja-', $params['startDate'], $params['endDate']) . mb_strtolower(Utils::normalizeChars(trans('texts.' . $reportType))) . '-report';

        $formats = ['csv', 'pdf', 'xlsx', 'zip'];
        if ( ! in_array($format, $formats)) {
            throw new Exception('Invalid format request to export report');
        }

        //Get labeled header
        $data = array_merge(
            [
                array_map(fn ($col) => $col['label'], $report->tableHeaderArray()),
            ],
            $data
        );

        $summary = [];
        if (array_values($totals) !== []) {
            $summary[] = array_merge([
                trans('texts.totals'),
            ], array_map(fn ($key) => trans('texts.' . $key), array_keys(array_values(array_values($totals)[0])[0])));
        }

        foreach ($totals as $currencyId => $each) {
            foreach ($each as $dimension => $val) {
                $tmp = [];
                $tmp[] = Utils::getFromCache($currencyId, 'currencies')->name . (($dimension) ? ' - ' . $dimension : '');
                foreach ($val as $field => $value) {
                    $tmp[] = $field == 'duration' ? Utils::formatTime($value) : Utils::formatMoney($value, $currencyId);
                }

                $summary[] = $tmp;
            }
        }

        return Excel::create($filename, function ($excel) use ($data, $reportType, $format, $summary): void {
            $excel->sheet(trans('texts.' . $reportType), function ($sheet) use ($data, $format, $summary): void {
                $sheet->setOrientation('landscape');
                $sheet->freezeFirstRow();
                if ($format == 'pdf') {
                    $sheet->setAllBorders('thin');
                }

                if ($format == 'csv') {
                    $sheet->rows(array_merge($data, [[]], $summary));
                } else {
                    $sheet->rows($data);
                }

                // Styling header
                $sheet->cells('A1:' . Utils::num2alpha(count($data[0]) - 1) . '1', function ($cells): void {
                    $cells->setBackground('#777777');
                    $cells->setFontColor('#FFFFFF');
                    $cells->setFontSize(13);
                    $cells->setFontFamily('Calibri');
                    $cells->setFontWeight('bold');
                });
                $sheet->setAutoSize(true);
            });

            if ($summary !== []) {
                $excel->sheet(trans('texts.totals'), function ($sheet) use ($summary, $format): void {
                    $sheet->setOrientation('landscape');
                    $sheet->freezeFirstRow();

                    if ($format == 'pdf') {
                        $sheet->setAllBorders('thin');
                    }

                    $sheet->rows($summary);

                    // Styling header
                    $sheet->cells('A1:' . Utils::num2alpha(count($summary[0]) - 1) . '1', function ($cells): void {
                        $cells->setBackground('#777777');
                        $cells->setFontColor('#FFFFFF');
                        $cells->setFontSize(13);
                        $cells->setFontFamily('Calibri');
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setAutoSize(true);
                });
            }
        });
    }
}

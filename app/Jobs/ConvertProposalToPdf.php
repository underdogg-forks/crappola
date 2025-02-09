<?php

namespace App\Jobs;

use App\Libraries\CurlUtils;
use App\Libraries\Utils;
use Exception;
use Illuminate\Support\Str;

class ConvertProposalToPdf extends Job
{
    public $proposal;

    public function __construct($proposal)
    {
        $this->proposal = $proposal;
    }

    public function handle()
    {
        if ( ! env('PHANTOMJS_CLOUD_KEY') && ! env('PHANTOMJS_BIN_PATH')) {
            return false;
        }

        if (Utils::isTravis()) {
            return false;
        }

        $proposal = $this->proposal;
        $link = $proposal->getLink(true, true);
        $phantomjsSecret = env('PHANTOMJS_SECRET');
        $phantomjsLink = sprintf('%s?phantomjs=true&phantomjs_secret=%s', $link, $phantomjsSecret);
        $filename = sprintf('%s/storage/app/%s.pdf', base_path(), mb_strtolower(Str::random(RANDOM_KEY_LENGTH)));

        try {
            $pdf = CurlUtils::renderPDF($phantomjsLink, $filename);

            if ( ! $pdf && ($key = env('PHANTOMJS_CLOUD_KEY'))) {
                $url = sprintf('http://api.phantomjscloud.com/api/browser/v2/%s/?request=%%7Burl:%%22%s?phantomjs=true%%26phantomjs_secret=%s%%22,renderType:%%22pdf%%22%%7D', $key, $link, $phantomjsSecret);
                $pdf = CurlUtils::get($url);
            }
        } catch (Exception $exception) {
            Utils::logError(sprintf('PhantomJS - Failed to load %s: %s', $phantomjsLink, $exception->getMessage()));

            return false;
        }

        if ( ! $pdf || mb_strlen($pdf) < 200) {
            Utils::logError(sprintf('PhantomJS - Invalid response %s: %s', $phantomjsLink, $pdf));

            return false;
        }

        return $pdf;
    }
}

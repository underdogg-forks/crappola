<?php

namespace App\Console\Commands;

use App\Libraries\CurlUtils;
use Illuminate\Console\Command;

class MobileLocalization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ninja:mobile-localization {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mobile localization resources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = mb_strtolower($this->option('type'));

        switch ($type) {
            case 'laravel':
                $this->laravelResources();
                break;
            default:
                $this->flutterResources();
                break;
        }
    }

    protected function getOptions()
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'Type', null],
        ];
    }

    private function laravelResources(): void
    {
        $resources = $this->getResources();

        foreach ($resources as $key => $val) {
            $transKey = "texts.{$key}";
            if (trans($transKey) == $transKey) {
                echo "'{$key}' => '{$val}',\n";
            }
        }
    }

    private function flutterResources(): void
    {
        $languages = cache('languages');
        $resources = $this->getResources();

        foreach ($languages as $language) {
            if ($language->locale == 'en') {
                continue;
            }

            echo "'{$language->locale}': {\n";

            foreach ($resources as $key => $val) {
                $text = trim(addslashes(trans("texts.{$key}", [], $language->locale)));
                if (mb_substr($text, 0, 6) == 'texts.') {
                    $text = $resources->{$key};
                }

                $text = str_replace(['<b>', '</b>'], '', $text);
                $text = str_replace(['<i>', '</i>'], '', $text);
                $text = str_replace(['<strong>', '</strong>'], '', $text);

                echo "'{$key}': '{$text}',\n";
            }

            echo "},\n";
        }
    }

    private function getResources()
    {
        $url = 'https://raw.githubusercontent.com/invoiceninja/flutter-client/develop/lib/utils/i18n.dart';
        $data = CurlUtils::get($url);

        $start = mb_strpos($data, 'do not remove comment') + 25;
        $end = mb_strpos($data, '},', $start);
        $data = mb_substr($data, $start, $end - $start - 5);

        $data = str_replace("\n", '', $data);
        $data = str_replace('"', "\'", $data);
        $data = str_replace("'", '"', $data);

        return json_decode('{' . rtrim($data, ',') . '}');
    }
}

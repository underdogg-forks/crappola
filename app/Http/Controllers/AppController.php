<?php

namespace App\Http\Controllers;

use App\Events\UserSettingsChanged;
use App\Libraries\Utils;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Invoice;
use App\Ninja\Mailers\Mailer;
use App\Ninja\Repositories\AccountRepository;
use App\Services\EmailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AppController extends BaseController
{
    protected $accountRepo;

    protected Mailer $mailer;

    protected EmailService $emailService;

    public function __construct(AccountRepository $accountRepo, Mailer $mailer, EmailService $emailService)
    {
        //parent::__construct();

        $this->accountRepo = $accountRepo;
        $this->mailer = $mailer;
        $this->emailService = $emailService;
    }

    public function showSetup()
    {
        return View::make('setup/setup');
    }

    public function doSetup(Request $request)
    {
        $valid = false;
        $test = $request->get('test');

        $app = $request->get('app');
        $app['key'] = env('APP_KEY') ?: strtolower(str_random(RANDOM_KEY_LENGTH));
        $app['debug'] = $request->get('debug') ? 'true' : 'false';
        $app['https'] = $request->get('https') ? 'true' : 'false';

        $database = $request->get('database');
        $dbType = 'mysql'; // $database['default'];
        $database['connections'] = [$dbType => $database['type']];
        $mail = $request->get('mail');

        if ($test == 'mail') {
            return self::testMail($mail);
        }

        $valid = self::testDatabase($database);
        if ($test == 'db') {
            return $valid === true ? 'Success' : $valid;
        }

        if (! $valid) {
            return Redirect::to('/setup')->withInput();
        }

        /*if (Utils::isDatabaseSetup() && Company::count() > 0) {
            return Redirect::to('/');
        }*/

        $_ENV['APP_ENV'] = 'development';
        $_ENV['APP_DEBUG'] = $app['debug'];
        $_ENV['APP_LOCALE'] = 'en';
        $_ENV['APP_URL'] = $app['url'];
        $_ENV['APP_KEY'] = $app['key'];
        $_ENV['APP_CIPHER'] = env('APP_CIPHER', 'AES-256-CBC');
        $_ENV['REQUIRE_HTTPS'] = $app['https'];
        $_ENV['DB_TYPE'] = $dbType;
        $_ENV['DB_HOST'] = $database['type']['host'];
        $_ENV['DB_DATABASE'] = $database['type']['database'];
        $_ENV['DB_USERNAME'] = $database['type']['username'];
        $_ENV['DB_PASSWORD'] = $database['type']['password'];
        $_ENV['MAIL_DRIVER'] = $mail['driver'];
        $_ENV['MAIL_PORT'] = $mail['port'];
        $_ENV['MAIL_ENCRYPTION'] = $mail['encryption'];
        $_ENV['MAIL_HOST'] = $mail['host'];
        $_ENV['MAIL_USERNAME'] = $mail['username'];
        $_ENV['MAIL_FROM_NAME'] = $mail['from']['name'];
        $_ENV['MAIL_FROM_ADDRESS'] = $mail['from']['address'];
        $_ENV['MAIL_PASSWORD'] = $mail['password'];
        $_ENV['PHANTOMJS_CLOUD_KEY'] = 'a-demo-key-with-low-quota-per-ip-address';
        $_ENV['PHANTOMJS_SECRET'] = strtolower(str_random(RANDOM_KEY_LENGTH));
        $_ENV['MAILGUN_DOMAIN'] = $mail['mailgun_domain'];
        $_ENV['MAILGUN_SECRET'] = $mail['mailgun_secret'];

        $config = '';
        foreach ($_ENV as $key => $val) {
            if (is_array($val)) {
                continue;
            }
            if (preg_match('/\s/', $val)) {
                $val = "'{$val}'";
            }
            $config .= "{$key}={$val}\n";
        }

        // Write Config Settings
        $fp = fopen(base_path() . '/.env', 'w');
        fwrite($fp, $config);
        fclose($fp);

        //Utils::isDatabaseSetup

        // == DB Migrate & Seed == //
        try {
            set_time_limit(60 * 5); // shouldn't take this long but just in case
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (Exception $e) {
            dd($e);
            /*Utils::logError($e);

            return Response::make($e->getMessage(), 500);*/
        }

        //$sqlFile = base_path() . '/database/setup.sql';
        //DB::unprepared(file_get_contents($sqlFile));

        Cache::flush();
        //Artisan::call('db:seed', ['--force' => true, '--class' => 'UpdateSeeder']);

        if (! Company::count()) {
            $firstName = trim($request->get('first_name'));
            $lastName = trim($request->get('last_name'));
            $email = trim(strtolower($request->get('email')));
            $password = trim($request->get('password'));
            $company = $this->accountRepo->create($firstName, $lastName, $email, $password);

            $user = $company->users()->first();
            //$user->acceptLatestTerms(request()->getClientIp());
            $user->save();
        }

        return Redirect::to('/login');
    }

    private function testMail($mail)
    {
        $email = $mail['from']['address'];
        $fromName = $mail['from']['name'];

        foreach ($mail as $key => $val) {
            Config::set("mail.{$key}", $val);
        }

        Config::set('mail.from.address', $email);
        Config::set('mail.from.name', $fromName);

        $data = [
            'text'      => 'Test email',
            'fromEmail' => $email,
        ];

        try {
            $response = $this->mailer->sendTo($email, $email, $fromName, 'Test email', 'contact', $data);

            return $response === true ? 'Sent' : $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function testDatabase($database)
    {
        $dbType = 'mysql'; // $database['default'];
        Config::set('database.default', $dbType);
        foreach ($database['connections'][$dbType] as $key => $val) {
            Config::set("database.connections.{$dbType}.{$key}", $val);
        }

        try {
            DB::reconnect();
            $valid = DB::connection()->getDatabaseName() ? true : false;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $valid;
    }

    public function install()
    {
        if (Utils::isNinjaProd()) {
            return Redirect::to('/');
        }
        if (Utils::isDatabaseSetup()) {
            return Redirect::to('/');
        }
        try {
            set_time_limit(60 * 5); // shouldn't take this long but just in case
            Artisan::call('migrate', ['--force' => true]);
            if (Industry::count() == 0) {
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (Exception $e) {
            Utils::logError($e);

            return Response::make($e->getMessage(), 500);
        }

        return Redirect::to('/');
    }

    public function update()
    {
        if (! Utils::isNinjaProd()) {
            if ($password = env('UPDATE_SECRET')) {
                if (! hash_equals($password, request('secret') ?: '')) {
                    $message = 'Invalid secret: /update?secret=<value>';
                    Utils::logError($message);
                    echo $message;
                    exit;
                }
            }

            try {
                set_time_limit(60 * 5);
                $this->checkInnoDB();

                $cacheCompiled = base_path('bootstrap/cache/compiled.php');
                if (file_exists($cacheCompiled)) {
                    unlink($cacheCompiled);
                }
                $cacheServices = base_path('bootstrap/cache/services.json');
                if (file_exists($cacheServices)) {
                    unlink($cacheServices);
                }

                Artisan::call('clear-compiled');
                Artisan::call('cache:clear');
                //Artisan::call('debugbar:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('config:clear');
                Auth::logout();
                Cache::flush();
                Session::flush();

                //Artisan::call('migrate', ['--force' => true]);
                //Artisan::call('db:seed', ['--force' => true, '--class' => 'UpdateSeeder']);

                Event::dispatch(new UserSettingsChanged());

                // legacy fix: check cipher is in .env file
                if (! env('APP_CIPHER')) {
                    $fp = fopen(base_path() . '/.env', 'a');
                    fwrite($fp, "\nAPP_CIPHER=AES-256-CBC");
                    fclose($fp);
                }

                // show message with link to Trello board
                $message = trans('texts.see_whats_new', ['version' => NINJA_VERSION]);
                $message = link_to(RELEASES_URL, $message, ['target' => '_blank']);
                $message = sprintf('%s - %s', trans('texts.processed_updates'), $message);
                Session::flash('warning', $message);
            } catch (Exception $e) {
                //Utils::logError($e);

                return Response::make($e->getMessage(), 500);
            }
        }

        return Redirect::to('/?clear_cache=true');
    }

    // MySQL changed the default table type from MyISAM to InnoDB
    // We need to make sure all tables are InnoDB to prevent migration failures
    public function checkInnoDB(): void
    {
        $result = DB::select("SELECT engine
                    FROM information_schema.TABLES
                    WHERE TABLE_NAME='clients' AND TABLE_SCHEMA='ninja'");

        if (count($result) && $result[0]->engine == 'InnoDB') {
            return;
        }

        $tables = DB::select('SHOW TABLES');
        $sql = "SET sql_mode = 'ALLOW_INVALID_DATES';\n";

        foreach ($tables as $table) {
            $fieldName = 'Tables_in_' . env('DB_DATABASE');
            $sql .= "ALTER TABLE {$table->$fieldName} engine=InnoDB;\n";
        }

        DB::unprepared($sql);
    }

    public function checkData()
    {
        try {
            Artisan::call('ninja:check-data');
            Artisan::call('ninja:init-lookup', ['--validate' => true]);

            // check error log is empty
            $errorLog = storage_path('logs/laravel-error.log');
            if (file_exists($errorLog)) {
                return 'Failure: error log exists';
            }

            return RESULT_SUCCESS;
        } catch (Exception $exception) {
            return $exception->getMessage() ?: RESULT_FAILURE;
        }
    }

    public function errors()
    {
        if (Utils::isNinjaProd()) {
            return redirect('/');
        }

        $errors = Utils::getErrors();

        return view('errors.list', compact('errors'));
    }

    public function testHeadless(): void
    {
        $invoice = Invoice::scope()->orderBy('id')->first();

        if (! $invoice) {
            dd('Please create an invoice to run this test');
        }

        header('Content-type:application/pdf');
        echo $invoice->getPDFString();
        exit;
    }

    public function runCommand()
    {
        if (Utils::isNinjaProd()) {
            abort(400, 'Not allowed');
        }

        $command = request()->command;
        $options = request()->options ?: [];
        $secret = env('COMMAND_SECRET');
        if (! $secret) {
            exit('Set a value for COMMAND_SECRET in the .env file');
        }

        if (! hash_equals($secret, request()->secret ?: '')) {
            exit('Invalid secret');
        }

        if (! $command || ! in_array($command, ['send-invoices', 'send-reminders', 'update-key'])) {
            exit('Invalid command: Valid options are send-invoices, send-reminders or update-key');
        }

        Artisan::call('ninja:' . $command, $options);

        return response(nl2br(Artisan::output()));
    }

    public function redirect()
    {
        return redirect((Utils::isNinja() ? NINJA_WEB_URL : ''), 301);
    }
}

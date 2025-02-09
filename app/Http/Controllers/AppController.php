<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Industry;
use App\Models\Invoice;
use App\Ninja\Mailers\Mailer;
use App\Ninja\Repositories\AccountRepository;
use App\Services\EmailService;
use Artisan;
use Cache;
use Config;
use DB;
use Event;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Request;
use Response;
use Session;

class AppController extends BaseController
{
    protected AccountRepository $accountRepo;

    protected Mailer $mailer;

    protected EmailService $emailService;

    public function __construct(AccountRepository $accountRepo, Mailer $mailer, EmailService $emailService)
    {
        //parent::__construct();

        $this->accountRepo = $accountRepo;
        $this->mailer = $mailer;
        $this->emailService = $emailService;
    }

    public function doSetup()
    {
        if (Utils::isNinjaProd()) {
            return Redirect::to('/');
        }

        $valid = false;
        $test = \Illuminate\Support\Facades\Request::input('test');

        $app = \Illuminate\Support\Facades\Request::input('app');
        $app['key'] = env('APP_KEY') ?: mb_strtolower(Str::random(RANDOM_KEY_LENGTH));
        $app['debug'] = \Illuminate\Support\Facades\Request::input('debug') ? 'true' : 'false';
        $app['https'] = \Illuminate\Support\Facades\Request::input('https') ? 'true' : 'false';

        $database = \Illuminate\Support\Facades\Request::input('database');
        $dbType = 'mysql'; // $database['default'];
        $database['connections'] = [$dbType => $database['type']];
        $mail = \Illuminate\Support\Facades\Request::input('mail');

        if ($test == 'mail') {
            return self::testMail($mail);
        }

        $valid = self::testDatabase($database);

        if ($test == 'db') {
            return $valid === true ? 'Success' : $valid;
        }

        if ( ! $valid) {
            return Redirect::to('/setup')->withInput();
        }

        if (Utils::isDatabaseSetup() && Account::count() > 0) {
            return Redirect::to('/');
        }

        $_ENV['APP_ENV'] = 'production';
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
        $_ENV['PHANTOMJS_SECRET'] = mb_strtolower(Str::random(RANDOM_KEY_LENGTH));
        $_ENV['MAILGUN_DOMAIN'] = $mail['mailgun_domain'];
        $_ENV['MAILGUN_SECRET'] = $mail['mailgun_secret'];

        $config = '';
        foreach ($_ENV as $key => $val) {
            if (is_array($val)) {
                continue;
            }

            if (preg_match('/\s/', $val)) {
                $val = sprintf("'%s'", $val);
            }

            $config .= sprintf('%s=%s%s', $key, $val, PHP_EOL);
        }

        // Write Config Settings
        $fp = fopen(base_path() . '/.env', 'w');
        fwrite($fp, $config);
        fclose($fp);

        if ( ! Utils::isDatabaseSetup()) {
            // == DB Migrate & Seed == //
            $sqlFile = base_path() . '/database/setup.sql';
            \Illuminate\Support\Facades\DB::unprepared(file_get_contents($sqlFile));
        }

        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true, '--class' => 'UpdateSeeder']);

        if ( ! Account::count()) {
            $firstName = trim(\Illuminate\Support\Facades\Request::input('first_name'));
            $lastName = trim(\Illuminate\Support\Facades\Request::input('last_name'));
            $email = trim(mb_strtolower(\Illuminate\Support\Facades\Request::input('email')));
            $password = trim(\Illuminate\Support\Facades\Request::input('password'));
            $account = $this->accountRepo->create($firstName, $lastName, $email, $password);

            $user = $account->users()->first();
            $user->acceptLatestTerms(request()->getClientIp());
            $user->save();
        }

        return Redirect::to('/login');
    }

    public function showSetup()
    {
        if (Utils::isNinjaProd() || (Utils::isDatabaseSetup() && Account::count() > 0)) {
            return Redirect::to('/');
        }

        if (file_exists(base_path() . '/.env')) {
            exit('Error: app is already configured, backup then delete the .env file to re-run the setup');
        }

        return View::make('setup');
    }

    public function updateSetup()
    {
        dd('here?');

        if (Utils::isNinjaProd()) {
            return Redirect::to('/');
        }

        dd('here?');

        if ( ! Auth::check() && Utils::isDatabaseSetup() && Account::count() > 0) {
            return Redirect::to('/');
        }

        dd('here?');

        if ( ! $canUpdateEnv = @fopen(base_path() . '/.env', 'w')) {
            \Illuminate\Support\Facades\Session::flash('error', 'Warning: Permission denied to write to .env config file, try running <code>sudo chown www-data:www-data /path/to/ninja/.env</code>');

            return Redirect::to('/settings/system_settings');
        }

        $app = \Illuminate\Support\Facades\Request::input('app');
        $db = \Illuminate\Support\Facades\Request::input('database');
        $mail = \Illuminate\Support\Facades\Request::input('mail');

        $_ENV['APP_URL'] = $app['url'];
        $_ENV['APP_DEBUG'] = \Illuminate\Support\Facades\Request::input('debug') ? 'true' : 'false';
        $_ENV['REQUIRE_HTTPS'] = \Illuminate\Support\Facades\Request::input('https') ? 'true' : 'false';

        $_ENV['DB_TYPE'] = 'mysql'; // $db['default'];
        $_ENV['DB_HOST'] = $db['type']['host'];
        $_ENV['DB_DATABASE'] = $db['type']['database'];
        $_ENV['DB_USERNAME'] = $db['type']['username'];
        $_ENV['DB_PASSWORD'] = $db['type']['password'];

        if ($mail) {
            $prefix = '';
            if (($user = auth()->user()) && Account::count() > 1) {
                $prefix = $user->account_id . '_';
            }

            $_ENV[$prefix . 'MAIL_DRIVER'] = $mail['driver'];
            $_ENV[$prefix . 'MAIL_PORT'] = $mail['port'];
            $_ENV[$prefix . 'MAIL_ENCRYPTION'] = $mail['encryption'];
            $_ENV[$prefix . 'MAIL_HOST'] = $mail['host'];
            $_ENV[$prefix . 'MAIL_USERNAME'] = $mail['username'];
            $_ENV[$prefix . 'MAIL_FROM_NAME'] = $mail['from']['name'];
            $_ENV[$prefix . 'MAIL_FROM_ADDRESS'] = $mail['from']['address'];
            $_ENV[$prefix . 'MAIL_PASSWORD'] = $mail['password'];
            $_ENV['MAILGUN_DOMAIN'] = $mail['mailgun_domain'];
            $_ENV['MAILGUN_SECRET'] = $mail['mailgun_secret'];
        }

        $config = '';
        foreach ($_ENV as $key => $val) {
            if (is_array($val)) {
                continue;
            }

            if (preg_match('/\s/', $val)) {
                $val = sprintf("'%s'", $val);
            }

            $config .= sprintf('%s=%s%s', $key, $val, PHP_EOL);
        }

        $filePath = base_path() . '/.env';
        $fp = fopen($filePath, 'w');
        fwrite($fp, $config);
        fclose($fp);

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('/settings/system_settings');
    }

    public function install()
    {
        if ( ! Utils::isNinjaProd() && ! Utils::isDatabaseSetup()) {
            try {
                set_time_limit(60 * 5); // shouldn't take this long but just in case
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                if (Industry::count() == 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (Exception $e) {
                Utils::logError($e);

                return \Illuminate\Support\Facades\Response::make($e->getMessage(), 500);
            }
        }

        return Redirect::to('/');
    }

    public function update()
    {
        //if (! Utils::isNinjaProd()) {
        /*if ($password = env('UPDATE_SECRET')) {
            if (! hash_equals($password, request('secret') ?: '')) {
                $message = 'Invalid secret: /update?secret=<value>';
                Utils::logError($message);
                echo $message;
                exit;
            }
        }*/

        /*try {
            set_time_limit(60 * 5);
            $this->checkInnoDB();

            $cacheCompiled = base_path('bootstrap/cache/compiled.php');
            if (file_exists($cacheCompiled)) { unlink ($cacheCompiled); }
            $cacheServices = base_path('bootstrap/cache/services.json');
            if (file_exists($cacheServices)) { unlink ($cacheServices); }

            Artisan::call('clear-compiled');
            Artisan::call('cache:clear');
            Artisan::call('debugbar:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Auth::logout();
            Cache::flush();
            Session::flush();
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true, '--class' => 'UpdateSeeder']);
            Event::dispatch(new UserSettingsChanged());

            // legacy fix: check cipher is in .env file
            if (! env('APP_CIPHER')) {
                $fp = fopen(base_path().'/.env', 'a');
                fwrite($fp, "\nAPP_CIPHER=AES-256-CBC");
                fclose($fp);
            }

            // show message with link to Trello board
            $message = trans('texts.see_whats_new', ['version' => NINJA_VERSION]);
            $message = link_to(RELEASES_URL, $message, ['target' => '_blank']);
            $message = sprintf('%s - %s', trans('texts.processed_updates'), $message);
            Session::flash('warning', $message);
        } catch (Exception $e) {
            Utils::logError($e);

            return Response::make($e->getMessage(), 500);
        }*/
        //}

        return Redirect::to('/?clear_cache=true');
    }

    // MySQL changed the default table type from MyISAM to InnoDB
    // We need to make sure all tables are InnoDB to prevent migration failures
    public function checkInnoDB(): void
    {
        $result = \Illuminate\Support\Facades\DB::select("SELECT engine
                    FROM information_schema.TABLES
                    WHERE TABLE_NAME='clients' AND TABLE_SCHEMA='ninja'");

        $engine = property_exists($result[0], 'engine') ? $result[0]->engine : $result[0]->ENGINE;

        if (count($result) && $engine == 'InnoDB') {
            return;
        }

        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $sql = "SET sql_mode = 'ALLOW_INVALID_DATES';\n";

        foreach ($tables as $table) {
            $fieldName = 'Tables_in_' . env('DB_DATABASE');
            $sql .= "ALTER TABLE {$table->{$fieldName}} engine=InnoDB;\n";
        }

        \Illuminate\Support\Facades\DB::unprepared($sql);
    }

    public function emailBounced(): string
    {
        $messageId = \Illuminate\Support\Facades\Request::input('MessageID');
        $error = \Illuminate\Support\Facades\Request::input('Name') . ': ' . \Illuminate\Support\Facades\Request::input('Description');

        return $this->emailService->markBounced($messageId, $error) ? RESULT_SUCCESS : RESULT_FAILURE;
    }

    public function emailOpened(): string
    {
        $messageId = \Illuminate\Support\Facades\Request::input('MessageID');

        return $this->emailService->markOpened($messageId) ? RESULT_SUCCESS : RESULT_FAILURE;

        return RESULT_SUCCESS;
    }

    public function checkData(): string
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('ninja:check-data');
            \Illuminate\Support\Facades\Artisan::call('ninja:init-lookup', ['--validate' => true]);

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

        return view('errors.list', ['errors' => $errors]);
    }

    public function stats()
    {
        if ( ! hash_equals(\Illuminate\Support\Facades\Request::input('password') ?: '', env('RESELLER_PASSWORD'))) {
            sleep(3);

            return '';
        }

        if (Utils::getResllerType() == RESELLER_REVENUE_SHARE) {
            $data = \Illuminate\Support\Facades\DB::table('accounts')
                ->leftJoin('payments', 'payments.account_id', '=', 'accounts.id')
                ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
                ->where('accounts.account_key', '=', NINJA_ACCOUNT_KEY)
                ->where('payments.is_deleted', '=', false)
                ->get([
                    'clients.public_id as client_id',
                    'payments.public_id as payment_id',
                    'payments.payment_date',
                    'payments.amount',
                ]);
        } else {
            $data = \Illuminate\Support\Facades\DB::table('users')->count();
        }

        return json_encode($data);
    }

    public function testHeadless(): void
    {
        $invoice = Invoice::scope()->orderBy('id')->first();

        if ( ! $invoice) {
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

        if ( ! $secret) {
            exit('Set a value for COMMAND_SECRET in the .env file');
        }

        if ( ! hash_equals($secret, request()->secret ?: '')) {
            exit('Invalid secret');
        }

        if ( ! $command || ! in_array($command, ['send-invoices', 'send-reminders', 'update-key'])) {
            exit('Invalid command: Valid options are send-invoices, send-reminders or update-key');
        }

        \Illuminate\Support\Facades\Artisan::call('ninja:' . $command, $options);

        return response(nl2br(\Illuminate\Support\Facades\Artisan::output()));
    }

    public function redirect()
    {
        return redirect((Utils::isNinja() ? NINJA_WEB_URL : ''), 301);
    }

    private function testDatabase(array $database): string|bool
    {
        $dbType = 'mysql'; // $database['default'];
        \Illuminate\Support\Facades\Config::set('database.default', $dbType);
        foreach ($database['connections'][$dbType] as $key => $val) {
            \Illuminate\Support\Facades\Config::set(sprintf('database.connections.%s.%s', $dbType, $key), $val);
        }

        try {
            \Illuminate\Support\Facades\DB::reconnect();
            $valid = (bool) \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

        return $valid;
    }

    private function testMail(array $mail)
    {
        $email = $mail['from']['address'];
        $fromName = $mail['from']['name'];

        foreach ($mail as $key => $val) {
            \Illuminate\Support\Facades\Config::set('mail.' . $key, $val);
        }

        \Illuminate\Support\Facades\Config::set('mail.from.address', $email);
        \Illuminate\Support\Facades\Config::set('mail.from.name', $fromName);

        $data = [
            'text'      => 'Test email',
            'fromEmail' => $email,
        ];

        try {
            $response = $this->mailer->sendTo($email, $email, $fromName, 'Test email', 'contact', $data);

            return $response === true ? 'Sent' : $response;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}

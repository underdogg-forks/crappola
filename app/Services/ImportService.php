<?php

namespace App\Services;

use App\Models\AccountGatewayToken;
use App\Models\Client;
use App\Models\Contact;
use App\Models\EntityModel;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Vendor;
use App\Ninja\Import\BaseTransformer;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\ContactRepository;
use App\Ninja\Repositories\CustomerRepository;
use App\Ninja\Repositories\ExpenseCategoryRepository;
use App\Ninja\Repositories\ExpenseRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Ninja\Repositories\ProductRepository;
use App\Ninja\Repositories\TaxRateRepository;
use App\Ninja\Repositories\VendorRepository;
use App\Ninja\Serializers\ArraySerializer;
use Auth;
use Carbon;
use Excel;
use Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Fractal\Manager;
use stdClass;
use Utils;

/**
 * Class ImportService.
 */
class ImportService
{
    public $fractal;

    public $paymentRepo;

    /**
     * @var \App\Ninja\Repositories\ExpenseRepository
     */
    public $expenseRepo;

    public $vendorRepo;

    public $expenseCategoryRepo;

    public $taxRateRepository;

    /**
     * @var array
     */
    public $results = [];

    /**
     * @var array
     */
    public static $entityTypes = [
        IMPORT_JSON,
        ENTITY_CLIENT,
        ENTITY_CONTACT,
        ENTITY_INVOICE,
        ENTITY_PAYMENT,
        ENTITY_TASK,
        ENTITY_PRODUCT,
        ENTITY_VENDOR,
        ENTITY_EXPENSE,
        ENTITY_CUSTOMER,
    ];

    /**
     * @var array
     */
    public static $sources = [
        IMPORT_CSV,
        IMPORT_JSON,
        IMPORT_FRESHBOOKS,
        IMPORT_HIVEAGE,
        IMPORT_INVOICEABLE,
        IMPORT_INVOICEPLANE,
        IMPORT_NUTCACHE,
        IMPORT_PANCAKE,
        IMPORT_RONIN,
        IMPORT_STRIPE,
        IMPORT_WAVE,
        IMPORT_ZOHO,
    ];

    /**
     * @var
     */
    protected $transformer;

    protected \App\Ninja\Repositories\InvoiceRepository $invoiceRepo;

    protected \App\Ninja\Repositories\ClientRepository $clientRepo;

    protected \App\Ninja\Repositories\CustomerRepository $customerRepo;

    protected \App\Ninja\Repositories\ContactRepository $contactRepo;

    protected \App\Ninja\Repositories\ProductRepository $productRepo;

    /**
     * @var array
     */
    protected $processedRows = [];

    private array $maps = [];

    /**
     * ImportService constructor.
     *
     * @param Manager            $manager
     * @param ClientRepository   $clientRepo
     * @param CustomerRepository $customerRepo
     * @param InvoiceRepository  $invoiceRepo
     * @param PaymentRepository  $paymentRepo
     * @param ContactRepository  $contactRepo
     * @param ProductRepository  $productRepo
     */
    public function __construct(
        Manager $manager,
        ClientRepository $clientRepo,
        CustomerRepository $customerRepo,
        InvoiceRepository $invoiceRepo,
        PaymentRepository $paymentRepo,
        ContactRepository $contactRepo,
        ProductRepository $productRepo,
        ExpenseRepository $expenseRepo,
        VendorRepository $vendorRepo,
        ExpenseCategoryRepository $expenseCategoryRepo,
        TaxRateRepository $taxRateRepository
    ) {
        $this->fractal = $manager;
        $this->fractal->setSerializer(new ArraySerializer());

        $this->clientRepo = $clientRepo;
        $this->customerRepo = $customerRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->paymentRepo = $paymentRepo;
        $this->contactRepo = $contactRepo;
        $this->productRepo = $productRepo;
        $this->expenseRepo = $expenseRepo;
        $this->vendorRepo = $vendorRepo;
        $this->expenseCategoryRepo = $expenseCategoryRepo;
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * @param $source
     * @param $entityType
     *
     * @return string
     */
    public static function getTransformerClassName(string $source, $entityType): string
    {
        return 'App\\Ninja\\Import\\' . $source . '\\' . ucwords($entityType) . 'Transformer';
    }

    /**
     * @param $source
     * @param $entityType
     * @param $maps
     *
     * @return mixed
     */
    public static function getTransformer($source, $entityType, $maps)
    {
        $className = self::getTransformerClassName($source, $entityType);

        return new $className($maps);
    }

    /**
     * @param $file
     *
     * @throws Exception
     *
     * @return array
     */
    public function importJSON($fileName, $includeData, $includeSettings)
    {
        $this->initMaps();
        $this->checkForFile($fileName);
        $file = file_get_contents($fileName);
        $json = json_decode($file, true);
        $json = $this->removeIdFields($json);

        $transformer = new BaseTransformer($this->maps);

        $this->checkClientCount(count($json['clients']));

        if ($includeSettings) {
            // remove blank id values
            $settings = [];
            foreach ($json as $field => $value) {
                if (mb_strstr($field, '_id') && ! $value) {
                    // continue;
                } else {
                    $settings[$field] = $value;
                }
            }

            $account = \Illuminate\Support\Facades\Auth::user()->account;
            $account->fill($settings);
            $account->save();

            $emailSettings = $account->account_email_settings;
            $emailSettings->fill($settings['account_email_settings'] ?? $settings);
            $emailSettings->save();
        }

        if ($includeData) {
            foreach ($json['products'] as $jsonProduct) {
                if ($transformer->hasProduct($jsonProduct['product_key'])) {
                    continue;
                }

                $productValidate = EntityModel::validate($jsonProduct, ENTITY_PRODUCT);
                if ($productValidate === true) {
                    $product = $this->productRepo->save($jsonProduct);
                    $this->addProductToMaps($product);
                    $this->addSuccess($product);
                } else {
                    $jsonProduct['type'] = ENTITY_PRODUCT;
                    $jsonProduct['error'] = $productValidate;
                    $this->addFailure(ENTITY_PRODUCT, $jsonProduct);
                    continue;
                }
            }

            foreach ($json['clients'] as $jsonClient) {
                $clientValidate = EntityModel::validate($jsonClient, ENTITY_CLIENT);
                if ($clientValidate === true) {
                    $client = $this->clientRepo->save($jsonClient);
                    $this->addClientToMaps($client);
                    $this->addSuccess($client);
                } else {
                    $jsonClient['type'] = ENTITY_CLIENT;
                    $jsonClient['error'] = $clientValidate;
                    $this->addFailure(ENTITY_CLIENT, $jsonClient);
                    continue;
                }

                foreach ($jsonClient['invoices'] as $jsonInvoice) {
                    $jsonInvoice['client_id'] = $client->id;
                    $invoiceValidate = EntityModel::validate($jsonInvoice, ENTITY_INVOICE);
                    if ($invoiceValidate === true) {
                        $invoice = $this->invoiceRepo->save($jsonInvoice);
                        $this->addInvoiceToMaps($invoice);
                        $this->addSuccess($invoice);
                    } else {
                        $jsonInvoice['type'] = ENTITY_INVOICE;
                        $jsonInvoice['error'] = $invoiceValidate;
                        $this->addFailure(ENTITY_INVOICE, $jsonInvoice);
                        continue;
                    }

                    foreach ($jsonInvoice['payments'] as $jsonPayment) {
                        $jsonPayment['invoice_id'] = $invoice->public_id;
                        $paymentValidate = EntityModel::validate($jsonPayment, ENTITY_PAYMENT);
                        if ($paymentValidate === true) {
                            $jsonPayment['client_id'] = $client->id;
                            $jsonPayment['invoice_id'] = $invoice->id;
                            $payment = $this->paymentRepo->save($jsonPayment);
                            $this->addSuccess($payment);
                        } else {
                            $jsonPayment['type'] = ENTITY_PAYMENT;
                            $jsonPayment['error'] = $paymentValidate;
                            $this->addFailure(ENTITY_PAYMENT, $jsonPayment);
                            continue;
                        }
                    }
                }
            }
        }

        \Illuminate\Support\Facades\File::delete($fileName);

        return $this->results;
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    public function removeIdFields(array $array): array
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = $this->removeIdFields($val);
            } elseif ($key === 'id') {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * @param $source
     * @param $files
     *
     * @return array
     */
    public function importFiles($source, $files): array
    {
        $results = [];
        $imported_files = null;
        $this->initMaps();

        foreach ($files as $entityType => $file) {
            $results[$entityType] = $this->execute($source, $entityType, $file);
        }

        return $results;
    }

    /**
     * @param array $files
     *
     * @throws Exception
     *
     * @return array
     */
    public function mapCSV(array $files): array
    {
        $data = [];

        foreach ($files as $entityType => $filename) {
            $class = 'App\\Models\\' . ucwords($entityType);
            $columns = $class::getImportColumns();
            $map = $class::getImportMap();

            // Lookup field translations
            foreach ($columns as $key => $value) {
                unset($columns[$key]);
                $label = $value;
                // disambiguate some of the labels
                if ($entityType == ENTITY_INVOICE) {
                    if ($label == 'name') {
                        $label = 'client_name';
                    } elseif ($label == 'notes') {
                        $label = 'product_notes';
                    } elseif ($label == 'terms') {
                        $label = 'invoice_terms';
                    }
                }

                $columns[$value] = trans('texts.' . $label);
            }

            array_unshift($columns, ' ');

            $data[$entityType] = $this->mapFile($entityType, $filename, $columns, $map);

            if ($entityType === ENTITY_CLIENT && count($data[$entityType]['data']) + Client::scope()->count() > \Illuminate\Support\Facades\Auth::user()->getMaxNumClients()) {
                throw new Exception(trans('texts.limit_clients', ['count' => \Illuminate\Support\Facades\Auth::user()->getMaxNumClients()]));
            }
        }

        return $data;
    }

    /**
     * @param $entityType
     * @param $filename
     * @param $columns
     * @param $map
     *
     * @return array
     */
    public function mapFile($entityType, $filename, $columns, $map)
    {
        $data = $this->getCsvData($filename);
        $headers = false;
        $hasHeaders = false;
        $mapped = [];

        if ($data !== []) {
            $headers = $data[0];
            foreach ($headers as $title) {
                if (mb_strpos(mb_strtolower($title), 'name') > 0) {
                    $hasHeaders = true;
                    break;
                }
            }

            $counter = count($headers);

            for ($i = 0; $i < $counter; $i++) {
                $title = mb_strtolower($headers[$i]);
                $mapped[$i] = '';

                foreach ($map as $search => $column) {
                    if ($this->checkForMatch($title, $search)) {
                        $hasHeaders = true;
                        $mapped[$i] = $column;
                        break;
                    }
                }
            }
        }

        $data = [
            'entityType' => $entityType,
            'data'       => $data,
            'headers'    => $headers,
            'hasHeaders' => $hasHeaders,
            'columns'    => $columns,
            'mapped'     => $mapped,
            'warning'    => false,
        ];

        // check that dates are valid
        if (count($data['data']) > 1) {
            $row = $data['data'][1];
            foreach ($mapped as $index => $field) {
                if ( ! mb_strstr($field, 'date')) {
                    continue;
                }

                try {
                    $date = new Carbon($row[$index]);
                } catch (Exception) {
                    $data['warning'] = 'invalid_date';
                }
            }
        }

        return $data;
    }

    /**
     * @param array $maps
     * @param       $headers
     *
     * @return array
     */
    public function importCSV(array $maps, array $headers, $timestamp): array
    {
        $results = [];

        foreach ($maps as $entityType => $map) {
            $results[$entityType] = $this->executeCSV($entityType, $map, $headers[$entityType], $timestamp);
        }

        return $results;
    }

    public function presentResults($results, $includeSettings = false): string
    {
        $message = '';
        $skipped = [];

        if ($includeSettings) {
            $message = trans('texts.imported_settings') . '<br/>';
        }

        foreach ($results as $entityType => $entityResults) {
            if (($count = count($entityResults[RESULT_SUCCESS])) !== 0) {
                $message .= trans(sprintf('texts.created_%ss', $entityType), ['count' => $count]) . '<br/>';
            }

            if (count($entityResults[RESULT_FAILURE]) > 0) {
                $skipped = array_merge($skipped, $entityResults[RESULT_FAILURE]);
            }
        }

        if ($skipped !== []) {
            $message .= '<p/>' . trans('texts.failed_to_import') . '<br/>';
            foreach ($skipped as $skip) {
                $message .= json_encode($skip) . '<br/>';
            }
        }

        return $message;
    }

    /**
     * @param $source
     * @param $entityType
     * @param $file
     *
     * @return array
     */
    private function execute($source, $entityType, $fileName): array
    {
        $results = [
            RESULT_SUCCESS => [],
            RESULT_FAILURE => [],
        ];

        // Convert the data
        $row_list = [];
        $this->checkForFile($fileName);

        Excel::load($fileName, function ($reader) use ($source, $entityType, &$row_list, &$results): void {
            $this->checkData($entityType, count($reader->all()));

            $reader->each(function ($row) use ($source, $entityType, &$row_list, &$results): void {
                if ($this->isRowEmpty($row)) {
                    return;
                }

                $data_index = $this->transformRow($source, $entityType, $row);

                if ($data_index !== false) {
                    if ($data_index !== true) {
                        // Wasn't merged with another row
                        $row_list[] = ['row' => $row, 'data_index' => $data_index];
                    }
                } else {
                    $results[RESULT_FAILURE][] = $row;
                }
            });
        });

        // Save the data
        foreach ($row_list as $row_data) {
            $result = $this->saveData($source, $entityType, $row_data['row'], $row_data['data_index']);
            if ($result) {
                $results[RESULT_SUCCESS][] = $result;
            } else {
                $results[RESULT_FAILURE][] = $row_data['row'];
            }
        }

        \Illuminate\Support\Facades\File::delete($fileName);

        return $results;
    }

    /**
     * @param $source
     * @param $entityType
     * @param $row
     *
     * @return bool|mixed
     */
    private function transformRow($source, $entityType, $row): bool|int|string|null
    {
        $transformer = static::getTransformer($source, $entityType, $this->maps);
        $resource = $transformer->transform($row);

        if ( ! $resource) {
            return false;
        }

        $data = $this->fractal->createData($resource)->toArray();

        // Create expesnse category
        if ($entityType == ENTITY_EXPENSE) {
            if ( ! empty($row->expense_category)) {
                $categoryId = $transformer->getExpenseCategoryId($row->expense_category);
                if ( ! $categoryId) {
                    $category = $this->expenseCategoryRepo->save(['name' => $row->expense_category]);
                    $this->addExpenseCategoryToMaps($category);
                    $data['expense_category_id'] = $category->id;
                }
            }

            if ( ! empty($row->vendor) && ($vendorName = trim($row->vendor)) && ! $transformer->getVendorId($vendorName)) {
                $vendor = $this->vendorRepo->save(['name' => $vendorName, 'vendor_contact' => []]);
                $this->addVendorToMaps($vendor);
                $data['vendor_id'] = $vendor->id;
            }
        }

        /*
        // if the invoice number is blank we'll assign it
        if ($entityType == ENTITY_INVOICE && ! $data['invoice_number']) {
            $account = Auth::user()->account;
            $invoice = Invoice::createNew();
            $data['invoice_number'] = $account->getNextNumber($invoice);
        }
        */

        if (EntityModel::validate($data, $entityType) !== true) {
            return false;
        }

        if ($entityType == ENTITY_INVOICE) {
            if (empty($this->processedRows[$data['invoice_number']])) {
                $this->processedRows[$data['invoice_number']] = $data;
            } else {
                // Merge invoice items
                $this->processedRows[$data['invoice_number']]['invoice_items'] = array_merge($this->processedRows[$data['invoice_number']]['invoice_items'], $data['invoice_items']);

                return true;
            }
        } else {
            $this->processedRows[] = $data;
        }

        return array_key_last($this->processedRows);
    }

    /**
     * @param $source
     * @param $entityType
     * @param $row
     * @param $data_index
     *
     * @return mixed
     */
    private function saveData($source, $entityType, $row, $data_index)
    {
        $data = $this->processedRows[$data_index];

        if ($entityType == ENTITY_INVOICE) {
            $data['is_public'] = true;
        }

        $entity = $this->{$entityType . 'Repo'}->save($data);

        // update the entity maps
        if ($entityType != ENTITY_CUSTOMER) {
            $mapFunction = 'add' . ucwords($entity->getEntityType()) . 'ToMaps';
            if (method_exists($this, $mapFunction)) {
                $this->{$mapFunction}($entity);
            }
        }

        // if the invoice is paid we'll also create a payment record
        if ($entityType === ENTITY_INVOICE && isset($data['paid']) && $data['paid'] > 0) {
            $this->createPayment($source, $row, $data['client_id'], $entity->id, $entity->public_id);
        }

        return $entity;
    }

    /**
     * @param $entityType
     * @param $count
     *
     * @throws Exception
     */
    private function checkData($entityType, int $count): void
    {
        if (Utils::isNinja() && $count > MAX_IMPORT_ROWS) {
            throw new Exception(trans('texts.limit_import_rows', ['count' => MAX_IMPORT_ROWS]));
        }

        if ($entityType === ENTITY_CLIENT) {
            $this->checkClientCount($count);
        }
    }

    /**
     * @param $count
     *
     * @throws Exception
     */
    private function checkClientCount(int $count): void
    {
        $totalClients = $count + Client::scope()->withTrashed()->count();
        if ($totalClients > \Illuminate\Support\Facades\Auth::user()->getMaxNumClients()) {
            throw new Exception(trans('texts.limit_clients', ['count' => \Illuminate\Support\Facades\Auth::user()->getMaxNumClients()]));
        }
    }

    /**
     * @param $source
     * @param $data
     * @param $clientId
     * @param $invoiceId
     */
    private function createPayment($source, $row, $clientId, $invoiceId, $invoicePublicId): void
    {
        $paymentTransformer = static::getTransformer($source, ENTITY_PAYMENT, $this->maps);

        $row->client_id = $clientId;
        $row->invoice_id = $invoiceId;

        if ($resource = $paymentTransformer->transform($row)) {
            $data = $this->fractal->createData($resource)->toArray();
            $data['invoice_id'] = $invoicePublicId;
            if (Payment::validate($data) === true) {
                $data['invoice_id'] = $invoiceId;
                $this->paymentRepo->save($data);
            }
        }
    }

    private function getCsvData($fileName): array
    {
        $this->checkForFile($fileName);

        if ( ! ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Reader::createFromPath($fileName, 'r');
        //$csv->setHeaderOffset(0); //set the CSV header offset
        $stmt = new Statement();
        $data = iterator_to_array($stmt->process($csv));

        if ($data !== []) {
            $headers = $data[0];

            // Remove Invoice Ninja headers
            if (count($headers) && count($data) > 4) {
                $firstCell = $headers[0];
                if (mb_strstr($firstCell, APP_NAME)) {
                    array_shift($data); // Invoice Ninja...
                    array_shift($data); // <blank line>
                    array_shift($data); // Enitty Type Header
                }
            }
        }

        return $data;
    }

    /**
     * @param $column
     * @param $pattern
     *
     * @return bool
     */
    private function checkForMatch($column, $pattern): bool
    {
        if (str_starts_with($column, 'sec')) {
            return false;
        }

        if (mb_strpos($pattern, '^')) {
            [$include, $exclude] = explode('^', $pattern);
            $includes = explode('|', $include);
            $excludes = explode('|', $exclude);
        } else {
            $includes = explode('|', $pattern);
            $excludes = [];
        }

        foreach ($includes as $string) {
            if (str_contains($column, $string)) {
                $excluded = false;
                foreach ($excludes as $exclude) {
                    if (str_contains($column, $exclude)) {
                        $excluded = true;
                        break;
                    }
                }

                if ( ! $excluded) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $entityType
     * @param $map
     * @param $hasHeaders
     *
     * @return array
     */
    private function executeCSV(int|string $entityType, $map, $hasHeaders, $timestamp): array
    {
        $results = [
            RESULT_SUCCESS => [],
            RESULT_FAILURE => [],
        ];
        $source = IMPORT_CSV;

        $path = env('FILE_IMPORT_PATH') ?: storage_path() . '/import';
        $fileName = sprintf('%s/%s_%s_%s.csv', $path, \Illuminate\Support\Facades\Auth::user()->account_id, $timestamp, $entityType);
        $data = $this->getCsvData($fileName);
        $this->checkData($entityType, count($data));
        $this->initMaps();

        // Convert the data
        $row_list = [];
        foreach ($data as $row) {
            if ($hasHeaders) {
                $hasHeaders = false;
                continue;
            }

            $row = $this->convertToObject($entityType, $row, $map);
            if ($this->isRowEmpty($row)) {
                continue;
            }

            $data_index = $this->transformRow($source, $entityType, $row);

            if ($data_index !== false) {
                if ($data_index !== true) {
                    // Wasn't merged with another row
                    $row_list[] = ['row' => $row, 'data_index' => $data_index];
                }
            } else {
                $results[RESULT_FAILURE][] = $row;
            }
        }

        // Save the data
        foreach ($row_list as $row_data) {
            $result = $this->saveData($source, $entityType, $row_data['row'], $row_data['data_index']);

            if ($result) {
                $results[RESULT_SUCCESS][] = $result;
            } else {
                $results[RESULT_FAILURE][] = $row;
            }
        }

        \Illuminate\Support\Facades\File::delete($fileName);

        return $results;
    }

    /**
     * @param $entityType
     * @param $data
     * @param $map
     *
     * @return stdClass
     */
    private function convertToObject(int|string $entityType, $data, $map): stdClass
    {
        $obj = new stdClass();
        $class = 'App\\Models\\' . ucwords($entityType);
        $columns = $class::getImportColumns();

        foreach ($columns as $column) {
            $obj->{$column} = false;
        }

        foreach ($map as $index => $field) {
            if ( ! $field) {
                continue;
            }

            if (isset($obj->{$field}) && $obj->{$field}) {
                continue;
            }

            if (isset($data[$index])) {
                $obj->{$field} = $data[$index];
            }
        }

        return $obj;
    }

    /**
     * @param $entity
     */
    private function addSuccess($entity): void
    {
        $this->results[$entity->getEntityType()][RESULT_SUCCESS][] = $entity;
    }

    /**
     * @param $entityType
     * @param $data
     */
    private function addFailure(string $entityType, $data): void
    {
        $this->results[$entityType][RESULT_FAILURE][] = $data;
    }

    private function init(): void
    {
        EntityModel::$notifySubscriptions = false;

        foreach ([ENTITY_CLIENT, ENTITY_INVOICE, ENTITY_PAYMENT, ENTITY_QUOTE, ENTITY_PRODUCT] as $entityType) {
            $this->results[$entityType] = [
                RESULT_SUCCESS => [],
                RESULT_FAILURE => [],
            ];
        }
    }

    private function initMaps(): void
    {
        $this->init();

        $this->maps = [
            'client'             => [],
            'contact'            => [],
            'customer'           => [],
            'invoice'            => [],
            'invoice_client'     => [],
            'product'            => [],
            'countries'          => [],
            'countries2'         => [],
            'currencies'         => [],
            'client_ids'         => [],
            'invoice_ids'        => [],
            'vendors'            => [],
            'expense_categories' => [],
            'tax_rates'          => [],
            'tax_names'          => [],
        ];

        $clients = $this->clientRepo->all();
        foreach ($clients as $client) {
            $this->addClientToMaps($client);
        }

        $customers = $this->customerRepo->all();
        foreach ($customers as $customer) {
            $this->addCustomerToMaps($customer);
        }

        $contacts = $this->contactRepo->all();
        foreach ($contacts as $contact) {
            $this->addContactToMaps($contact);
        }

        $invoices = $this->invoiceRepo->all();
        foreach ($invoices as $invoice) {
            $this->addInvoiceToMaps($invoice);
        }

        $products = $this->productRepo->all();
        foreach ($products as $product) {
            $this->addProductToMaps($product);
        }

        $countries = \Illuminate\Support\Facades\Cache::get('countries');
        foreach ($countries as $country) {
            $this->maps['countries'][mb_strtolower($country->name)] = $country->id;
            $this->maps['countries2'][mb_strtolower($country->iso_3166_2)] = $country->id;
        }

        $currencies = \Illuminate\Support\Facades\Cache::get('currencies');
        foreach ($currencies as $currency) {
            $this->maps['currencies'][mb_strtolower($currency->code)] = $currency->id;
        }

        $vendors = $this->vendorRepo->all();
        foreach ($vendors as $vendor) {
            $this->addVendorToMaps($vendor);
        }

        $expenseCaegories = $this->expenseCategoryRepo->all();
        foreach ($expenseCaegories as $category) {
            $this->addExpenseCategoryToMaps($category);
        }

        $taxRates = $this->taxRateRepository->all();
        foreach ($taxRates as $taxRate) {
            $name = trim(mb_strtolower($taxRate->name));
            $this->maps['tax_rates'][$name] = $taxRate->rate;
            $this->maps['tax_names'][$name] = $taxRate->name;
        }
    }

    /**
     * @param Invoice $invoice
     */
    private function addInvoiceToMaps(Invoice $invoice): void
    {
        if (($number = mb_strtolower(trim($invoice->invoice_number))) !== '' && ($number = mb_strtolower(trim($invoice->invoice_number))) !== '0') {
            $this->maps['invoices'][$number] = $invoice;
            $this->maps['invoice'][$number] = $invoice->id;
            $this->maps['invoice_client'][$number] = $invoice->client_id;
            $this->maps['invoice_ids'][$invoice->public_id] = $invoice->id;
        }
    }

    /**
     * @param Client $client
     */
    private function addClientToMaps(Client $client): void
    {
        if (($name = mb_strtolower(trim($client->name))) !== '' && ($name = mb_strtolower(trim($client->name))) !== '0') {
            $this->maps['client'][$name] = $client->id;
            $this->maps['client_ids'][$client->public_id] = $client->id;
        }

        if ($client->contacts->count()) {
            $contact = $client->contacts[0];
            if (($email = mb_strtolower(trim($contact->email))) !== '' && ($email = mb_strtolower(trim($contact->email))) !== '0') {
                $this->maps['client'][$email] = $client->id;
            }

            if (($name = mb_strtolower(trim($contact->getFullName()))) !== '' && ($name = mb_strtolower(trim($contact->getFullName()))) !== '0') {
                $this->maps['client'][$name] = $client->id;
            }

            $this->maps['client_ids'][$client->public_id] = $client->id;
        }
    }

    /**
     * @param Customer $customer
     */
    private function addCustomerToMaps(AccountGatewayToken $customer): void
    {
        $this->maps['customer'][$customer->token] = $customer;
        $this->maps['customer'][$customer->contact->email] = $customer;
    }

    /**
     * @param Product $product
     */
    private function addContactToMaps(Contact $contact): void
    {
        if (($key = mb_strtolower(trim($contact->email))) !== '' && ($key = mb_strtolower(trim($contact->email))) !== '0') {
            $this->maps['contact'][$key] = $contact;
        }
    }

    /**
     * @param Product $product
     */
    private function addProductToMaps(Product $product): void
    {
        if (($key = mb_strtolower(trim($product->product_key))) !== '' && ($key = mb_strtolower(trim($product->product_key))) !== '0') {
            $this->maps['product'][$key] = $product;
        }
    }

    private function addExpenseToMaps(Expense $expense): void
    {
        // do nothing
    }

    private function addVendorToMaps(Vendor $vendor): void
    {
        $this->maps['vendor'][mb_strtolower($vendor->name)] = $vendor->id;
    }

    private function addExpenseCategoryToMaps(ExpenseCategory $category): void
    {
        if (($name = mb_strtolower($category->name)) !== '' && ($name = mb_strtolower($category->name)) !== '0') {
            $this->maps['expense_category'][$name] = $category->id;
        }
    }

    private function isRowEmpty($row)
    {
        $isEmpty = true;

        foreach ($row as $key => $val) {
            if (trim($val) !== '' && trim($val) !== '0') {
                $isEmpty = false;
            }
        }

        return $isEmpty;
    }

    private function checkForFile(string $fileName): bool
    {
        $counter = 0;

        while ( ! file_exists($fileName)) {
            $counter++;
            if ($counter > 60) {
                throw new Exception('File not found: ' . $fileName);
            }

            sleep(2);
        }

        return true;
    }
}

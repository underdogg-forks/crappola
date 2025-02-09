<?php

namespace App\Ninja\Import;

use App\Libraries\Utils;
use Carbon;
use Exception;
use League\Fractal\TransformerAbstract;

/**
 * Class BaseTransformer.
 */
class BaseTransformer extends TransformerAbstract
{
    protected $maps;

    /**
     * BaseTransformer constructor.
     *
     * @param $maps
     */
    public function __construct($maps)
    {
        $this->maps = $maps;
    }

    /**
     * @param $name
     */
    public function hasClient($name): bool
    {
        $name = trim(mb_strtolower($name));

        return isset($this->maps[ENTITY_CLIENT][$name]);
    }

    /**
     * @param $name
     */
    public function hasVendor($name): bool
    {
        $name = trim(mb_strtolower($name));

        return isset($this->maps[ENTITY_VENDOR][$name]);
    }

    /**
     * @param $key
     */
    public function hasProduct($key): bool
    {
        $key = trim(mb_strtolower($key));

        return isset($this->maps[ENTITY_PRODUCT][$key]);
    }

    /**
     * @param $data
     * @param $field
     *
     * @return string
     */
    public function getString($data, $field)
    {
        return (isset($data->{$field}) && $data->{$field}) ? $data->{$field} : '';
    }

    /**
     * @param $data
     * @param $field
     *
     * @return int
     */
    public function getNumber($data, $field)
    {
        return (isset($data->{$field}) && $data->{$field}) ? $data->{$field} : 0;
    }

    /**
     * @param $data
     * @param $field
     *
     * @return float
     */
    public function getFloat($data, $field)
    {
        return (isset($data->{$field}) && $data->{$field}) ? Utils::parseFloat($data->{$field}) : 0;
    }

    /**
     * @param $name
     */
    public function getClientId($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps[ENTITY_CLIENT][$name] ?? null;
    }

    /**
     * @param $name
     */
    public function getProduct($data, $key, $field, $default = false)
    {
        $productKey = trim(mb_strtolower($data->{$key}));

        if ( ! isset($this->maps['product'][$productKey])) {
            return $default;
        }

        $product = $this->maps['product'][$productKey];

        return $product->{$field} ?: $default;
    }

    /**
     * @param $name
     */
    public function getContact($email)
    {
        $email = trim(mb_strtolower($email));

        if ( ! isset($this->maps['contact'][$email])) {
            return false;
        }

        return $this->maps['contact'][$email];
    }

    /**
     * @param $name
     */
    public function getCustomer($key)
    {
        $key = trim($key);

        if ( ! isset($this->maps['customer'][$key])) {
            return false;
        }

        return $this->maps['customer'][$key];
    }

    /**
     * @param $name
     */
    public function getCountryId($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps['countries'][$name] ?? null;
    }

    /**
     * @param $name
     */
    public function getCountryIdBy2($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps['countries2'][$name] ?? null;
    }

    /**
     * @param $name
     */
    public function getTaxRate($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps['tax_rates'][$name] ?? 0;
    }

    /**
     * @param $name
     */
    public function getTaxName($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps['tax_names'][$name] ?? '';
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getFirstName($name)
    {
        $name = Utils::splitName($name);

        return $name[0];
    }

    /**
     * @param        $date
     * @param string $format
     * @param mixed  $data
     * @param mixed  $field
     */
    public function getDate($data, $field)
    {
        if ($date = data_get($data, $field)) {
            try {
                $date = new Carbon($date);
            } catch (Exception) {
                // if we fail to parse return blank
                $date = false;
            }
        }

        return $date ? $date->format('Y-m-d') : null;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getLastName($name)
    {
        $name = Utils::splitName($name);

        return $name[1];
    }

    /**
     * @param $number
     *
     * @return string
     */
    public function getInvoiceNumber($number): ?string
    {
        return $number ? mb_str_pad(trim($number), 4, '0', STR_PAD_LEFT) : null;
    }

    /**
     * @param $invoiceNumber
     */
    public function getInvoiceId($invoiceNumber)
    {
        $invoiceNumber = $this->getInvoiceNumber($invoiceNumber);
        $invoiceNumber = mb_strtolower($invoiceNumber);

        return $this->maps[ENTITY_INVOICE][$invoiceNumber] ?? null;
    }

    /**
     * @param $invoiceNumber
     */
    public function getInvoicePublicId($invoiceNumber)
    {
        $invoiceNumber = $this->getInvoiceNumber($invoiceNumber);
        $invoiceNumber = mb_strtolower($invoiceNumber);

        return isset($this->maps['invoices'][$invoiceNumber]) ? $this->maps['invoices'][$invoiceNumber]->public_id : null;
    }

    /**
     * @param $invoiceNumber
     */
    public function hasInvoice($invoiceNumber): bool
    {
        $invoiceNumber = $this->getInvoiceNumber($invoiceNumber);
        $invoiceNumber = mb_strtolower($invoiceNumber);

        return isset($this->maps[ENTITY_INVOICE][$invoiceNumber]);
    }

    /**
     * @param $invoiceNumber
     */
    public function getInvoiceClientId($invoiceNumber)
    {
        $invoiceNumber = $this->getInvoiceNumber($invoiceNumber);
        $invoiceNumber = mb_strtolower($invoiceNumber);

        return $this->maps[ENTITY_INVOICE . '_' . ENTITY_CLIENT][$invoiceNumber] ?? null;
    }

    /**
     * @param $name
     */
    public function getVendorId($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps[ENTITY_VENDOR][$name] ?? null;
    }

    /**
     * @param $name
     */
    public function getExpenseCategoryId($name)
    {
        $name = mb_strtolower(trim($name));

        return $this->maps[ENTITY_EXPENSE_CATEGORY][$name] ?? null;
    }
}

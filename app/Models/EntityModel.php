<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

/**
 * Class EntityModel.
 */
class EntityModel extends Model
{
    /**
     * @var bool
     */
    public static $notifySubscriptions = true;

    /**
     * @var array
     */
    public static $statuses = [
        STATUS_ACTIVE,
        STATUS_ARCHIVED,
        STATUS_DELETED,
    ];

    /**
     * @var bool
     */
    protected static $hasPublicId = true;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * @return mixed
     */
    public static function createNew($context = null)
    {
        $className = get_called_class();
        $entity = new $className();

        if ($context) {
            $user = $context instanceof User ? $context : $context->user;
            $company = $context->company;
        } elseif (Auth::check()) {
            $user = Auth::user();
            $company = Auth::user()->company;
        } else {
            Utils::fatalError();
        }

        $entity->user_id = $user->id;
        $entity->company_id = $company->id;

        // store references to the original user/company to prevent needing to reload them
        $entity->setRelation('user', $user);
        $entity->setRelation('company', $company);

        /*if (static::$hasPublicId) {
            $entity->public_id = static::getNextPublicId($entity->company_id);
        }*/

        return $entity;
    }

    private static function getNextPublicId($companyId)
    {
        /*$className = get_called_class();

        if (method_exists($className, 'trashed')) {
            $lastEntity = $className::whereCompanyPlanId($companyId)->withTrashed();
        } else {
            $lastEntity = $className::whereCompanyPlanId($companyId);
        }

        $lastEntity = $lastEntity->orderBy('public_id', 'DESC')->first();

        if ($lastEntity) {
            return $lastEntity->public_id + 1;
        }*/

        return 1;
    }

    /**
     * @return mixed
     */
    public static function getPrivateId($publicId)
    {
        if (! $publicId) {
            return;
        }

        $className = get_called_class();

        if (method_exists($className, 'trashed')) {
            return $className::scope($publicId)->withTrashed()->value('id');
        }

        return $className::scope($publicId)->value('id');
    }

    public static function getPortalPrivateId($publicId, $companyId)
    {
        if (! $publicId) {
            return;
        }

        $className = get_called_class();

        if (method_exists($className, 'trashed')) {
            return $className::scope($publicId, $companyId)->withTrashed()->value('id');
        }

        return $className::scope($publicId, $companyId)->value('id');
    }

    /**
     * @return string
     */
    public static function getTransformerName($entityType)
    {
        /* if (! Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Transformers\\{$module->getName()}Transformer";
            }
        } */

        return 'App\\Ninja\\Transformers\\' . ucwords(Utils::toCamelCase($entityType)) . 'Transformer';
    }

    /**
     * @param mixed $entity
     *                      TODO Remove $entityType parameter
     *
     * @return bool|string
     */
    public static function validate($data, $entityType = false, $entity = false)
    {
        if (! $entityType) {
            $className = get_called_class();
            $entityBlank = new $className();
            $entityType = $entityBlank->getEntityType();
        }

        // Use the API request if it exists
        $action = $entity ? 'update' : 'create';
        $requestClass = sprintf('App\\Http\\Requests\\%s%sAPIRequest', ucwords($action), Str::studly($entityType));
        if (! class_exists($requestClass)) {
            $requestClass = sprintf('App\\Http\\Requests\\%s%sRequest', ucwords($action), Str::studly($entityType));
        }

        $request = new $requestClass();
        $request->setUserResolver(function (): ?Authenticatable {
            return Auth::user();
        });
        $request->setEntity($entity);
        $request->replace($data);

        if (! $request->authorize()) {
            return trans('texts.not_allowed');
        }

        $validator = Validator::make($data, $request->rules());

        if ($validator->fails()) {
            return $validator->messages()->first();
        }

        return true;
    }

    public static function getIcon($entityType)
    {
        $icons = [
            'dashboard'          => 'tachometer',
            'clients'            => 'users',
            'products'           => 'cube',
            'invoices'           => 'file-pdf-o',
            'payments'           => 'credit-card',
            'recurring_invoices' => 'files-o',
            'recurring_quotes'   => 'files-o',
            'recurring_expenses' => 'files-o',
            'credits'            => 'credit-card',
            'quotes'             => 'file-text-o',
            'proposals'          => 'th-large',
            'tasks'              => 'clock-o',
            'expenses'           => 'file-image-o',
            'vendors'            => 'building',
            'settings'           => 'cog',
            'self-update'        => 'download',
            'reports'            => 'th-list',
            'projects'           => 'briefcase',
            'tickets'            => 'life-ring',
        ];

        return array_get($icons, $entityType);
    }

    public static function getFormUrl($entityType)
    {
        if (in_array($entityType, [ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_TEMPLATE])) {
            return str_replace('_', 's/', Utils::pluralizeEntityType($entityType));
        }

        return Utils::pluralizeEntityType($entityType);
    }

    /*
    public function getEntityType()
    {
        return '';
    }

    public function getNmae()
    {
        return '';
    }
    */

    public static function getStatesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStates($entityType);
    }

    /**
     * @return string
     */
    public static function getClassName($entityType)
    {
        /* if (! Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Models\\{$module->getName()}";
            }
        } */

        if ($entityType == ENTITY_QUOTE || $entityType == ENTITY_RECURRING_INVOICE || $entityType == ENTITY_RECURRING_QUOTE) {
            $entityType = ENTITY_INVOICE;
        }

        return 'App\\Models\\' . ucwords(Utils::toCamelCase($entityType));
    }

    public static function getStates($entityType = false)
    {
        $data = [];

        foreach (static::$statuses as $status) {
            $data[$status] = trans("texts.{$status}");
        }

        return $data;
    }

    public static function getStatusesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStatuses($entityType);
    }

    public static function getStatuses($entityType = false)
    {
        return [];
    }

    /**
     * @return string
     */
    public function getActivityKey()
    {
        return '[' . $this->getEntityType() . ':' . $this->public_id . ':' . $this->getDisplayName() . ']';
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->public_id;
    }

    // converts "App\Models\Client" to "client_id"

    public function entityKey()
    {
        return $this->public_id . ':' . $this->getEntityType();
    }

    public function subEntityType()
    {
        return $this->getEntityType();
    }

    public function isEntityType($type)
    {
        return $this->getEntityType() === $type;
    }

    /**
     * @param bool $publicId
     * @param bool $companyId
     *
     * @return mixed
     */
    public function scopeScope($query, $publicId = false, $companyId = false)
    {
        // If 'false' is passed as the publicId return nothing rather than everything
        if (func_num_args() > 1 && ! $publicId && ! $companyId) {
            $query->where('id', '=', 0);

            return $query;
        }

        if (! $companyId) {
            $companyId = Auth::user()->company_id;
        }

        $query->where($this->getTable() . '.company_id', '=', $companyId);

        if ($publicId) {
            if (is_array($publicId)) {
                $query->whereIn('public_id', $publicId);
            } else {
                $query->wherePublicId($publicId);
            }
        }

        if (Auth::check() && method_exists($this, 'getEntityType')
            && ! Auth::user()->hasPermission('view_' . $this->getEntityType())
            && $this->getEntityType() != ENTITY_TAX_RATE
            && $this->getEntityType() != ENTITY_DOCUMENT
            && $this->getEntityType() != ENTITY_INVITATION) {
            $query->where(Utils::pluralizeEntityType($this->getEntityType()) . '.user_id', '=', Auth::user()->id);
        }

        return $query;
    }

    // isDirty return true if the field's new value is the same as the old one

    public function scopeWithActiveOrSelected($query, $id = false)
    {
        return $query->withTrashed()
            ->where(function ($query) use ($id): void {
                $query->whereNull('deleted_at')
                    ->orWhere('id', '=', $id);
            });
    }

    /**
     * @return mixed
     */
    public function scopeWithArchived($query)
    {
        return $query->withTrashed()->where('is_deleted', '=', false);
    }

    public function setNullValues(): void
    {
        foreach ($this->fillable as $field) {
            if (! strstr($field, '_id')) {
                continue;
            }
            if ($this->$field) {
                continue;
            }
            $this->$field = null;
        }
    }

    /**
     * @return string
     */
    public function getKeyField()
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $name = $parts[count($parts) - 1];

        return strtolower($name) . '_id';
    }

    public function loadFromRequest(): void
    {
        foreach (static::$requestFields as $field) {
            if ($value = request()->$field) {
                $this->$field = strpos($field, 'date') ? Utils::fromSqlDate($value) : $value;
            }
        }
    }

    public function isChanged()
    {
        foreach ($this->fillable as $field) {
            if ($this->$field != $this->getOriginal($field)) {
                return true;
            }
        }

        return false;
    }

    public function statusClass()
    {
        return '';
    }

    public function statusLabel()
    {
        return '';
    }

    public function save(array $options = [])
    {
        try {
            return parent::save($options);
        } catch (QueryException $exception) {
            // check if public_id has been taken
            if ($exception->getCode() == 23000 && static::$hasPublicId) {
                /*$nextId = static::getNextPublicId($this->company_id);
                if ($nextId != $this->public_id) {
                    $this->public_id = $nextId;
                    if (env('MULTI_DB_ENABLED')) {
                        if ($this->contact_key) {
                            $this->contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                        } elseif ($this->invitation_key) {
                            $this->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                        }
                    }

                    return $this->save($options);
                }*/
            }
            throw $exception;
        }
    }

    public function equalTo($obj)
    {
        if (empty($obj->id)) {
            return false;
        }

        return $this->id == $obj->id && $this->getEntityType() == $obj->entityType;
    }

    public function __call($method, $params)
    {
        $entity = strtolower(class_basename($this));

        if ($entity) {
            $configPath = "modules.relations.$entity.$method";

            if (config()->has($configPath)) {
                $function = config()->get($configPath);

                return $function($this);
            }
        }

        return parent::__call($method, $params);
    }
}

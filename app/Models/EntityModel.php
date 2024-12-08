<?php

namespace App\Models;

use Module;
use Utils;

/**
 * Class EntityModel.
 */
class EntityModel extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

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
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * @param $method
     * @param $params
     */
    public function __call($method, $params)
    {
        if (count(config('modules.relations')) > 0) {
            $entityType = $this->getEntityType();

            if ($entityType) {
                $config = implode('.', ['modules.relations.' . $entityType, $method]);
                if (config()->has($config)) {
                    $function = config()->get($config);

                    return $function($this);
                }
            }
        }

        return parent::__call($method, $params);
    }

    /**
     * @param null $context
     *
     * @return mixed
     */
    public static function createNew($context = null): static
    {
        $className = static::class;
        $entity = new $className();

        if ($context) {
            $user = $context instanceof User ? $context : $context->user;
            $account = $context->account;
        } elseif (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $account = \Illuminate\Support\Facades\Auth::user()->account;
        } else {
            Utils::fatalError();
        }

        $entity->user_id = $user->id;
        $entity->account_id = $account->id;

        // store references to the original user/account to prevent needing to reload them
        $entity->setRelation('user', $user);
        $entity->setRelation('account', $account);

        if (static::$hasPublicId) {
            $entity->public_id = static::getNextPublicId($entity->account_id);
        }

        return $entity;
    }

    /**
     * @param $publicId
     *
     * @return mixed
     */
    public static function getPrivateId($publicId)
    {
        if ( ! $publicId) {
            return;
        }

        $className = static::class;

        if (method_exists($className, 'trashed')) {
            return $className::scope($publicId)->withTrashed()->value('id');
        }

        return $className::scope($publicId)->value('id');
    }

    /**
     * @param $entityType
     *
     * @return string
     */
    public static function getClassName($entityType): string
    {
        if ( ! Utils::isNinjaProd() && ($module = Module::find($entityType))) {
            return sprintf('Modules\%s\Models\%s', $module->getName(), $module->getName());
        }

        if ($entityType == ENTITY_QUOTE || $entityType == ENTITY_RECURRING_INVOICE) {
            $entityType = ENTITY_INVOICE;
        }

        return 'App\\Models\\' . ucwords(Utils::toCamelCase($entityType));
    }

    /**
     * @param $entityType
     *
     * @return string
     */
    public static function getTransformerName($entityType): string
    {
        if (Utils::isNinjaProd()) {
            return 'App\\Ninja\\Transformers\\' . ucwords(Utils::toCamelCase($entityType)) . 'Transformer';
        }

        if ($module = Module::find($entityType)) {
            return sprintf('Modules\%s\Transformers\%sTransformer', $module->getName(), $module->getName());
        }

        return 'App\\Ninja\\Transformers\\' . ucwords(Utils::toCamelCase($entityType)) . 'Transformer';
    }

    /**
     * @param       $data
     * @param       $entityType
     * @param mixed $entity
     *                          TODO Remove $entityType parameter
     *
     * @return bool|string
     */
    public static function validate($data, $entityType = false, $entity = false)
    {
        if ( ! $entityType) {
            $className = static::class;
            $entityBlank = new $className();
            $entityType = $entityBlank->getEntityType();
        }

        // Use the API request if it exists
        $action = $entity ? 'update' : 'create';
        $requestClass = sprintf('App\\Http\\Requests\\%s%sAPIRequest', ucwords($action), \Illuminate\Support\Str::studly($entityType));
        if ( ! class_exists($requestClass)) {
            $requestClass = sprintf('App\\Http\\Requests\\%s%sRequest', ucwords($action), \Illuminate\Support\Str::studly($entityType));
        }

        $request = new $requestClass();
        $request->setUserResolver(fn () => \Illuminate\Support\Facades\Auth::user());
        $request->setEntity($entity);
        $request->replace($data);

        if ( ! $request->authorize()) {
            return trans('texts.not_allowed');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());

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
        ];

        return \Illuminate\Support\Arr::get($icons, $entityType);
    }

    public static function getFormUrl($entityType)
    {
        if (in_array($entityType, [ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_TEMPLATE])) {
            return str_replace('_', 's/', Utils::pluralizeEntityType($entityType));
        }

        return Utils::pluralizeEntityType($entityType);
    }

    /**
     * @return mixed[]
     */
    public static function getStates($entityType = false): array
    {
        $data = [];

        foreach (static::$statuses as $status) {
            $data[$status] = trans('texts.' . $status);
        }

        return $data;
    }

    public static function getStatuses($entityType = false): array
    {
        return [];
    }

    public static function getStatesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStates($entityType);
    }

    public static function getStatusesFor($entityType = false)
    {
        $class = static::getClassName($entityType);

        return $class::getStatuses($entityType);
    }

    /**
     * @return string
     */
    public function getActivityKey(): string
    {
        return '[' . $this->getEntityType() . ':' . $this->public_id . ':' . $this->getDisplayName() . ']';
    }

    public function entityKey(): string
    {
        return $this->public_id . ':' . $this->getEntityType();
    }

    public function subEntityType()
    {
        return $this->getEntityType();
    }

    public function isEntityType($type): bool
    {
        return $this->getEntityType() === $type;
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

    /**
     * @param      $query
     * @param bool $publicId
     * @param bool $accountId
     *
     * @return mixed
     */
    public function scopeScope($query, $publicId = false, $accountId = false)
    {
        // If 'false' is passed as the publicId return nothing rather than everything
        if (func_num_args() > 1 && ! $publicId && ! $accountId) {
            $query->where('id', '=', 0);

            return $query;
        }

        if ( ! $accountId) {
            $accountId = \Illuminate\Support\Facades\Auth::user()->account_id;
        }

        $query->where($this->getTable() . '.account_id', '=', $accountId);

        if ($publicId) {
            if (is_array($publicId)) {
                $query->whereIn('public_id', $publicId);
            } else {
                $query->wherePublicId($publicId);
            }
        }

        if (\Illuminate\Support\Facades\Auth::check() && method_exists($this, 'getEntityType')
            && ! \Illuminate\Support\Facades\Auth::user()->hasPermission('view_' . $this->getEntityType())
            && $this->getEntityType() != ENTITY_TAX_RATE
            && $this->getEntityType() != ENTITY_DOCUMENT
            && $this->getEntityType() != ENTITY_INVITATION) {
            $query->where(Utils::pluralizeEntityType($this->getEntityType()) . '.user_id', '=', \Illuminate\Support\Facades\Auth::user()->id);
        }

        return $query;
    }

    public function scopeWithActiveOrSelected($query, $id = false)
    {
        return $query->withTrashed()
            ->where(function ($query) use ($id): void {
                $query->whereNull('deleted_at')
                    ->orWhere('id', '=', $id);
            });
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeWithArchived($query)
    {
        return $query->withTrashed()->where('is_deleted', '=', false);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->public_id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    public function setNullValues(): void
    {
        foreach ($this->fillable as $field) {
            if (mb_strstr($field, '_id') && ! $this->{$field}) {
                $this->{$field} = null;
            }
        }
    }

    // converts "App\Models\Client" to "client_id"

    /**
     * @return string
     */
    public function getKeyField(): string
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $name = $parts[count($parts) - 1];

        return mb_strtolower($name) . '_id';
    }

    public function loadFromRequest(): void
    {
        foreach (static::$requestFields as $field) {
            if ($value = request()->{$field}) {
                $this->{$field} = mb_strpos($field, 'date') ? Utils::fromSqlDate($value) : $value;
            }
        }
    }

    // isDirty return true if the field's new value is the same as the old one
    public function isChanged(): bool
    {
        foreach ($this->fillable as $field) {
            if ($this->{$field} != $this->getOriginal($field)) {
                return true;
            }
        }

        return false;
    }

    public function statusClass(): string
    {
        return '';
    }

    public function statusLabel(): string
    {
        return '';
    }

    public function save(array $options = [])
    {
        try {
            return parent::save($options);
        } catch (\Illuminate\Database\QueryException $queryException) {
            // check if public_id has been taken
            if ($queryException->getCode() == 23000 && static::$hasPublicId) {
                $nextId = static::getNextPublicId($this->account_id);
                if ($nextId != $this->public_id) {
                    $this->public_id = $nextId;
                    if (env('MULTI_DB_ENABLED')) {
                        if ($this->contact_key) {
                            $this->contact_key = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
                        } elseif ($this->invitation_key) {
                            $this->invitation_key = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
                        }
                    }

                    return $this->save($options);
                }
            }

            throw $queryException;
        }
    }

    public function equalTo($obj)
    {
        if (empty($obj->id)) {
            return false;
        }

        return $this->id == $obj->id && $this->getEntityType() == $obj->entityType;
    }

    private static function getNextPublicId($accountId): int|float
    {
        $className = static::class;

        if (method_exists($className, 'trashed')) {
            $lastEntity = $className::whereAccountId($accountId)->withTrashed();
        } else {
            $lastEntity = $className::whereAccountId($accountId);
        }

        $lastEntity = $lastEntity->orderBy('public_id', 'DESC')->first();

        if ($lastEntity) {
            return $lastEntity->public_id + 1;
        }

        return 1;
    }
}

<?php

namespace App\Ninja\Repositories;

use Utils;

/**
 * Class BaseRepository.
 */
class BaseRepository
{
    /**
     * @return null
     */
    public function getClassName() {}

    public function archive($entity): void
    {
        if ($entity->trashed()) {
            return;
        }

        $entity->delete();

        $className = $this->getEventClass($entity, 'Archived');

        if (class_exists($className)) {
            event(new $className($entity));
        }
    }

    public function restore($entity): void
    {
        if ( ! $entity->trashed()) {
            return;
        }

        $fromDeleted = false;
        $entity->restore();

        if ($entity->is_deleted) {
            $fromDeleted = true;
            $entity->is_deleted = false;
            $entity->save();
        }

        $className = $this->getEventClass($entity, 'Restored');

        if (class_exists($className)) {
            event(new $className($entity, $fromDeleted));
        }
    }

    public function delete($entity): void
    {
        if ($entity->is_deleted) {
            return;
        }

        $entity->is_deleted = true;
        $entity->save();

        $entity->delete();

        $className = $this->getEventClass($entity, 'Deleted');

        if (class_exists($className)) {
            event(new $className($entity));
        }
    }

    /**
     * @return int
     */
    public function bulk($ids, $action): int
    {
        if ( ! $ids) {
            return 0;
        }

        $entities = $this->findByPublicIdsWithTrashed($ids);

        foreach ($entities as $entity) {
            if (\Illuminate\Support\Facades\Auth::user()->can('edit', $entity)) {
                $this->{$action}($entity);
            }
        }

        return count($entities);
    }

    /**
     * @return mixed
     */
    public function findByPublicIds($ids)
    {
        return $this->getInstance()->scope($ids)->get();
    }

    /**
     * @return mixed
     */
    public function findByPublicIdsWithTrashed($ids)
    {
        return $this->getInstance()->scope($ids)->withTrashed()->get();
    }

    protected function applyFilters($query, string $entityType, $table = false)
    {
        $table = Utils::pluralizeEntityType($table ?: $entityType);

        if ($filter = session('entity_state_filter:' . $entityType, STATUS_ACTIVE)) {
            $filters = explode(',', $filter);
            $query->where(function ($query) use ($filters, $table): void {
                $query->whereNull($table . '.id');

                if (in_array(STATUS_ACTIVE, $filters)) {
                    $query->orWhereNull($table . '.deleted_at');
                }

                if (in_array(STATUS_ARCHIVED, $filters)) {
                    $query->orWhere(function ($query) use ($table): void {
                        $query->whereNotNull($table . '.deleted_at');

                        if ($table != 'users') {
                            $query->where($table . '.is_deleted', '=', 0);
                        }
                    });
                }

                if (in_array(STATUS_DELETED, $filters)) {
                    $query->orWhere(function ($query) use ($table): void {
                        $query->whereNotNull($table . '.deleted_at')
                            ->where($table . '.is_deleted', '=', 1);
                    });
                }
            });
        }

        return $query;
    }

    /**
     * @return mixed
     */
    private function getInstance()
    {
        $className = $this->getClassName();

        return new $className();
    }

    /**
     * @return string
     */
    private function getEventClass($entity, string $type): string
    {
        return 'App\Events\\' . ucfirst($entity->getEntityType()) . 'Was' . $type;
    }
}

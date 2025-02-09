<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * Class DocumentPolicy.
 */
class DocumentPolicy extends EntityPolicy
{
    /**
     * @param mixed $item
     *
     */
    public static function create(User $user, $item): bool
    {
        return true;
    }

    /**
     * @param Document $document
     * @return bool
     */
    public static function view(User $user, $document)
    {
        if ($user->hasPermission(['view_expense', 'view_invoice'], true)) {
            return true;
        }

        if ($document->expense) {
            if ($document->expense->invoice) {
                return $user->can('view', $document->expense->invoice);
            }

            return $user->can('view', $document->expense);
        }

        if ($document->invoice) {
            return $user->can('view', $document->invoice);
        }

        return $user->owns($document);
    }
}

<?php
namespace App\Ninja\Transformers;

use App\Models\Activity;

/**
 * @SWG\Definition(definition="Activity", @SWG\Xml(name="Activity"))
 */
class ActivityTransformer extends EntityTransformer
{
	  /**
     * @SWG\Property(property="id", type="integer", example=1)
     * @SWG\Property(property="activity_type_id", type="integer", example=1)
     * @SWG\Property(property="client_id", type="integer", example=1)
     * @SWG\Property(property="user_id", type="integer", example=1)
     * @SWG\Property(property="invoice_id", type="integer", example=1)
     * @SWG\Property(property="payment_id", type="integer", example=1)
     * @SWG\Property(property="credit_id", type="integer", example=1)
     * @SWG\Property(property="updated_at", type="integer", example=1451160233, readOnly=true)
     * @SWG\Property(property="expense_id", type="integer", example=1)
     * @SWG\Property(property="is_system", type="boolean", example=false)
     * @SWG\Property(property="contact_id", type="integer", example=1)
     * @SWG\Property(property="task_id", type="integer", example=1)
     */

    protected array $defaultIncludes = [];

    /**
     * @var array
     */
    protected array $availableIncludes = [];

    /**
     * @param Activity $activity
     *
     * @return array
     */
    public function transform(Activity $activity)
    {
        return [
            'id' => $activity->key(),
            'activity_type_id' => $activity->activity_type_id,
            'client_id' => $activity->client ? $activity->client->public_id : null,
            'user_id' => $activity->user->public_id + 1,
            'invoice_id' => $activity->invoice ? $activity->invoice->public_id : null,
            'payment_id' => $activity->payment ? $activity->payment->public_id : null,
            'credit_id' => $activity->credit ? $activity->credit->public_id : null,
            'updated_at' => $this->getTimestamp($activity->updated_at),
            'expense_id' => $activity->expense_id ? $activity->expense->public_id : null,
            'is_system' => $activity->is_system ? (bool)$activity->is_system : null,
            'contact_id' => $activity->contact_id ? $activity->contact->public_id : null,
            'task_id' => $activity->task_id ? $activity->task->public_id : null,
        ];
    }
}

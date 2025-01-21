<?php

namespace App\Http\Requests;

class UpdateRecurringExpenseRequest extends RecurringExpenseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->entity()) {
            return false;
        }

        return (bool) $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{amount: string}
     */
    public function rules(): array
    {
        return [
            'amount' => 'numeric',
        ];
    }
}

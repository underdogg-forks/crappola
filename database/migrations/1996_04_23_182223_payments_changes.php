<?php

use App\Models\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class PaymentsChanges extends Migration
{
    public function up(): void
    {
        Schema::create('payment_statuses', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        $statuses = [
            ['id' => '1', 'name' => 'Pending'],
            ['id' => '2', 'name' => 'Voided'],
            ['id' => '3', 'name' => 'Failed'],
            ['id' => '4', 'name' => 'Completed'],
            ['id' => '5', 'name' => 'Partially Refunded'],
            ['id' => '6', 'name' => 'Refunded'],
        ];

        foreach ($statuses as $status) {
            $record = PaymentStatus::find($status['id']);
            if ($record) {
                $record->name = $status['name'];
                $record->save();
            } else {
                PaymentStatus::create($status);
            }
        }

        Schema::create('payment_methods', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('account_gateway_token_id')->nullable();
            $table->unsignedInteger('payment_type_id');
            $table->string('source_reference');

            $table->unsignedInteger('routing_number')->nullable();
            $table->smallInteger('last4')->unsigned()->nullable();
            $table->date('expiration')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('status')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('ip')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            //$table->foreign('account_gateway_token_id')->references('id')->on('account_gateway_tokens');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_statuses');

        Schema::dropIfExists('payment_methods');
    }
}

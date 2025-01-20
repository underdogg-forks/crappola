<?php

use App\Models\PaymentStatus;
use Illuminate\Database\Migrations\Migration;

class PaymentsChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_statuses');

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
        Eloquent::reguard();

        Schema::dropIfExists('payment_methods');

        Schema::create('payment_methods', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('account_gateway_token_id');
            $table->unsignedInteger('payment_type_id');
            $table->string('source_reference');

            $table->unsignedInteger('routing_number')->nullable();
            $table->smallInteger('last4')->unsigned()->nullable();
            $table->date('expiration')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('public_id')->index();
        });

        Schema::table('payment_methods', function ($table): void {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('account_gateway_token_id')->references('id')->on('account_gateway_tokens');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->unique(['company_id', 'public_id']);
        });

        Schema::table('payments', function ($table): void {
            $table->decimal('refunded', 13, 2);
            $table->unsignedInteger('payment_status_id')->default(PAYMENT_STATUS_COMPLETED);

            $table->unsignedInteger('routing_number')->nullable();
            $table->smallInteger('last4')->unsigned()->nullable();
            $table->date('expiration')->nullable();
            $table->text('gateway_error')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('payment_method_id')->nullable();
        });

        Schema::table('payments', function ($table): void {
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });

        Schema::table('invoices', function ($table): void {
            $table->boolean('client_enable_auto_bill')->default(false);
        });

        \DB::table('invoices')
            ->where('auto_bill', '=', 1)
            ->update(['client_enable_auto_bill' => 1, 'auto_bill' => AUTO_BILL_OPT_OUT]);

        \DB::table('invoices')
            ->where('auto_bill', '=', 0)
            ->where('is_recurring', '=', 1)
            ->update(['auto_bill' => AUTO_BILL_OFF]);

        Schema::table('account_gateway_tokens', function ($table): void {
            $table->unsignedInteger('default_payment_method_id')->nullable();
        });

        Schema::table('account_gateway_tokens', function ($table): void {
            $table->foreign('default_payment_method_id')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('payments', function ($table): void {
            $table->dropColumn('refunded');
            $table->dropForeign('payments_payment_status_id_foreign');
            $table->dropColumn('payment_status_id');

            $table->dropColumn('routing_number');
            $table->dropColumn('last4');
            $table->dropColumn('expiration');
            $table->dropColumn('gateway_error');
            $table->dropColumn('email');

            $table->dropForeign('payments_payment_method_id_foreign');
            $table->dropColumn('payment_method_id');
        });

        \DB::table('invoices')
            ->where('auto_bill', '=', AUTO_BILL_OFF)
            ->update(['auto_bill' => 0]);

        \DB::table('invoices')
            ->where(function ($query): void {
                $query->where('auto_bill', '=', AUTO_BILL_ALWAYS);
                $query->orwhere(function ($query): void {
                    $query->where('auto_bill', '!=', 0);
                    $query->where('client_enable_auto_bill', '=', 1);
                });
            })
            ->update(['auto_bill' => 1]);

        \DB::table('invoices')
            ->where('auto_bill', '!=', 1)
            ->update(['auto_bill' => 0]);

        Schema::table('invoices', function ($table): void {
            $table->dropColumn('client_enable_auto_bill');
        });

        Schema::dropIfExists('payment_statuses');

        Schema::table('account_gateway_tokens', function ($table): void {
            $table->dropForeign('account_gateway_tokens_default_payment_method_id_foreign');
            $table->dropColumn('default_payment_method_id');
        });

        Schema::dropIfExists('payment_methods');
    }
}

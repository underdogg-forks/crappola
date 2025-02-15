<?php

use App\Models\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class PaymentsChanges extends Migration
{
    public function up()
    {
        Schema::dropIfExists('payment_statuses');

        Schema::create('payment_statuses', function ($table) {
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

        Model::unguard();
        foreach ($statuses as $status) {
            $record = PaymentStatus::find($status['id']);
            if ($record) {
                $record->name = $status['name'];
                $record->save();
            } else {
                PaymentStatus::create($status);
            }
        }
        Model::reguard();

        Schema::dropIfExists('payment_methods');

        Schema::create('payment_methods', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('account_gateway_token_id')->nullable();
            $table->unsignedInteger('payment_type_id');
            $table->unsignedInteger('currency_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->index();

            $table->string('source_reference');

            $table->unsignedInteger('routing_number')->nullable();
            $table->smallInteger('last4')->unsigned()->nullable();
            $table->date('expiration')->nullable();
            $table->string('email')->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_gateway_token_id')->references('id')->on('account_gateway_tokens')->onDelete('cascade');
        });

        Schema::table('payment_methods', function ($table) {});

        Schema::table('payments', function ($table) {
            $table->unsignedInteger('payment_status_id')->after('id')->default(PAYMENT_STATUS_COMPLETED);
            $table->unsignedInteger('payment_method_id')->after('payment_status_id')->nullable();
        });

        Schema::table('payments', function ($table) {
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });

        Schema::table('invoices', function ($table) {
            $table->boolean('client_enable_auto_bill')->default(false);
        });

        \DB::table('invoices')
            ->where('auto_bill', '=', 1)
            ->update(['client_enable_auto_bill' => 1, 'auto_bill' => AUTO_BILL_OPT_OUT]);

        \DB::table('invoices')
            ->where('auto_bill', '=', 0)
            ->where('is_recurring', '=', 1)
            ->update(['auto_bill' => AUTO_BILL_OFF]);

        Schema::table('account_gateway_tokens', function ($table) {
            $table->unsignedInteger('default_payment_method_id')->after('id')->nullable();
        });

        Schema::table('account_gateway_tokens', function ($table) {
            $table->foreign('default_payment_method_id')->references('id')->on('payment_methods');
        });
    }

    public function down()
    {
        Schema::table('payments', function ($table) {
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
            ->where(function ($query) {
                $query->where('auto_bill', '=', AUTO_BILL_ALWAYS);
                $query->orwhere(function ($query) {
                    $query->where('auto_bill', '!=', 0);
                    $query->where('client_enable_auto_bill', '=', 1);
                });
            })
            ->update(['auto_bill' => 1]);

        \DB::table('invoices')
            ->where('auto_bill', '!=', 1)
            ->update(['auto_bill' => 0]);

        Schema::table('invoices', function ($table) {
            $table->dropColumn('client_enable_auto_bill');
        });

        Schema::dropIfExists('payment_statuses');

        Schema::table('account_gateway_tokens', function ($table) {
            $table->dropForeign('account_gateway_tokens_default_payment_method_id_foreign');
            $table->dropColumn('default_payment_method_id');
        });

        Schema::dropIfExists('payment_methods');
    }
}

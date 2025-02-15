<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCustomInvoiceFields extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->string('custom_invoice_item_label1')->nullable();
            $table->string('custom_invoice_item_label2')->nullable();
            $table->string('recurring_invoice_number_prefix')->default('R');
            $table->boolean('enable_client_portal')->default(true);
            $table->text('invoice_fields')->nullable();
            $table->text('devices')->nullable();
        });

        Schema::table('invoice_items', function ($table) {
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('custom_invoice_item_label1');
            $table->dropColumn('custom_invoice_item_label2');
            $table->dropColumn('recurring_invoice_number_prefix');
            $table->dropColumn('enable_client_portal');
            $table->dropColumn('invoice_fields');
            $table->dropColumn('devices');
        });

        Schema::table('invoice_items', function ($table) {
            $table->dropColumn('custom_value1');
            $table->dropColumn('custom_value2');
        });
    }
}

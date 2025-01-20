<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupportTokenBilling extends Migration
{
    public function up(): void
    {
        Schema::create('account_gateway_tokens', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('account_gateway_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('default_payment_method_id')->nullable();

            $table->string('token');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('account_gateway_id')->references('id')->on('account_gateways')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->foreign('default_payment_method_id')->references('id')->on('payment_methods');
        });

        DB::table('companies')->update(['token_billing_type_id' => TOKEN_BILLING_ALWAYS]);
    }

    public function down(): void
    {
        Schema::drop('account_gateway_tokens');
    }
}

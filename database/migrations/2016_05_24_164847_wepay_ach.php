<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->string('contact_key')->nullable()->default(null)->index()->unique();
        });

        Schema::table('payment_methods', function ($table): void {
            $table->string('bank_name')->nullable();
            $table->string('ip')->nullable();
        });

        Schema::table('payments', function ($table): void {
            $table->string('bank_name')->nullable();
            $table->string('ip')->nullable();
        });

        Schema::table('accounts', function ($table): void {
            $table->boolean('auto_bill_on_due_date')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->dropColumn('contact_key');
        });

        Schema::table('payments', function ($table): void {
            $table->dropColumn('bank_name');
            $table->dropColumn('ip');
        });

        Schema::table('payment_methods', function ($table): void {
            $table->dropColumn('bank_name');
            $table->dropColumn('ip');
        });

        Schema::table('accounts', function ($table): void {
            $table->dropColumn('auto_bill_on_due_date');
        });
    }
};

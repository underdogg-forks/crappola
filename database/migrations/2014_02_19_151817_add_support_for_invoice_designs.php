<?php

use Illuminate\Database\Migrations\Migration;

class AddSupportForInvoiceDesigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('invoice_designs', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('invoice_designs')->insert(['name' => 'Clean']);
        DB::table('invoice_designs')->insert(['name' => 'Bold']);
        DB::table('invoice_designs')->insert(['name' => 'Modern']);
        DB::table('invoice_designs')->insert(['name' => 'Plain']);

        Schema::table('invoices', function ($table): void {
            $table->unsignedInteger('invoice_design_id')->default(1);
        });

        Schema::table('companies', function ($table): void {
            $table->unsignedInteger('invoice_design_id')->default(1);
        });

        DB::table('invoices')->update(['invoice_design_id' => 1]);
        DB::table('companies')->update(['invoice_design_id' => 1]);

        Schema::table('invoices', function ($table): void {
            $table->foreign('invoice_design_id')->references('id')->on('invoice_designs');
        });

        Schema::table('companies', function ($table): void {
            $table->foreign('invoice_design_id')->references('id')->on('invoice_designs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->dropForeign('invoices_invoice_design_id_foreign');
            $table->dropColumn('invoice_design_id');
        });

        Schema::table('companies', function ($table): void {
            $table->dropForeign('accounts_invoice_design_id_foreign');
            $table->dropColumn('invoice_design_id');
        });

        Schema::dropIfExists('invoice_designs');
    }
}

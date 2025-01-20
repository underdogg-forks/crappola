<?php

use Illuminate\Database\Migrations\Migration;

class AddDefaultQuoteTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->text('quote_terms')->nullable();
        });

        $companys = DB::table('companies')
            ->orderBy('id')
            ->get(['id', 'invoice_terms']);

        foreach ($companys as $company) {
            DB::table('companies')
                ->where('id', $company->id)
                ->update(['quote_terms' => $company->invoice_terms]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('quote_terms');
        });
    }
}

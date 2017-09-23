<?php
use Illuminate\Database\Migrations\Migration;

class AddDefaultQuoteTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function ($table) {
            $table->text('quote_terms')->nullable();
        });
        $accounts = DB::table('companies')
            ->orderBy('id')
            ->get(['id', 'invoice_terms']);
        foreach ($accounts as $account) {
            DB::table('companies')
                ->where('id', $account->id)
                ->update(['quote_terms' => $account->invoice_terms]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function ($table) {
            $table->dropColumn('quote_terms');
        });
    }
}

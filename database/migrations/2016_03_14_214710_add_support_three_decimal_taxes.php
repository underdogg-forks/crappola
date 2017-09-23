<?php
use Illuminate\Database\Migrations\Migration;

class AddSupportThreeDecimalTaxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lookup__taxrates', function ($table) {
            if (Schema::hasColumn('lookup__taxrates', 'rate')) {
                $table->decimal('rate', 13, 3)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lookup__taxrates', function ($table) {
            $table->decimal('rate', 13, 2)->change();
        });
    }
}

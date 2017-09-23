<?php
use Illuminate\Database\Migrations\Migration;

class AddRememberToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function ($table) {
            $table->string('remember_token', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function ($table) {
            $table->dropColumn('remember_token');
        });
    }
}

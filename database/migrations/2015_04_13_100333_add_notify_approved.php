<?php
use Illuminate\Database\Migrations\Migration;

class AddNotifyApproved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function ($table) {
            $table->boolean('notify_approved')->default(true);
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
            $table->dropColumn('notify_approved');
        });
    }
}

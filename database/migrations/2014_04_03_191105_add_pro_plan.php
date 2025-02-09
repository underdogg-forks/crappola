<?php

use Illuminate\Database\Migrations\Migration;

class AddProPlan extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->date('pro_plan_paid')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('pro_plan_paid');
        });
    }
}

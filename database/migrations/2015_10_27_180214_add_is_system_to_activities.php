<?php

use App\Models\Activity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddIsSystemToActivities extends Migration
{
    public function up()
    {
        Schema::table('activities', function ($table) {});

        $activities = Activity::where('message', 'like', '%<i>System</i>%')->get();
        foreach ($activities as $activity) {
            $activity->is_system = true;
            $activity->save();
        }

        Schema::table('activities', function ($table) {
            $table->dropColumn('message');
        });
    }

    public function down()
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('is_system');
        });

        Schema::table('activities', function ($table) {
            $table->text('message')->nullable();
        });
    }
}

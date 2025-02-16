<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnableResumingTasks extends Migration
{
    public function up()
    {
        Schema::table('tasks', function ($table) {});

        $tasks = DB::table('tasks')
            ->where('duration', '=', -1)
            ->select('id', 'duration', 'start_time')
            ->get();

        foreach ($tasks as $task) {
            $data = [
                'is_running' => true,
                'duration'   => 0,
            ];

            DB::table('tasks')
                ->where('id', $task->id)
                ->update($data);
        }
    }

    public function down()
    {
        Schema::table('tasks', function ($table) {
            $table->dropColumn('is_running');
            $table->dropColumn('resume_time');
            $table->dropColumn('break_duration');
            $table->dropColumn('time_log');
        });
    }
}

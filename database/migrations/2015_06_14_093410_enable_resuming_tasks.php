<?php

use Illuminate\Database\Migrations\Migration;

class EnableResumingTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tasks', function ($table): void {
            $table->boolean('is_running')->after('task_status_sort_order')->default(false);
            $table->integer('break_duration')->after('is_running')->nullable();
            $table->timestamp('resume_time')->after('break_duration')->nullable();
            $table->text('time_log')->after('resume_time')->nullable();
        });

        $tasks = DB::table('tasks')
                    ->where('duration', '=', -1)
                    ->select('id', 'duration', 'start_at')
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tasks', function ($table): void {
            $table->dropColumn('is_running');
            $table->dropColumn('resume_time');
            $table->dropColumn('break_duration');
            $table->dropColumn('time_log');
        });
    }
}

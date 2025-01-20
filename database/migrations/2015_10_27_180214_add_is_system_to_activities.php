<?php

use App\Models\Activity;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('activities', function ($table): void {
            $table->boolean('is_system')->default(0);
        });

        $activities = Activity::where('message', 'like', '%<i>System</i>%')->get();
        foreach ($activities as $activity) {
            $activity->is_system = true;
            $activity->save();
        }

        Schema::table('activities', function ($table): void {
            $table->dropColumn('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('activities', function ($table): void {
            $table->dropColumn('is_system');
        });

        Schema::table('activities', function ($table): void {
            $table->text('message')->nullable();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('date_formats')
            ->where('label', '20/03/2013')
            ->update(['label' => '20-03-2013']);

        DB::table('datetime_formats')
            ->where('label', '20/03/2013 6:15 pm')
            ->update(['label' => '20-03-2013 6:15 pm']);

        Schema::table('datetime_formats', function (Blueprint $table): void {
            $table->string('format_moment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('datetime_formats', function (Blueprint $table): void {
            $table->dropColumn('format_moment');
        });
    }
};

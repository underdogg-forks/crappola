<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDartFormatColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('date_formats', function (Blueprint $table): void {
            $table->string('format_dart');
        });
        Schema::table('datetime_formats', function (Blueprint $table): void {
            $table->string('format_dart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('date_formats', function (Blueprint $table): void {
            $table->dropColumn('format_dart');
        });
        Schema::table('datetime_formats', function (Blueprint $table): void {
            $table->dropColumn('format_dart');
        });
    }
}

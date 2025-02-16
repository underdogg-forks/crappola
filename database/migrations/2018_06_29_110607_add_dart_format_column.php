<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDartFormatColumn extends Migration
{
    public function up()
    {
        Schema::table('date_formats', function (Blueprint $table) {});
        Schema::table('datetime_formats', function (Blueprint $table) {});
    }

    public function down()
    {
        Schema::table('date_formats', function (Blueprint $table) {
            $table->dropColumn('format_dart');
        });
        Schema::table('datetime_formats', function (Blueprint $table) {
            $table->dropColumn('format_dart');
        });
    }
}

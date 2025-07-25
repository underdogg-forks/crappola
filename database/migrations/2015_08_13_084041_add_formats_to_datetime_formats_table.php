<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFormatsToDatetimeFormatsTable extends Migration
{
    public function up()
    {
        DB::table('date_formats')
            ->where('label', '20/03/2013')
            ->update(['label' => '20-03-2013']);

        DB::table('datetime_formats')
            ->where('label', '20/03/2013 6:15 pm')
            ->update(['label' => '20-03-2013 6:15 pm']);

        Schema::table('datetime_formats', function (Blueprint $table) {});
    }

    public function down()
    {
        Schema::table('datetime_formats', function (Blueprint $table) {
            $table->dropColumn('format_moment');
        });
    }
}

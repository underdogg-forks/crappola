<?php

use Illuminate\Database\Migrations\Migration;

class AddFontSize extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->smallInteger('font_size')->default(DEFAULT_FONT_SIZE);
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('font_size');
        });
    }
}

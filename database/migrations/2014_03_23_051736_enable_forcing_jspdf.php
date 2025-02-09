<?php

use Illuminate\Database\Migrations\Migration;

class EnableForcingJspdf extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('force_pdfjs')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('force_pdfjs');
        });
    }
}

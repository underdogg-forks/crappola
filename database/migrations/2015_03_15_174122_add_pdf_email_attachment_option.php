<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddPdfEmailAttachmentOption extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('pdf_email_attachment');
        });
    }
}

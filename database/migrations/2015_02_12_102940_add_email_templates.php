<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddEmailTemplates extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        if (Schema::hasColumn('accounts', 'email_template_invoice')) {
            Schema::table('accounts', function ($table) {
                $table->dropColumn('email_template_invoice');
                $table->dropColumn('email_template_quote');
                $table->dropColumn('email_template_payment');
            });
        }
    }
}

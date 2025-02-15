<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddReminderEmails extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            if (Schema::hasColumn('accounts', 'email_subject_invoice')) {
                $table->dropColumn('email_subject_invoice');
                $table->dropColumn('email_subject_quote');
                $table->dropColumn('email_subject_payment');

                $table->dropColumn('email_subject_reminder1');
                $table->dropColumn('email_subject_reminder2');
                $table->dropColumn('email_subject_reminder3');

                $table->dropColumn('email_template_reminder1');
                $table->dropColumn('email_template_reminder2');
                $table->dropColumn('email_template_reminder3');
            }

            $table->dropColumn('enable_reminder1');
            $table->dropColumn('enable_reminder2');
            $table->dropColumn('enable_reminder3');

            $table->dropColumn('num_days_reminder1');
            $table->dropColumn('num_days_reminder2');
            $table->dropColumn('num_days_reminder3');
        });
    }
}

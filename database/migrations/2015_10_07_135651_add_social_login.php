<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddSocialLogin extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {});

        Schema::table('accounts', function ($table) {});

        Schema::table('invoices', function ($table) {});

        Schema::table('invitations', function ($table) {});
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('oauth_user_id');
            $table->dropColumn('oauth_provider_id');
        });

        Schema::table('accounts', function ($table) {
            $table->dropColumn('custom_invoice_text_label1');
            $table->dropColumn('custom_invoice_text_label2');
        });

        Schema::table('invoices', function ($table) {
            $table->dropColumn('custom_text_value1');
            $table->dropColumn('custom_text_value2');
        });

        Schema::table('invitations', function ($table) {
            $table->dropColumn('opened_date');
            $table->dropColumn('message_id');
            $table->dropColumn('email_error');
        });
    }
}

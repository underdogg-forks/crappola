<?php

use App\Libraries\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDefaultNoteToClient extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 13, 3);
            $table->string('tax_name2')->nullable();
            $table->decimal('tax_rate2', 13, 3);
        });

        Schema::table('products', function ($table) {
            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 13, 3);
            $table->string('tax_name2')->nullable();
            $table->decimal('tax_rate2', 13, 3);
        });

        DB::statement('update products
            left join tax_rates on tax_rates.id = products.default_tax_rate_id
            set products.tax_name1 = tax_rates.name, products.tax_rate1 = tax_rates.rate');

        DB::statement('update accounts
            left join tax_rates on tax_rates.id = accounts.default_tax_rate_id
            set accounts.tax_name1 = tax_rates.name, accounts.tax_rate1 = tax_rates.rate');

        if (Schema::hasColumn('accounts', 'default_tax_rate_id')) {
            Schema::table('accounts', function ($table) {
                $table->dropColumn('default_tax_rate_id');
            });
        }

        if (Schema::hasColumn('products', 'default_tax_rate_id')) {
            Schema::table('products', function ($table) {
                $table->dropColumn('default_tax_rate_id');
            });
        }

        if (Utils::isNinja()) {
            Schema::table('users', function ($table) {
                $table->unique(['oauth_user_id', 'oauth_provider_id']);
            });
        }

        DB::statement('update accounts
            set quote_design_id = invoice_design_id');

        DB::statement('update invoice_designs
            set name = "Custom1"
            where id = 11
            and name = "Custom"');
    }

    public function down()
    {
        Schema::table('clients', function ($table) {
            $table->dropColumn('public_notes');
        });

        Schema::table('invoices', function ($table) {
            $table->dropColumn('private_notes');
        });

        Schema::table('payments', function ($table) {
            $table->dropColumn('private_notes');
        });

        Schema::table('accounts', function ($table) {
            $table->renameColumn('custom_design1', 'custom_design');
            $table->dropColumn('custom_design2');
            $table->dropColumn('custom_design3');
            $table->dropColumn('analytics_key');
            $table->dropColumn('tax_name1');
            $table->dropColumn('tax_rate1');
            $table->dropColumn('tax_name2');
            $table->dropColumn('tax_rate2');
        });

        Schema::table('products', function ($table) {
            $table->dropColumn('tax_name1');
            $table->dropColumn('tax_rate1');
            $table->dropColumn('tax_name2');
            $table->dropColumn('tax_rate2');
        });
    }
}

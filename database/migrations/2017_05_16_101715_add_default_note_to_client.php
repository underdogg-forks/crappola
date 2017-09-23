<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDefaultNoteToClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function ($table) {
            $table->text('public_notes')->nullable();
        });
        Schema::table('invoices', function ($table) {
            $table->text('private_notes')->nullable();
        });
        Schema::table('customers__payments', function ($table) {
            $table->text('private_notes')->nullable();
        });
        Schema::table('companies', function ($table) {
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
            left join lookup__taxrates on lookup__taxrates.id = products.default_tax_rate_id
            set products.tax_name1 = lookup__taxrates.name, products.tax_rate1 = lookup__taxrates.rate');
        DB::statement('update companies
            left join lookup__taxrates on lookup__taxrates.id = companies.default_tax_rate_id
            set companies.tax_name1 = lookup__taxrates.name, companies.tax_rate1 = lookup__taxrates.rate');
        if (Schema::hasColumn('companies', 'default_tax_rate_id')) {
            Schema::table('companies', function ($table) {
                $table->dropColumn('default_tax_rate_id');
            });
        }
        if (Schema::hasColumn('products', 'default_tax_rate_id')) {
            Schema::table('products', function ($table) {
                $table->dropColumn('default_tax_rate_id');
            });
        }
        if (Utils::isNinja()) {
            Schema::table('staff', function ($table) {
                $table->unique(['oauth_user_id', 'oauth_provider_id']);
            });
        }
        Schema::table('companies', function ($table) {
            $table->unsignedInteger('quote_design_id')->default(1);
            $table->renameColumn('custom_design', 'custom_design1');
            $table->mediumText('custom_design2')->nullable();
            $table->mediumText('custom_design3')->nullable();
            $table->string('analytics_key')->nullable();
        });
        DB::statement('update companies
            set quote_design_id = invoice_design_id');
        DB::statement('update invoice_designs
            set name = "Custom1"
            where id = 11
            and name = "Custom"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', function ($table) {
            $table->dropColumn('public_notes');
        });
        Schema::table('invoices', function ($table) {
            $table->dropColumn('private_notes');
        });
        Schema::table('customers__payments', function ($table) {
            $table->dropColumn('private_notes');
        });
        Schema::table('companies', function ($table) {
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

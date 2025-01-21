<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('companies', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('timezone_id')->nullable();
            $table->unsignedInteger('date_format_id')->nullable();
            $table->unsignedInteger('datetime_format_id')->nullable();
            $table->unsignedInteger('currency_id')->nullable();

            $table->unsignedInteger('industry_id')->nullable();
            $table->unsignedInteger('size_id')->nullable();

            $table->string('account_key')->unique();
            $table->string('name')->nullable();
            $table->string('ip');

            $table->boolean('inclusive_taxes')->default(0);

            $table->timestamp('last_login')->nullable();

            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedInteger('country_id')->nullable();

            $table->string('work_email')->nullable();
            $table->string('work_phone')->nullable();

            $table->string('vat_number')->nullable();
            $table->string('id_number')->nullable();

            $table->decimal('task_rate', 12, 4)->default(0);

            $table->boolean('enable_client_portal_dashboard')->default(true);
            $table->boolean('enable_portal_password')->default(0);
            $table->boolean('send_portal_password')->default(0);

            $table->boolean('require_approve_quote')->default(1);
            $table->integer('valid_until_days')->nullable();

            $table->unsignedInteger('language_id')->default(1);

            $table->text('invoice_terms')->nullable();
            $table->text('quote_terms')->nullable();

            $table->text('email_footer')->nullable();
            $table->text('invoice_footer')->nullable();

            $table->unsignedInteger('default_tax_rate_id')->nullable();
            $table->smallInteger('recurring_hour')->default(DEFAULT_SEND_RECURRING_HOUR);

            $table->boolean('invoice_taxes')->default(true);
            $table->boolean('invoice_item_taxes')->default(false);

            $table->boolean('show_item_taxes')->default(0);

            $table->unsignedInteger('header_font_id')->default(1);
            $table->unsignedInteger('body_font_id')->default(1);

            $table->boolean('fill_products')->default(true);
            $table->boolean('update_products')->default(true);

            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();

            $table->string('invoice_number_prefix')->nullable();
            $table->integer('invoice_number_counter')->default(1)->nullable();

            $table->string('quote_number_prefix')->nullable();
            $table->integer('quote_number_counter')->default(1)->nullable();

            $table->boolean('share_counter')->default(true);

            $table->smallInteger('token_billing_type_id')->default(TOKEN_BILLING_ALWAYS);

            $table->boolean('pdf_email_attachment')->default(0);

            $table->string('email_template_invoice')->nullable();
            $table->string('email_template_quote')->nullable();
            $table->string('email_template_payment')->nullable();

            $table->boolean('utf8_invoices')->default(true);
            $table->boolean('auto_wrap')->default(false);
            $table->string('subdomain')->nullable();

            $table->smallInteger('font_size')->default(DEFAULT_FONT_SIZE);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('timezone_id')->references('id')->on('timezones');
            $table->foreign('date_format_id')->references('id')->on('date_formats');
            $table->foreign('datetime_format_id')->references('id')->on('datetime_formats');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('industry_id')->references('id')->on('industries');
            $table->foreign('size_id')->references('id')->on('sizes');

            //DB::table('companies')->update(['language_id' => 1]);

            //DB::table('companies')->update(['fill_products' => true]);
            //DB::table('companies')->update(['update_products' => true]);

            //$table->foreign('language_id')->references('id')->on('languages');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

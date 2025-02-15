<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ConfideSetupUsersTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('password_reminders');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('users');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('timezones');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('date_formats');
        Schema::dropIfExists('datetime_formats');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('payment_types');

        Schema::create('countries', function ($table) {
            $table->increments('id');
            $table->string('capital', 255)->nullable();
            $table->string('citizenship', 255)->nullable();
            $table->string('country_code', 3)->default('');
            $table->string('currency', 255)->nullable();
            $table->string('currency_code', 255)->nullable();
            $table->string('currency_sub_unit', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('iso_3166_2', 2)->default('');
            $table->string('iso_3166_3', 3)->default('');
            $table->string('name', 255)->default('');
            $table->string('region_code', 3)->default('');
            $table->string('sub_region_code', 3)->default('');
            $table->boolean('eea')->default(0);
            $table->boolean('swap_currency_symbol')->default(0);
            $table->string('thousand_separator')->nullable();
            $table->string('decimal_separator')->nullable();
        });

        Schema::create('themes', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('payment_types', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('payment_terms', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->index();
            $t->integer('num_days');
            $t->string('name');
            $t->timestamps();
            $t->softDeletes();

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('timezones', function ($t) {
            $t->increments('id');
            $t->string('name');
            $t->string('location');
        });

        Schema::create('date_formats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('picker_format');
            $t->string('label');
        });

        Schema::create('datetime_formats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('format_moment');
            $t->string('label');
        });

        Schema::create('currencies', function ($t) {
            $t->increments('id');

            $t->string('name');
            $t->string('symbol');
            $t->string('precision');
            $t->string('thousand_separator');
            $t->string('decimal_separator');
            $t->string('code');
            $t->boolean('swap_currency_symbol')->default(false);
        });

        Schema::create('sizes', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('industries', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('accounts', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('timezone_id')->nullable();
            $t->unsignedInteger('date_format_id')->nullable();
            $t->unsignedInteger('datetime_format_id')->nullable();
            $t->unsignedInteger('currency_id')->nullable();

            $t->string('name')->nullable();
            $t->string('ip');
            $t->string('account_key')->unique();
            $t->timestamp('last_login')->nullable();

            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('postal_code')->nullable();
            $t->unsignedInteger('country_id')->nullable();
            $t->string('id_number')->nullable();
            $t->string('vat_number')->nullable();
            $t->string('work_phone')->nullable();
            $t->string('work_email')->nullable();

            $t->unsignedInteger('industry_id')->nullable();
            $t->unsignedInteger('size_id')->nullable();
            $t->string('website')->nullable();

            $t->smallInteger('direction_reminder1')->default(1);
            $t->smallInteger('direction_reminder2')->default(1);
            $t->smallInteger('direction_reminder3')->default(1);

            $t->smallInteger('field_reminder1')->default(1);
            $t->smallInteger('field_reminder2')->default(1);
            $t->smallInteger('field_reminder3')->default(1);

            $t->smallInteger('token_billing_type_id')->default(TOKEN_BILLING_ALWAYS);

            $t->unsignedInteger('default_tax_rate_id')->nullable();
            $t->smallInteger('recurring_hour')->default(DEFAULT_SEND_RECURRING_HOUR);

            $t->string('invoice_number_pattern')->nullable();
            $t->string('quote_number_pattern')->nullable();

            $t->string('invoice_number_prefix')->nullable();
            $t->integer('invoice_number_counter')->default(1)->nullable();

            $t->string('quote_number_prefix')->nullable();
            $t->integer('quote_number_counter')->default(1)->nullable();

            $t->text('invoice_footer')->nullable();
            $t->text('invoice_labels')->nullable();

            $t->boolean('share_counter')->default(true);
            $t->smallInteger('pdf_email_attachment')->default(0);
            $t->boolean('utf8_invoices')->default(true);
            $t->boolean('auto_wrap')->default(false);
            $t->string('subdomain')->nullable();

            $t->boolean('auto_convert_quote')->default(true);

            $t->unsignedInteger('header_font_id')->default(1);
            $t->unsignedInteger('body_font_id')->default(1);

            $t->smallInteger('font_size')->default(DEFAULT_FONT_SIZE);
            $t->integer('invoice_number_counter')->default(1)->nullable();
            $t->integer('quote_number_counter')->default(1)->nullable();
            $t->boolean('show_item_taxes')->default(0);

            $t->string('email_subject_invoice')->nullable();
            $t->string('email_subject_quote')->nullable();
            $t->string('email_subject_payment')->nullable();

            $t->string('email_subject_reminder1')->nullable();
            $t->string('email_subject_reminder2')->nullable();
            $t->string('email_subject_reminder3')->nullable();

            $t->text('email_template_reminder1')->nullable();
            $t->text('email_template_reminder2')->nullable();
            $t->text('email_template_reminder3')->nullable();

            $t->boolean('enable_reminder1')->default(false);
            $t->boolean('enable_reminder2')->default(false);
            $t->boolean('enable_reminder3')->default(false);

            $t->smallInteger('num_days_reminder1')->default(7);
            $t->smallInteger('num_days_reminder2')->default(14);
            $t->smallInteger('num_days_reminder3')->default(30);

            $t->text('email_template_invoice')->nullable();
            $t->text('email_template_quote')->nullable();
            $t->text('email_template_payment')->nullable();

            $t->text('invoice_terms')->nullable();
            $t->text('quote_terms')->nullable();
            $t->text('email_footer')->nullable();
            $t->text('client_view_css')->nullable();

            $t->date('pro_plan_paid')->nullable();

            $t->boolean('invoice_taxes')->default(true);
            $t->boolean('invoice_item_taxes')->default(false);
            $t->boolean('enable_buy_now_buttons')->default(false);

            $t->smallInteger('email_design_id')->default(1);
            $t->boolean('enable_email_markup')->default(false);

            $t->unsignedInteger('quote_design_id')->default(1);
            $t->mediumText('custom_design1')->nullable();
            $t->mediumText('custom_design2')->nullable();
            $t->mediumText('custom_design3')->nullable();

            $t->string('custom_label1')->nullable();
            $t->string('custom_value1')->nullable();

            $t->string('custom_label2')->nullable();
            $t->string('custom_value2')->nullable();

            $t->string('custom_client_label1')->nullable();
            $t->string('custom_client_label2')->nullable();

            $t->string('custom_invoice_text_label1')->nullable();
            $t->string('custom_invoice_text_label2')->nullable();

            $t->boolean('hide_quantity')->default(0);
            $t->boolean('hide_paid_to_date')->default(0);

            $t->string('analytics_key')->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('timezone_id')->references('id')->on('timezones');
            $t->foreign('date_format_id')->references('id')->on('date_formats');
            $t->foreign('datetime_format_id')->references('id')->on('datetime_formats');
            $t->foreign('country_id')->references('id')->on('countries');
            $t->foreign('currency_id')->references('id')->on('currencies');
            $t->foreign('industry_id')->references('id')->on('industries');
            $t->foreign('size_id')->references('id')->on('sizes');
        });

        Schema::create('gateways', function ($t) {
            $t->increments('id');

            $t->string('name');
            $t->string('provider');
            $t->boolean('visible')->default(true);
            $t->boolean('recommended')->default(0);
            $t->unsignedInteger('sort_order')->default(10000);
            $t->string('site_url', 200)->nullable();
            $t->timestamps();
        });

        Schema::create('users', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('public_id')->nullable();

            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('phone')->nullable();
            $t->string('username')->unique();
            $t->string('email')->nullable();
            $t->string('password');
            $t->string('confirmation_code')->nullable();
            $t->string('remember_token', 100)->nullable();
            $t->smallInteger('failed_logins')->nullable();

            $t->string('oauth_user_id')->nullable();
            $t->unsignedInteger('oauth_provider_id')->nullable();

            $t->boolean('registered')->default(false);
            $t->boolean('confirmed')->default(false);
            $t->string('referral_code')->nullable();
            $t->boolean('dark_mode')->default(false)->nullable();

            $t->boolean('notify_sent')->default(true);
            $t->boolean('notify_viewed')->default(false);
            $t->boolean('notify_paid')->default(true);
            $t->boolean('notify_approved')->default(true);
            $t->boolean('force_pdfjs')->default(false);
            $t->unsignedInteger('news_feed_id')->nullable();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $t->timestamps();
            $t->softDeletes();

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('account_gateways', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('gateway_id');
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->index();

            $t->text('config');
            $t->unsignedInteger('accepted_credit_cards')->nullable();
            $t->boolean('show_address')->default(true)->nullable();
            $t->boolean('update_address')->default(true)->nullable();
            $t->boolean('require_cvv')->default(true)->nullable();
            $t->boolean('show_shipping_address')->default(false)->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('gateway_id')->references('id')->on('gateways');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('password_resets', function ($t) {
            $t->string('email');
            $t->string('token');

            $t->timestamps();
        });

        Schema::create('clients', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('currency_id')->nullable();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('industry_id')->nullable();
            $t->unsignedInteger('size_id')->nullable();
            $t->unsignedInteger('public_id')->index();

            $t->string('name')->nullable();
            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('postal_code')->nullable();
            $t->unsignedInteger('country_id')->nullable();
            $t->string('work_phone')->nullable();
            $t->string('vat_number')->nullable();
            $t->string('id_number')->nullable();
            $t->decimal('balance', 13, 2)->nullable();
            $t->decimal('paid_to_date', 13, 2)->nullable();
            $t->integer('payment_terms')->nullable();
            $t->timestamp('last_login')->nullable();
            $t->string('primary_color')->nullable();
            $t->string('secondary_color')->nullable();

            $t->string('custom_value1')->nullable();
            $t->string('custom_value2')->nullable();
            $t->text('public_notes')->nullable();
            $t->text('private_notes')->nullable();

            $t->boolean('is_deleted')->default(false);

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $t->foreign('country_id')->references('id')->on('countries');
            $t->foreign('industry_id')->references('id')->on('industries');
            $t->foreign('size_id')->references('id')->on('sizes');
            $t->foreign('currency_id')->references('id')->on('currencies');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('contacts', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('client_id')->index();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->nullable();

            $t->boolean('is_primary')->default(0);
            $t->boolean('send_invoice')->default(0);
            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->timestamp('last_login')->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('invoice_statuses', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('frequencies', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('invoices', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('client_id')->index();
            $t->unsignedInteger('recurring_invoice_id')->index()->nullable();
            $t->unsignedInteger('invoice_status_id')->default(1);
            $t->unsignedInteger('invoice_type_id')->comment('was: is_quote, was: boolean');
            $t->unsignedInteger('quote_id')->nullable();
            $t->unsignedInteger('quote_invoice_id')->nullable();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->index();

            $t->string('invoice_number');
            $t->float('discount');
            $t->string('po_number');
            $t->date('invoice_date')->nullable();
            $t->date('due_date')->nullable();
            $t->boolean('is_amount_discount')->nullable();
            $t->boolean('is_recurring')->default(false);
            $t->unsignedInteger('frequency_id');
            $t->boolean('auto_bill')->default(false);
            $t->boolean('has_tasks')->default(false);
            $t->boolean('has_expenses')->default(false);
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->timestamp('last_sent_date')->nullable();

            $t->string('tax_name1');
            $t->decimal('tax_rate1', 13, 3);

            $t->decimal('amount', 13, 2);
            $t->decimal('balance', 13, 2);
            $t->decimal('partial', 13, 2)->nullable();
            $t->text('terms');
            $t->text('invoice_footer')->nullable();

            $t->decimal('custom_value1', 13, 2)->default(0);
            $t->decimal('custom_value2', 13, 2)->default(0);

            $t->boolean('custom_taxes1')->default(0);
            $t->boolean('custom_taxes2')->default(0);

            $t->string('custom_text_value1')->nullable();
            $t->string('custom_text_value2')->nullable();

            $t->text('public_notes');
            $t->text('private_notes')->nullable();
            $t->boolean('is_deleted')->default(false);

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $t->foreign('invoice_status_id')->references('id')->on('invoice_statuses');
            $t->foreign('recurring_invoice_id')->references('id')->on('invoices')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
            $t->unique(['account_id', 'invoice_number']);
        });

        Schema::create('invitations', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('invoice_id')->index();
            $t->unsignedInteger('contact_id');
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->index();
            $t->string('invitation_key')->index()->unique();

            $t->string('transaction_reference')->nullable();
            $t->timestamp('sent_date')->nullable();
            $t->timestamp('viewed_date')->nullable();

            $t->timestamp('opened_date')->nullable();
            $t->string('message_id')->nullable();
            $t->text('email_error')->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $t->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('tax_rates', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id');

            $t->string('name');
            $t->decimal('rate', 13, 3);

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('products', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id');

            $t->string('product_key');
            $t->text('notes');
            $t->decimal('cost', 13, 2);
            $t->decimal('qty', 13, 2)->nullable();

            $t->unsignedInteger('default_tax_rate_id')->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('invoice_items', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('invoice_id')->index();
            $t->unsignedInteger('product_id')->nullable();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id');

            $t->string('product_key');
            $t->text('notes');
            $t->decimal('cost', 13, 2);
            $t->decimal('qty', 13, 2)->nullable();

            $t->string('tax_name1')->nullable();
            $t->decimal('tax_rate1', 13, 3)->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('payments', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('invoice_id')->index();
            $t->unsignedInteger('client_id')->index();
            $t->unsignedInteger('contact_id')->nullable();
            $t->unsignedInteger('invitation_id')->nullable();
            $t->unsignedInteger('account_gateway_id')->nullable();
            $t->unsignedInteger('payment_type_id')->nullable();
            $t->unsignedInteger('user_id')->nullable();
            $t->unsignedInteger('public_id')->index();

            $t->decimal('amount', 13, 2);
            $t->date('payment_date')->nullable();
            $t->string('transaction_reference')->nullable();
            $t->string('payer_id')->nullable();
            $t->text('private_notes')->nullable();
            $t->boolean('is_deleted')->default(false);

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $t->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $t->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $t->foreign('account_gateway_id')->references('id')->on('account_gateways')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $t->foreign('payment_type_id')->references('id')->on('payment_types');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('credits', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('client_id')->index();
            $t->unsignedInteger('user_id');
            $t->unsignedInteger('public_id')->index();

            $t->decimal('amount', 13, 2);
            $t->decimal('balance', 13, 2);
            $t->date('credit_date')->nullable();
            $t->string('credit_number')->nullable();

            $t->boolean('is_deleted')->default(false);

            $t->text('private_notes');

            $t->timestamps();
            $t->softDeletes();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unique(['account_id', 'public_id']);
        });

        Schema::create('activities', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id');
            $t->unsignedInteger('client_id')->nullable();
            $t->unsignedInteger('contact_id')->nullable();
            $t->unsignedInteger('payment_id')->nullable();
            $t->unsignedInteger('invoice_id')->nullable();
            $t->unsignedInteger('credit_id')->nullable();
            $t->unsignedInteger('invitation_id')->nullable();
            $t->unsignedInteger('token_id')->nullable();
            $t->unsignedInteger('user_id');

            $t->boolean('is_system')->default(0);
            $t->text('message')->nullable();
            $t->text('json_backup')->nullable();
            $t->integer('activity_type_id');
            $t->decimal('adjustment', 13, 2)->nullable();
            $t->decimal('balance', 13, 2)->nullable();
            $t->string('ip')->nullable();

            $t->timestamps();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('password_reminders');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('users');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('timezones');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('date_formats');
        Schema::dropIfExists('datetime_formats');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('payment_types');
    }
}

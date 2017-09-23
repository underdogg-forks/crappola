<?php
use Illuminate\Database\Migrations\Migration;

class ConfideSetupUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('lookup__payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('customers__credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('customers__payments');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('invoices__items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('lookup__taxrates');
        Schema::dropIfExists('relations__contacts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('password_reminders');
        Schema::dropIfExists('relations');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('lookup__currencies');
        Schema::dropIfExists('lookup__invoicestatuses');
        Schema::dropIfExists('lookup__countries');
        Schema::dropIfExists('lookup__timezones');
        Schema::dropIfExists('lookup__frequencies');
        Schema::dropIfExists('lookup__dateformats');
        Schema::dropIfExists('lookup__datetimeformats');
        Schema::dropIfExists('lookup__sizes');
        Schema::dropIfExists('lookup__industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('lookup__payment_types');
        Schema::create('lookup__countries', function ($table) {
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
        });
        Schema::create('themes', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('lookup__payment_types', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('lookup__payment_terms', function ($t) {
            $t->increments('id');
            $t->integer('num_days');
            $t->string('name');
        });
        Schema::create('lookup__timezones', function ($t) {
            $t->increments('id');
            $t->string('name');
            $t->string('location');
        });
        Schema::create('lookup__dateformats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('picker_format');
            $t->string('label');
        });
        Schema::create('lookup__datetimeformats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('label');
        });
        Schema::create('lookup__currencies', function ($t) {
            $t->increments('id');
            $t->string('name');
            $t->string('symbol');
            $t->string('precision');
            $t->string('thousand_separator');
            $t->string('decimal_separator');
            $t->string('code');
        });
        Schema::create('lookup__sizes', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('lookup__industries', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('companies', function ($t) {
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
            $t->text('invoice_terms')->nullable();
            $t->text('email_footer')->nullable();
            $t->unsignedInteger('industry_id')->nullable();
            $t->unsignedInteger('size_id')->nullable();
            $t->boolean('invoice_taxes')->default(true);
            $t->boolean('invoice_item_taxes')->default(false);
            $t->timestamps();
            $t->softDeletes();
            //$t->foreign('timezone_id')->references('id')->on('lookup__timezones');
            //$t->foreign('date_format_id')->references('id')->on('lookup__dateformats');
            //$t->foreign('datetime_format_id')->references('id')->on('lookup__datetimeformats');
            //$t->foreign('country_id')->references('id')->on('lookup__countries');
            //$t->foreign('currency_id')->references('id')->on('lookup__currencies');
            //$t->foreign('industry_id')->references('id')->on('lookup__industries');
            //$t->foreign('size_id')->references('id')->on('lookup__sizes');
        });
        Schema::create('gateways', function ($t) {
            $t->increments('id');

            $t->string('name');
            $t->string('provider');
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
        });
        Schema::create('staff', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id')->index();
            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('phone')->nullable();
            $t->string('username')->unique();
            $t->string('email')->nullable();
            $t->string('password');
            $t->string('confirmation_code')->nullable();
            $t->boolean('registered')->default(false);
            $t->boolean('confirmed')->default(false);
            $t->integer('theme_id')->nullable();
            $t->boolean('notify_sent')->default(true);
            $t->boolean('notify_viewed')->default(false);
            $t->boolean('notify_paid')->default(true);
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $t->unsignedInteger('public_id')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('account_gateways', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('gateway_id');
            $t->text('config');
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('gateway_id')->references('id')->on('gateways');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);

        });
        Schema::create('password_reminders', function ($t) {
            $t->string('email');
            $t->string('token');
            $t->timestamps();
        });
        Schema::create('relations', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('currency_id')->nullable();
            $t->string('name')->nullable();
            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('postal_code')->nullable();
            $t->unsignedInteger('country_id')->nullable();
            $t->string('work_phone')->nullable();
            $t->text('private_notes')->nullable();
            $t->decimal('balance', 13, 2)->nullable();
            $t->decimal('paid_to_date', 13, 2)->nullable();
            $t->timestamp('last_login')->nullable();
            $t->string('website')->nullable();
            $t->unsignedInteger('industry_id')->nullable();
            $t->unsignedInteger('size_id')->nullable();
            $t->boolean('is_deleted')->default(false);
            $t->integer('lookup__payment_terms')->nullable();
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('country_id')->references('id')->on('lookup__countries');
            //$t->foreign('industry_id')->references('id')->on('lookup__industries');
            //$t->foreign('size_id')->references('id')->on('lookup__sizes');
            //$t->foreign('currency_id')->references('id')->on('lookup__currencies');
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });

        Schema::create('customers', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('public_id')->index();
            $t->unsignedInteger('currency_id')->nullable();
            $t->string('name')->nullable();
            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('postal_code')->nullable();
            $t->unsignedInteger('country_id')->nullable();
            $t->string('work_phone')->nullable();
            $t->text('private_notes')->nullable();
            $t->decimal('balance', 13, 2)->nullable();
            $t->decimal('paid_to_date', 13, 2)->nullable();
            $t->timestamp('last_login')->nullable();
            $t->string('website')->nullable();
            $t->unsignedInteger('industry_id')->nullable();
            $t->unsignedInteger('size_id')->nullable();
            $t->boolean('is_deleted')->default(false);
            $t->integer('lookup__payment_terms')->nullable();
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('country_id')->references('id')->on('lookup__countries');
            //$t->foreign('industry_id')->references('id')->on('lookup__industries');
            //$t->foreign('size_id')->references('id')->on('lookup__sizes');
            //$t->foreign('currency_id')->references('id')->on('lookup__currencies');

            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });


        Schema::create('relations__contacts', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('customer_id')->index();
            $t->boolean('is_primary')->default(0);
            $t->boolean('send_invoice')->default(0);
            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->timestamp('last_login')->nullable();
            //$t->foreign('customer_id')->references('id')->on('relations')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            $t->unsignedInteger('public_id')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('lookup__invoicestatuses', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('lookup__frequencies', function ($t) {
            $t->increments('id');
            $t->string('name');
        });
        Schema::create('invoices', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('customer_id')->index();
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('invoice_status_id')->default(1);
            $t->string('invoice_number');
            $t->float('discount');
            $t->string('po_number');
            $t->date('invoice_date')->nullable();
            $t->date('due_date')->nullable();
            $t->text('terms');
            $t->text('public_notes');
            $t->boolean('is_deleted')->default(false);
            $t->boolean('is_recurring')->default(false);
            $t->unsignedInteger('frequency_id');
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->timestamp('last_sent_date')->nullable();
            $t->unsignedInteger('recurring_invoice_id')->index()->nullable();
            $t->string('tax_name1');
            $t->decimal('tax_rate1', 13, 3);
            $t->decimal('amount', 13, 2);
            $t->decimal('balance', 13, 2);
            //$t->foreign('customer_id')->references('id')->on('relations')->onDelete('cascade');
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('invoice_status_id')->references('id')->on('lookup__invoicestatuses');
            //$t->foreign('recurring_invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
            $t->unique(['company_id', 'invoice_number']);
        });
        Schema::create('invitations', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('contact_id');
            $t->unsignedInteger('invoice_id')->index();
            $t->string('invitation_key')->index()->unique();
            $t->string('transaction_reference')->nullable();
            $t->timestamp('sent_date')->nullable();
            $t->timestamp('viewed_date')->nullable();
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            //$t->foreign('contact_id')->references('id')->on('relations__contacts')->onDelete('cascade');
            //$t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('lookup__taxrates', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('staff_id');
            $t->string('name');
            $t->decimal('rate', 13, 3);
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            $t->unsignedInteger('public_id');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('products', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('staff_id');
            $t->string('product_key');
            $t->text('notes');
            $t->decimal('cost', 13, 2);
            $t->decimal('qty', 13, 2)->nullable();
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            $t->unsignedInteger('public_id');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('invoices__items', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('invoice_id')->index();
            $t->unsignedInteger('product_id')->nullable();
            $t->string('product_key');
            $t->text('notes');
            $t->decimal('cost', 13, 2);
            $t->decimal('qty', 13, 2)->nullable();
            $t->string('tax_name1')->nullable();
            $t->decimal('tax_rate1', 13, 3)->nullable();
            //$t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            //$t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            $t->unsignedInteger('public_id');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('customers__payments', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('invoice_id')->index();
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('customer_id')->index();
            $t->unsignedInteger('contact_id')->nullable();
            $t->unsignedInteger('invitation_id')->nullable();
            $t->unsignedInteger('staff_id')->nullable();
            $t->unsignedInteger('account_gateway_id')->nullable();
            $t->unsignedInteger('payment_type_id')->nullable();
            $t->boolean('is_deleted')->default(false);
            $t->decimal('amount', 13, 2);
            $t->date('payment_date')->nullable();
            $t->string('transaction_reference')->nullable();
            $t->string('payer_id')->nullable();
            //$t->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('customer_id')->references('id')->on('relations')->onDelete('cascade');
            //$t->foreign('contact_id')->references('id')->on('relations__contacts')->onDelete('cascade');
            //$t->foreign('account_gateway_id')->references('id')->on('account_gateways')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            //$t->foreign('payment_type_id')->references('id')->on('lookup__payment_types');
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('customers__credits', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('customer_id')->index();
            $t->unsignedInteger('staff_id');
            $t->boolean('is_deleted')->default(false);
            $t->decimal('amount', 13, 2);
            $t->decimal('balance', 13, 2);
            $t->date('credit_date')->nullable();
            $t->string('credit_number')->nullable();
            $t->text('private_notes');
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$t->foreign('customer_id')->references('id')->on('relations')->onDelete('cascade');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');;
            $t->unsignedInteger('public_id')->index();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('activities', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('customer_id')->nullable();
            $t->unsignedInteger('contact_id')->nullable();
            $t->unsignedInteger('payment_id')->nullable();
            $t->unsignedInteger('invoice_id')->nullable();
            $t->unsignedInteger('credit_id')->nullable();
            $t->unsignedInteger('invitation_id')->nullable();
            $t->text('message')->nullable();
            $t->text('json_backup')->nullable();
            $t->integer('activity_type_id');
            $t->decimal('adjustment', 13, 2)->nullable();
            $t->decimal('balance', 13, 2)->nullable();
            $t->timestamps();
            $t->softDeletes();
            //$t->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lookup__payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('customers__credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('customers__payments');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('invoices__items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('lookup__taxrates');
        Schema::dropIfExists('relations__contacts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('password_reminders');
        Schema::dropIfExists('relations');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('lookup__currencies');
        Schema::dropIfExists('lookup__invoicestatuses');
        Schema::dropIfExists('lookup__countries');
        Schema::dropIfExists('lookup__timezones');
        Schema::dropIfExists('lookup__frequencies');
        Schema::dropIfExists('lookup__dateformats');
        Schema::dropIfExists('lookup__datetimeformats');
        Schema::dropIfExists('lookup__sizes');
        Schema::dropIfExists('lookup__industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('lookup__payment_types');
    }
}

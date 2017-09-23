<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('relation_id');
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('name')->nullable();
            $table->string('address1');
            $table->string('address2');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->unsignedInteger('country_id')->nullable();
            $table->string('work_phone');
            $table->text('private_notes');
            $table->string('website');
            $table->tinyInteger('is_deleted')->default(0);
            $table->integer('public_id')->default(0);
            $table->string('vat_number')->nullable();
            $table->string('id_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$table->foreign('country_id')->references('id')->on('lookup__countries');
            //$table->foreign('currency_id')->references('id')->on('lookup__currencies');
        });
        Schema::create('vendors__contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('vendor_id')->index();
            $table->boolean('is_primary')->default(0);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            //$table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            //$table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedInteger('public_id')->nullable();
            $table->unique(['company_id', 'public_id']);
            $table->timestamps();
            $table->softDeletes();


        });




        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->decimal('amount', 13, 2);
            $table->decimal('foreign_amount', 13, 2);
            $table->decimal('exchange_rate', 13, 4);
            $table->date('expense_date')->nullable();
            $table->text('private_notes');
            $table->text('public_notes');
            $table->unsignedInteger('invoice_currency_id')->nullable(false);
            $table->boolean('should_be_invoiced')->default(true);
            // Relations
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            // Indexes
            $table->unsignedInteger('public_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'public_id']);
        });
        Schema::table('lookup__payment_terms', function (Blueprint $table) {
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('public_id')->index();
            $table->timestamps();
            $table->softDeletes();
            ////$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            ////$table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$table->unique(array('company_id', 'public_id'));
        });
        // Update public id
        $paymentTerms = DB::table('lookup__payment_terms')
            ->where('public_id', '=', 0)
            ->select('id', 'public_id')
            ->get();
        $i = 1;
        foreach ($paymentTerms as $pTerm) {
            $data = ['public_id' => $i++];
            DB::table('lookup__payment_terms')->where('id', $pTerm->id)->update($data);
        }
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('has_expenses')->default(false);
        });
        Schema::table('lookup__payment_terms', function (Blueprint $table) {
            $table->unique(['company_id', 'public_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('expenses');
        Schema::drop('vendor_contacts');
        Schema::drop('vendors');
    }
}

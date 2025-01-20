tu<?php

use Illuminate\Database\Migrations\Migration;

class CreatePaymentLibraries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_libraries');

        Schema::create('payment_libraries', function ($t): void {
            $t->increments('id');

            $t->string('name', 100);
            $t->boolean('visible')->default(true);

            $t->timestamps();
        });

        Schema::table('gateways', function ($table): void {
            $table->unsignedInteger('payment_library_id')->default(1);
        });

        DB::table('gateways')->update(['payment_library_id' => 1]);

        Schema::table('gateways', function ($table): void {
            $table->foreign('payment_library_id')->references('id')->on('payment_libraries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('gateways', 'payment_library_id')) {
            Schema::table('gateways', function ($table): void {
                $table->dropForeign('gateways_payment_library_id_foreign');
                $table->dropColumn('payment_library_id');
            });
        }

        Schema::dropIfExists('payment_libraries');
    }
}

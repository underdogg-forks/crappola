<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLibraries extends Migration
{
    public function up(): void
    {
        Schema::create('payment_libraries', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::table('gateways', function ($table): void {
            $table->unsignedInteger('payment_library_id')->default(1);
        });

        DB::table('gateways')->update(['payment_library_id' => 1]);

        Schema::table('gateways', function ($table): void {
            $table->foreign('payment_library_id')->references('id')->on('payment_libraries')->onDelete('cascade');
        });
    }

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

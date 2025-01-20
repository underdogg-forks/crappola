<?php

use Illuminate\Database\Migrations\Migration;

class AddCustomFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('custom_label1')->nullable();
            $table->string('custom_value1')->nullable();

            $table->string('custom_label2')->nullable();
            $table->string('custom_value2')->nullable();

            $table->string('custom_client_label1')->nullable();
            $table->string('custom_client_label2')->nullable();
        });

        Schema::table('clients', function ($table): void {
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('custom_label1');
            $table->dropColumn('custom_value1');

            $table->dropColumn('custom_label2');
            $table->dropColumn('custom_value2');

            $table->dropColumn('custom_client_label1');
            $table->dropColumn('custom_client_label2');
        });

        Schema::table('clients', function ($table): void {
            $table->dropColumn('custom_value1');
            $table->dropColumn('custom_value2');
        });
    }
}

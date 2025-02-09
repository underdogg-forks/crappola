<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('gateways', function ($table): void {
            $table->unsignedInteger('sort_order')->default(10000);
            $table->boolean('recommended')->default(0);
            $table->string('site_url', 200)->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('gateways', 'sort_order')) {
            Schema::table('gateways', function ($table): void {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('gateways', 'recommended')) {
            Schema::table('gateways', function ($table): void {
                $table->dropColumn('recommended');
            });
        }

        if (Schema::hasColumn('gateways', 'site_url')) {
            Schema::table('gateways', function ($table): void {
                $table->dropColumn('site_url');
            });
        }
    }
};

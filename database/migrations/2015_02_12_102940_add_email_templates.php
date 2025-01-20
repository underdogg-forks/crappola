<?php

use Illuminate\Database\Migrations\Migration;

class AddEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->text('email_template_invoice')->nullable();
            $table->text('email_template_quote')->nullable();
            $table->text('email_template_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('companies', 'email_template_invoice')) {
            Schema::table('companies', function ($table): void {
                $table->dropColumn('email_template_invoice');
                $table->dropColumn('email_template_quote');
                $table->dropColumn('email_template_payment');
            });
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class AddInvoiceSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if ( ! Schema::hasColumn('invitations', 'signature_base64')) {
            Schema::table('invitations', function ($table): void {
                $table->text('signature_base64')->nullable();
                $table->timestamp('signature_date')->nullable();
            });

            Schema::table('companies', function ($table): void {
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
            });

            if (Utils::isNinja()) {
                Schema::table('payment_methods', function ($table): void {
                    $table->unsignedInteger('account_gateway_token_id')->nullable()->change();
                });

                // This may fail if the foreign key doesn't exist
                try {
                    Schema::table('payment_methods', function ($table): void {
                        $table->dropForeign('payment_methods_account_gateway_token_id_foreign');
                    });
                } catch (Exception $e) {
                    // do nothing
                }

                Schema::table('payment_methods', function ($table): void {
                    $table->foreign('account_gateway_token_id')->references('id')->on('account_gateway_tokens')->onDelete('cascade');
                });

                Schema::table('payments', function ($table): void {
                    $table->dropForeign('payments_payment_method_id_foreign');
                });

                Schema::table('payments', function ($table): void {
                    $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invitations', function ($table): void {
            $table->dropColumn('signature_base64');
            $table->dropColumn('signature_date');
        });

        Schema::table('companies', function ($table): void {
            $table->dropColumn('utm_source');
            $table->dropColumn('utm_medium');
            $table->dropColumn('utm_campaign');
            $table->dropColumn('utm_term');
            $table->dropColumn('utm_content');
        });
    }
}

<?php

use App\Libraries\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceSignature extends Migration
{
    public function up()
    {
        if ( ! Schema::hasColumn('invitations', 'signature_base64')) {
            Schema::table('invitations', function ($table) {
                $table->text('signature_base64')->nullable();
                $table->timestamp('signature_date')->nullable();
            });

            Schema::table('companies', function ($table) {
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
            });

            if (Utils::isNinja()) {
                Schema::table('payment_methods', function ($table) {});

                // This may fail if the foreign key doesn't exist
                /*try {
                    Schema::table('payment_methods', function ($table) {
                        $table->dropForeign('payment_methods_account_gateway_token_id_foreign');
                    });
                } catch (Exception $e) {
                    // do nothing
                }*/

                Schema::table('payment_methods', function ($table) {});

                Schema::table('payments', function ($table) {
                    $table->dropForeign('payments_payment_method_id_foreign');
                });

                Schema::table('payments', function ($table) {
                    $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
                });
            }
        }
    }

    public function down()
    {
        Schema::table('invitations', function ($table) {
            $table->dropColumn('signature_base64');
            $table->dropColumn('signature_date');
        });

        Schema::table('companies', function ($table) {
            $table->dropColumn('utm_source');
            $table->dropColumn('utm_medium');
            $table->dropColumn('utm_campaign');
            $table->dropColumn('utm_term');
            $table->dropColumn('utm_content');
        });
    }
}

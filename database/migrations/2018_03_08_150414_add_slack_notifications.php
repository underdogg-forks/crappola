<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlackNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('activities', function ($table): void {
            $table->integer('task_id')->unsigned()->change();
            $table->integer('client_id')->unsigned()->nullable()->change();
        });

        DB::statement('UPDATE activities SET client_id = NULL WHERE client_id = 0');

        if ( ! Schema::hasColumn('users', 'slack_webhook_url')) {
            Schema::table('users', function ($table): void {
                $table->string('slack_webhook_url')->nullable();
                $table->string('accepted_terms_version')->nullable();
                $table->timestamp('accepted_terms_timestamp')->nullable();
                $table->string('accepted_terms_ip')->nullable();
            });
        }

        if ( ! Schema::hasColumn('accounts', 'auto_archive_invoice')) {
            Schema::table('accounts', function ($table): void {
                $table->boolean('auto_archive_invoice')->default(false)->nullable();
                $table->boolean('auto_archive_quote')->default(false)->nullable();
                $table->boolean('auto_email_invoice')->default(true)->nullable();
                $table->boolean('send_item_details')->default(false)->nullable();
            });
        }

        try {
            Schema::table('expenses', function ($table): void {
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            });

            Schema::table('activities', function ($table): void {
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->index('payment_id');
            });

            Schema::table('companies', function ($table): void {
                $table->dropForeign('companies_payment_id_foreign');
            });

            Schema::table('companies', function ($table): void {
                $table->index('payment_id');
            });

            Schema::table('user_accounts', function ($table): void {
                $table->dropForeign('user_accounts_user_id1_foreign');
                $table->dropForeign('user_accounts_user_id2_foreign');
                $table->dropForeign('user_accounts_user_id3_foreign');
                $table->dropForeign('user_accounts_user_id4_foreign');
                $table->dropForeign('user_accounts_user_id5_foreign');
            });

            Schema::table('user_accounts', function ($table): void {
                $table->index('user_id1');
                $table->index('user_id2');
                $table->index('user_id3');
                $table->index('user_id4');
                $table->index('user_id5');
            });
        } catch (Exception $exception) {
            // do nothing, change only needed for invoiceninja servers
        }

        Schema::table('jobs', function (Blueprint $table): void {
            $table->dropIndex('jobs_queue_reserved_reserved_at_index');
            $table->dropColumn('reserved');
            $table->index(['queue', 'reserved_at']);
        });

        Schema::table('failed_jobs', function (Blueprint $table): void {
            $table->longText('exception')->after('payload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function ($table): void {
            $table->dropColumn('slack_webhook_url');
            $table->dropColumn('accepted_terms_version');
            $table->dropColumn('accepted_terms_timestamp');
            $table->dropColumn('accepted_terms_ip');
        });

        Schema::table('accounts', function ($table): void {
            $table->dropColumn('auto_archive_invoice');
            $table->dropColumn('auto_archive_quote');
            $table->dropColumn('auto_email_invoice');
            $table->dropColumn('send_item_details');
        });

        Schema::table('jobs', function (Blueprint $table): void {
            $table->tinyInteger('reserved')->unsigned();
            $table->index(['queue', 'reserved', 'reserved_at']);
            $table->dropIndex('jobs_queue_reserved_at_index');
        });

        Schema::table('failed_jobs', function (Blueprint $table): void {
            $table->dropColumn('exception');
        });
    }
}

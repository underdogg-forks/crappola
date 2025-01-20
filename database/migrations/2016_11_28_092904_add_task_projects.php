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
        Schema::create('projects', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('client_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->nullable();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedInteger('public_id')->index();
            $table->unique(['account_id', 'public_id']);
        });

        Schema::table('tasks', function ($table): void {
            $table->unsignedInteger('project_id')->nullable()->index();

            if (Schema::hasColumn('tasks', 'description')) {
                $table->text('description')->change();
            }
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('tasks', function ($table): void {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // is_deleted to standardize tables
        Schema::table('expense_categories', function ($table): void {
            $table->boolean('is_deleted')->default(false);
        });

        Schema::table('products', function ($table): void {
            $table->boolean('is_deleted')->default(false);
        });

        // add 'delete cascase' to resolve error when deleting an account
        // This may fail if the foreign key doesn't exist
        try {
            Schema::table('account_gateway_tokens', function ($table): void {
                $table->dropForeign('account_gateway_tokens_default_payment_method_id_foreign');
            });
        } catch (Exception $e) {
            // do nothing
        }

        Schema::table('account_gateway_tokens', function ($table): void {
            $table->foreign('default_payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });

        if ( ! Schema::hasColumn('invoices', 'is_public')) {
            Schema::table('invoices', function ($table): void {
                $table->boolean('is_public')->default(false);
            });
        }

        DB::table('invoices')->update(['is_public' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tasks', function ($table): void {
            $table->dropForeign('tasks_project_id_foreign');
            $table->dropColumn('project_id');
        });

        Schema::dropIfExists('projects');

        Schema::table('expense_categories', function ($table): void {
            $table->dropColumn('is_deleted');
        });

        Schema::table('products', function ($table): void {
            $table->dropColumn('is_deleted');
        });

        Schema::table('invoices', function ($table): void {
            $table->dropColumn('is_public');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_categories', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');

            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('proposal_snippets', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');

            $table->unsignedInteger('proposal_category_id')->nullable();
            $table->string('name');
            $table->string('icon');
            $table->text('private_notes');

            $table->mediumText('html');
            $table->mediumText('css');

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('proposal_templates', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->text('private_notes');

            $table->string('name');
            $table->mediumText('html');
            $table->mediumText('css');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('proposals', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');

            $table->unsignedInteger('invoice_id')->index();
            $table->unsignedInteger('proposal_template_id')->nullable()->index();
            $table->text('private_notes');
            $table->mediumText('html');
            $table->mediumText('css');

            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('proposal_template_id')->references('id')->on('proposal_templates')->onDelete('cascade');
        });

        Schema::create('proposal_invitations', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('proposal_id')->index();
            $table->string('invitation_key')->index()->unique();

            $table->timestamp('sent_date')->nullable();
            $table->timestamp('viewed_date')->nullable();
            $table->timestamp('opened_date')->nullable();
            $table->string('message_id')->nullable();
            $table->text('email_error')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_invitations');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('proposal_templates');
        Schema::dropIfExists('proposal_snippets');
        Schema::dropIfExists('proposal_categories');
    }
}

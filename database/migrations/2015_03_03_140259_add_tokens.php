<?php
use Illuminate\Database\Migrations\Migration;

class AddTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_tokens', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('staff_id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->string('token')->unique();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->unsignedInteger('public_id')->nullable();
            $table->unique(['company_id', 'public_id']);
        });
        Schema::table('activities', function ($table) {
            $table->unsignedInteger('token_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('account_tokens');
        Schema::table('activities', function ($table) {
            $table->dropColumn('token_id');
        });
    }
}

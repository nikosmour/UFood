<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_applicants', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_id')->primary();
            $table->char('department',60);
            $table->year('first_year');
            $table->date('expiration_date');
            $table->char('permanent_address',60);
            $table->unsignedBigInteger('permanent_address_phone');
            $table->char('temporary_address',60)->nullable();
            $table->unsignedBigInteger('temporary_address__phone')->nullable();
            $table->unsignedBigInteger('cellphone')->nullable();
            $table->timestamps();
            $table->foreign('academic_id')->references('academic_id')->on('academic_citizens')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_applicants');
    }
};

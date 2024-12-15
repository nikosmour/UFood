<?php

use App\Models\CardApplication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_application_checking', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('card_application_staff_id');
            $table->foreignIdFor(CardApplication::class);
            $table->timestamps();
            $table->string('card_application_staff_comment')->nullable();
            $table->foreign('card_application_staff_id')->references('id')->on('card_application_staff')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('card_application_id')->references('id')->on('card_applications')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_application_checking');
    }
};

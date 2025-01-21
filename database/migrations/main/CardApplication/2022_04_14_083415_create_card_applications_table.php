<?php

use App\Enum\CardStatusEnum;
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
        Schema::create('card_applications', function (Blueprint $table) {
            $table->id();
            $table->string('student_comments')->nullable();
            $table->string('card_application_staff_comments')->nullable();
            $table->enum('status', CardStatusEnum::values()->toArray());
            $table->unsignedBigInteger('academic_id');
            $table->unsignedTinyInteger('card_application_staff_id')->nullable();
            $table->timestamps();
            $table->foreign('academic_id')->references('academic_id')->on('card_applicants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('card_application_staff_id')->references('id')->on('card_application_staff')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_applications');
    }
};

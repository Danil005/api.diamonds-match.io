<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('sign')->nullable();
            $table->unsignedBigInteger('partner_appearance_id')->nullable();
            $table->unsignedBigInteger('personal_qualities_partner_id')->nullable();
            $table->unsignedBigInteger('partner_information_id')->nullable();
            $table->unsignedBigInteger('test_id')->nullable();
            $table->unsignedBigInteger('my_appearance_id')->nullable();
            $table->unsignedBigInteger('my_personal_qualities_id')->nullable();
            $table->unsignedBigInteger('my_information_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('status_pay')->default('free');


            $table->foreign('partner_appearance_id')->references('id')->on('questionnaire_partner_appearances')->onDelete('cascade');
            $table->foreign('personal_qualities_partner_id')->references('id')->on('questionnaire_personal_qualities_partners')->onDelete('cascade');
            $table->foreign('partner_information_id')->references('id')->on('questionnaire_partner_information')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('questionnaire_tests')->onDelete('cascade');
            $table->foreign('my_appearance_id')->references('id')->on('questionnaire_my_appearances')->onDelete('cascade');
            $table->foreign('my_personal_qualities_id')->references('id')->on('questionnaire_my_personal_qualities')->onDelete('cascade');
            $table->foreign('my_information_id')->references('id')->on('questionnaire_my_information')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaires');
    }
}

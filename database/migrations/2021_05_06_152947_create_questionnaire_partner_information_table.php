<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnairePartnerInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_partner_information', function (Blueprint $table) {
            $table->id();
            $table->string('age');
            $table->string('place_birth');
            $table->string('city');
            $table->string('zodiac_signs');
            $table->string('height');
            $table->string('weight');
            $table->string('marital_status');
            $table->string('languages');
            $table->boolean('moving_country');
            $table->boolean('moving_city');
            $table->boolean('children');
            $table->string('children_count')->nullable();
            $table->string('children_desire');
            $table->string('smoking');
            $table->string('alcohol');
            $table->string('religion');
            $table->string('sport');
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
        Schema::dropIfExists('questionnaire_partner_information');
    }
}

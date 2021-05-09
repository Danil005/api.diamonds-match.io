<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireMyInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_my_information', function (Blueprint $table) {
            $table->id();
            $table->string('name');
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
            $table->string('education');
            $table->string('work');
            $table->string('salary');
            $table->string('health_problems');
            $table->string('allergies');
            $table->string('pets');
            $table->string('have_pets');
            $table->string('films_or_books');
            $table->string('relax');
            $table->string('countries_was');
            $table->string('countries_dream');
            $table->string('best_gift');
            $table->string('hobbies');
            $table->string('kredo');
            $table->string('features_repel');
            $table->string('age_difference');
            $table->string('films');
            $table->string('songs');
            $table->string('ideal_weekend');
            $table->string('sleep');
            $table->string('doing_10');
            $table->string('signature_dish');
            $table->string('clubs');
            $table->string('best_gift_received');
            $table->string('talents');
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
        Schema::dropIfExists('questionnaire_my_information');
    }
}

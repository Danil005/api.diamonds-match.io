<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireMyAppearancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_my_appearances', function (Blueprint $table) {
            $table->id();
            $table->string('sex');
            $table->string('ethnicity');
            $table->string('body_type');
            $table->string('chest')->nullable();
            $table->string('booty')->nullable();
            $table->string('hair_color');
            $table->string('hair_length')->nullable();
            $table->string('eye_color')->nullable();
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
        Schema::dropIfExists('questionnaire_my_appearances');
    }
}

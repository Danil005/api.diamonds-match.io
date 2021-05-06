<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_tests', function (Blueprint $table) {
            $table->id();
            $table->integer('lies');
            $table->integer('intervention');
            $table->integer('value');
            $table->integer('life');
            $table->integer('motive_marriage');
            $table->integer('family_atmosphere');
            $table->integer('position_sex');
            $table->integer('books');
            $table->integer('friends');
            $table->integer('leisure');
            $table->integer('discussion_feelings');
            $table->integer('work_relationship');
            $table->integer('family_decisions');
            $table->integer('consent');
            $table->integer('interests_partner');
            $table->integer('first_place_relationship');
            $table->integer('position_society');
            $table->integer('conflicts');
            $table->integer('cleanliness');
            $table->integer('clear_plan');
            $table->integer('conflict_behavior');
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
        Schema::dropIfExists('questionnaire_tests');
    }
}

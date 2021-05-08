<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('questions_tests', function (Blueprint $table) {
//            $table->id();
//            $table->string('field');
//            $table->string('question_ru');
//            $table->string('question_en');
//            $table->string('answers_ru');
//            $table->string('answers_en');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions_tests');
    }
}

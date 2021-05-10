<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedBigInteger('with_questionnaire_id');
            $table->float('appearance')->default(0);
            $table->float('personal_qualities')->default(0);
            $table->float('information')->default(0);
            $table->float('test')->default(0);
            $table->float('total')->default(0);

            $table->foreign('questionnaire_id')->on('questionnaires')->references('id')->onDelete('cascade');
            $table->foreign('with_questionnaire_id')->on('questionnaires')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('questionnaire_matches');
    }
}

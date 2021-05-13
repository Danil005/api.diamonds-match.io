<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnairePersonalQualitiesPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_personal_qualities_partners', function (Blueprint $table) {
            $table->id();
            $table->boolean('calm')->default(false);
            $table->boolean('energetic')->default(false);
            $table->boolean('live_in_moment')->default(false);
            $table->boolean('pragmatic')->default(false);
            $table->boolean('ambitious')->default(false);
            $table->boolean('modest')->default(false);
            $table->boolean('self')->default(false);
            $table->boolean('need_support')->default(false);
            $table->boolean('housewifely')->default(false);
            $table->boolean('indifferent_life')->default(false);
            $table->boolean('aristocratic')->default(false);
            $table->boolean('simple')->default(false);
            $table->boolean('sport')->default(false);
            $table->boolean('indifferent_sport')->default(false);
            $table->boolean('lover_going_out')->default(false);
            $table->boolean('home')->default(false);
            $table->boolean('adventuress')->default(false);
            $table->boolean('rational')->default(false);
            $table->boolean('strong-willed')->default(false);
            $table->boolean('soft')->default(false);
            $table->boolean('lark')->default(false);
            $table->boolean('owl')->default(false);
            $table->boolean('humanitarian')->default(false);
            $table->boolean('mathematical')->default(false);
            $table->boolean('open')->default(false);
            $table->boolean('cautious')->default(false);
            $table->boolean('extrovert')->default(false);
            $table->boolean('introvert')->default(false);
            $table->boolean('infantile')->default(false);
            $table->boolean('mature')->default(false);
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
        Schema::dropIfExists('questionnaire_personal_qualities_partners');
    }
}

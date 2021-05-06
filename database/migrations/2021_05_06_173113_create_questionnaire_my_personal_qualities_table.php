<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireMyPersonalQualitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_my_personal_qualities', function (Blueprint $table) {
            $table->id();
            $table->boolean('calm')->default(false);
            $table->boolean('energetic')->default(false);
            $table->boolean('happy')->default(false);
            $table->boolean('modest')->default(false);
            $table->boolean('purposeful')->default(false);
            $table->boolean('weak-willed')->default(false);
            $table->boolean('self')->default(false);
            $table->boolean('dependent')->default(false);
            $table->boolean('feminine')->default(false);
            $table->boolean('courageous')->default(false);
            $table->boolean('confident')->default(false);
            $table->boolean('delicate')->default(false);
            $table->boolean('live_here_now')->default(false);
            $table->boolean('pragmatic')->default(false);
            $table->boolean('graceful')->default(false);
            $table->boolean('sociable')->default(false);
            $table->boolean('smiling')->default(false);
            $table->boolean('housewifely')->default(false);
            $table->boolean('ambitious')->default(false);
            $table->boolean('artistic')->default(false);
            $table->boolean('good')->default(false);
            $table->boolean('aristocratic')->default(false);
            $table->boolean('stylish')->default(false);
            $table->boolean('economical')->default(false);
            $table->boolean('business')->default(false);
            $table->boolean('sports')->default(false);
            $table->boolean('fearless')->default(false);
            $table->boolean('shy')->default(false);
            $table->boolean('playful')->default(false);
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
        Schema::dropIfExists('questionnaire_my_personal_qualities');
    }
}

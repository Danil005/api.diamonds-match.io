<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterpretationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interpretations', function (Blueprint $table) {
            $table->id();
            $table->boolean('honesty');
            $table->boolean('boundaries');
            $table->boolean('values');
            $table->boolean('life_style');
            $table->boolean('motive');
            $table->boolean('atmosphere');
            $table->boolean('sex');
            $table->boolean('books');
            $table->boolean('friends');
            $table->boolean('leisure');
            $table->boolean('emotional_closeness');
            $table->boolean('family_roles');
            $table->boolean('dominance');
            $table->boolean('opinion_addiction');
            $table->boolean('interests');
            $table->boolean('language_love');
            $table->boolean('psychotypes');
            $table->boolean('conflict_resolution');
            $table->boolean('household_order');
            $table->boolean('plans');
            $table->boolean('quarrels_temperament');
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
        Schema::dropIfExists('interpretations');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('persons', function (Blueprint $table) {
          $table->id();
          $table->string('name');
          $table->string('lastname');
          $table->string('email')->unique();
          $table->string('phone')->unique();
          $table->string('position');
            $table->unsignedBigInteger('directorate_id');
          $table->foreign('directorate_id')->references('id')->on('directorates')
          ->onDelete('no action')
          ->onUpdate('cascade');
           $table->unsignedBigInteger('employment_type_id');
          $table->foreign('employment_type_id')->references('id')->on('employment_types')
          ->onDelete('no action')
          ->onUpdate('cascade');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};

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
        Schema::create('internet_users', function (Blueprint $table) {
            $table->id();
         $table->unsignedBigInteger('person_id');
          $table->unsignedBigInteger('group_id');
         $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        $table->foreign('person_id')->references('id')->on('persons')
          ->onDelete('no action')->onUpdate('cascade');
           $table->string('username')->unique();
           $table->integer('device_limit');
           $table->boolean('status')->default(true);
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internet_users');
    }
};

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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
         $table->unsignedBigInteger('internet_user_id');
                $table->unsignedBigInteger('violation_type_id');
            $table->foreign('violation_type_id')
                  ->references('id')->on('violations_types')
                  ->onDelete('no action')
                  ->onUpdate('cascade');
            $table->foreign('internet_user_id')
          ->references('id')->on('internet_users')
          ->onDelete('no action')
          ->onUpdate('cascade');
           $table->text('comment');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};

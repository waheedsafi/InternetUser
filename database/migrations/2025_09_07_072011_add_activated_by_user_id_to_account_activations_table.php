<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_activations', function (Blueprint $table) {
            // Add nullable foreign key to users table
            $table->unsignedBigInteger('activated_by_user_id')
                ->nullable()
                ->after('reason');

            $table->foreign('activated_by_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('account_activations', function (Blueprint $table) {
            // Drop foreign and column in reverse
            $table->dropForeign(['activated_by_user_id']);
            $table->dropColumn('activated_by_user_id');
        });
    }
};
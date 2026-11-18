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
        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('challenge_id')->nullable()
                  ->constrained('challenges', 'challenge_id')->nullOnDelete();
            $table->string('proof_url');
            $table->timestamp('submitted_at');
            $table->string('status')->default('approved');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
    }
};

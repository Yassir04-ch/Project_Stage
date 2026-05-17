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
        Schema::create('justifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')
                  ->constrained('absences')
                  ->onDelete('cascade');
            $table->foreignId('justified_by')   
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('type', ['medical', 'family', 'mission', 'congé', 'other']);
            $table->text('reason');
            $table->string('proof_file')->nullable(); 
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justifications');
    }
};

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
        Schema::create('office_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->string('position')->nullable(); // Position of user in the office
            $table->date('assigned_date')->nullable();
            $table->boolean('is_primary')->default(false); // If this is the primary office of user
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure a user can have only one primary office
            $table->unique(['user_id', 'office_id'], 'user_office_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_user');
    }
};

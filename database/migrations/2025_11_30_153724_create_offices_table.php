<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type'); // 'headquarters', 'regional', 'area'
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('parent_office_id')->nullable(); // For hierarchical relationship
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key for hierarchical relationship
            $table->foreign('parent_office_id')->references('id')->on('offices')->onDelete('set null');
        });

        // Insert default offices
        DB::table('offices')->insert([
            [
                'name' => 'Headquarters',
                'code' => 'HQ001',
                'type' => 'headquarters',
                'address' => 'Central Office Location',
                'phone' => '021-12345678',
                'email' => 'hq@example.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};

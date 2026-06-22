<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->string('block', 20)->nullable();
            $table->string('floor', 10)->nullable();
            $table->string('flat_no', 20);
            $table->enum('type', ['residential', 'commercial', 'parking'])->default('residential');
            $table->decimal('area_sqft', 8, 2)->nullable();
            $table->enum('status', ['occupied', 'vacant'])->default('vacant');
            $table->timestamps();

            $table->unique(['society_id', 'block', 'flat_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};

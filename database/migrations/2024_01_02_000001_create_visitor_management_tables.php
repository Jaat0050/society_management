<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('company')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('id_card_no')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visitor_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flat_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_staff_id')->nullable()->constrained('delivery_staff')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pass_code', 12)->unique();
            $table->string('qr_token', 64)->unique();
            $table->string('visitor_name');
            $table->string('phone', 20)->nullable();
            $table->string('id_proof')->nullable();
            $table->enum('visit_type', ['guest', 'delivery', 'cab', 'domestic_help', 'vendor', 'other'])->default('guest');
            $table->string('vehicle_no')->nullable();
            $table->unsignedTinyInteger('person_count')->default(1);
            $table->text('purpose')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'checked_in', 'checked_out'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('entry_at')->nullable();
            $table->timestamp('exit_at')->nullable();
            $table->timestamps();

            $table->index(['society_id', 'status']);
            $table->index(['society_id', 'entry_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_entries');
        Schema::dropIfExists('delivery_staff');
    }
};

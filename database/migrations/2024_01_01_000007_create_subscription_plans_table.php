<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('max_flats')->nullable();
            $table->integer('max_users')->nullable();
            $table->integer('duration_months')->default(12);
            $table->boolean('is_active')->default(true);
            $table->text('features')->nullable();
            $table->timestamps();
        });

        Schema::table('societies', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('is_active')->constrained()->nullOnDelete();
            $table->enum('subscription_status', ['active', 'trial', 'expired', 'cancelled'])->default('trial')->after('subscription_plan_id');
            $table->date('subscription_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('blocked_at')->nullable()->after('subscription_ends_at');
            $table->text('block_reason')->nullable()->after('blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'subscription_status', 'subscription_ends_at', 'blocked_at', 'block_reason']);
        });
        Schema::dropIfExists('subscription_plans');
    }
};

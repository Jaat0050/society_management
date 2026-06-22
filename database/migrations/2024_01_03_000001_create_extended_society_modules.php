<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('move_out_date')->nullable()->after('move_in_date');
            $table->string('id_proof_type', 50)->nullable()->after('phone');
            $table->string('id_proof_no', 50)->nullable()->after('id_proof_type');
            $table->foreignId('user_id')->nullable()->after('flat_id')->constrained()->nullOnDelete();
        });

        Schema::table('maintenance_bills', function (Blueprint $table) {
            $table->decimal('penalty_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('penalty_amount');
            $table->string('payment_mode', 30)->nullable()->after('paid_at');
            $table->string('transaction_ref', 100)->nullable()->after('payment_mode');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('assigned_staff_id')->nullable()->after('user_id')->constrained('staff')->nullOnDelete();
            $table->foreignId('assigned_vendor_id')->nullable()->after('assigned_staff_id');
        });

        Schema::table('visitor_entries', function (Blueprint $table) {
            $table->string('approval_otp', 6)->nullable()->after('qr_token');
            $table->timestamp('otp_expires_at')->nullable()->after('approval_otp');
        });

        Schema::create('member_family', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relation', 50);
            $table->string('phone', 20)->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->timestamps();
        });

        Schema::create('member_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parking_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['car', 'bike', 'other'])->default('car');
            $table->string('vehicle_no', 30);
            $table->string('make_model', 100)->nullable();
            $table->string('sticker_no', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('member_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('doc_type', ['aadhaar', 'pan', 'agreement', 'photo', 'other'])->default('other');
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('service_type', 100);
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->decimal('contract_value', 12, 2)->nullable();
            $table->text('schedule_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->foreign('assigned_vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });

        Schema::create('complaint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 80);
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_mode', 30)->default('cash');
            $table->text('description')->nullable();
            $table->string('reference_no', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_bill_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flat_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('gateway', 30)->default('manual');
            $table->string('transaction_id', 100)->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->string('category', 80);
            $table->decimal('amount', 12, 2);
            $table->date('entry_date');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'leave', 'half_day'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'attendance_date']);
        });

        Schema::create('staff_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->string('payment_month', 7);
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->string('payment_mode', 30)->default('bank');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('society_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['sms', 'email', 'push'])->default('email');
            $table->string('recipient_group', 50)->default('all_residents');
            $table->string('title');
            $table->text('body');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('parking_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parking_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_no', 30);
            $table->string('sticker_no', 30)->nullable();
            $table->enum('pass_type', ['resident', 'visitor'])->default('resident');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->string('location')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });

        Schema::create('cctv_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('location');
            $table->string('stream_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sos_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flat_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_phone', 20)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sos_alerts');
        Schema::dropIfExists('cctv_cameras');
        Schema::dropIfExists('security_incidents');
        Schema::dropIfExists('parking_passes');
        Schema::dropIfExists('emergency_alerts');
        Schema::dropIfExists('society_notifications');
        Schema::dropIfExists('staff_payments');
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('complaint_logs');
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_vendor_id']);
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn(['assigned_staff_id', 'assigned_vendor_id']);
        });
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('member_documents');
        Schema::dropIfExists('member_vehicles');
        Schema::dropIfExists('member_family');
        Schema::table('visitor_entries', function (Blueprint $table) {
            $table->dropColumn(['approval_otp', 'otp_expires_at']);
        });
        Schema::table('maintenance_bills', function (Blueprint $table) {
            $table->dropColumn(['penalty_amount', 'paid_amount', 'payment_mode', 'transaction_ref']);
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['move_out_date', 'id_proof_type', 'id_proof_no', 'user_id']);
        });
    }
};

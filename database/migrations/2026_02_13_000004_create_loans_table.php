<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('loan_code')->unique();

            $table->decimal('principal', 14, 2);
            $table->string('interest_type'); // daily | weekly
            $table->decimal('interest_rate', 5, 2); // percent
            $table->decimal('interest_amount', 14, 2)->default(0);
            $table->decimal('admin_fee', 14, 2)->default(0);
            $table->decimal('total_payable', 14, 2)->default(0);

            $table->unsignedInteger('term_count'); // jumlah angsuran
            $table->date('first_due_date');

            $table->string('status')->default('pending');

            $table->dateTime('applied_at')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_note')->nullable();

            $table->dateTime('disbursed_at')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['status', 'first_due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

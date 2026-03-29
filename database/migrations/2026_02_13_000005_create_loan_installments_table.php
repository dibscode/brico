<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->date('due_date');

            $table->decimal('principal_due', 14, 2);
            $table->decimal('interest_due', 14, 2);
            $table->decimal('total_due', 14, 2);

            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->unique(['loan_id', 'sequence']);
            $table->index(['loan_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};

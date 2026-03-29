<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_categories', function (Blueprint $table) {
            $table->string('account_code')->nullable()->after('type');
            $table->index(['account_code']);
        });
    }

    public function down(): void
    {
        Schema::table('cash_categories', function (Blueprint $table) {
            $table->dropIndex(['account_code']);
            $table->dropColumn('account_code');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_log', function (Blueprint $table): void {
            $table->string('fingerprint', 64)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('email_log', function (Blueprint $table): void {
            $table->dropUnique(['fingerprint']);
            $table->dropColumn('fingerprint');
        });
    }
};

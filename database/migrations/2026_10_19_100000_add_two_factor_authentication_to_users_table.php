<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('two_factor_required')->default(false)->after('is_active');
            $table->string('two_factor_method')->nullable()->after('two_factor_required');
            $table->boolean('has_email_authentication')->default(false)->after('two_factor_method');
            $table->text('app_authentication_secret')->nullable()->after('has_email_authentication');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'two_factor_required',
                'two_factor_method',
                'has_email_authentication',
                'app_authentication_secret',
            ]);
        });
    }
};

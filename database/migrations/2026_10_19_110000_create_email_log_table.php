<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_log', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('date')->index();
            $table->string('from')->nullable();
            $table->string('to')->nullable()->index();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->longText('headers')->nullable();
            $table->text('attachments')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_log');
    }
};

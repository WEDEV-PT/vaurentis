<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_clicks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('click_x');
            $table->unsignedSmallInteger('click_y');
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index(['project_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_clicks');
    }
};

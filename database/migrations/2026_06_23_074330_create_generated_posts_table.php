<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_blueprint_id')->constrained()->cascadeOnDelete();
            $table->longText('raw_content');
            $table->string('hook_propose', 280)->nullable();
            $table->json('body_points')->nullable();
            $table->tinyInteger('technical_readability_score')->nullable();
            $table->json('suggested_hashtags')->nullable();
            $table->text('tone_compliance_justification')->nullable();
            $table->string('status', 20)->default('draft');
            $table->index('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_posts');
    }
};

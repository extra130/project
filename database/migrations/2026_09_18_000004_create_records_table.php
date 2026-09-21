<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('module_id')->nullable()->index();

            // type: development / test / issue / note
            $table->string('type', 30)->index();
            $table->string('title', 255);
            $table->longText('content');

            // source: manual / codex / agent / api
            $table->string('source', 30)->default('manual')->index();

            $table->string('git_branch', 255)->nullable();
            $table->string('git_commit', 100)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('set null');
            $table->index(['project_id', 'module_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};

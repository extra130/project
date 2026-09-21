<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();

            $table->string('original_name', 255);
            $table->string('display_name', 255);
            $table->text('note')->nullable();

            $table->string('storage_path', 500);
            $table->string('mime_type', 150)->nullable();
            $table->string('extension', 30)->nullable();
            $table->unsignedBigInteger('file_size');

            $table->integer('sort_order')->default(0)->index();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};

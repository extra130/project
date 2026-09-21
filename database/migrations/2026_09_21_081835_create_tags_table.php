<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        // 紀錄與標籤的多對多關聯表
        Schema::create('record_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            
            $table->unique(['record_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_tag');
        Schema::dropIfExists('tags');
    }
};

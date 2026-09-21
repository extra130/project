<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->index();

            $table->string('original_name', 255);          // 上傳時的原始檔名，不可修改
            $table->string('display_name', 255);           // 顯示名稱，可修改
            $table->text('note')->nullable();              // 附件備註，可修改

            $table->string('storage_path', 500);           // records/YYYY/MM/{uuid}.ext
            $table->string('mime_type', 150)->nullable();
            $table->string('extension', 30)->nullable();
            $table->unsignedBigInteger('file_size');

            $table->integer('sort_order')->default(0)->index();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('record_id')->references('id')->on('records')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_files');
    }
};

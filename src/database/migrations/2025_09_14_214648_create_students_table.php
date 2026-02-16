<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ผูกกับ users.id
            $table->string('prefix', 10)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('student_id', 20)->nullable();
            $table->string('room_bed', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('profile_image', 255)->nullable(); // เก็บ path รูปโปรไฟล์
            $table->timestamps();
            $table->string('sticker_number')->nullable();
            $table->string('qr_code_value')->nullable();

            // foreign key -> users.id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

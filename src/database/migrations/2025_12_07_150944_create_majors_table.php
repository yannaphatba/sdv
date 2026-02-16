<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('majors', function (Blueprint $table) {
        $table->id();
        // ✅ เพิ่ม ->nullable() เข้าไปตรงนี้ครับริว
        $table->unsignedBigInteger('faculty_id')->nullable(); 
        $table->string('name');
        $table->timestamps();

        $table->foreign('faculty_id')
              ->references('id')
              ->on('faculties')
              ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};

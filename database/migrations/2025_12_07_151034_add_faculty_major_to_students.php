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
    Schema::table('students', function (Blueprint $table) {
        $table->unsignedBigInteger('faculty_id')->nullable()->after('advisor_id');
        $table->unsignedBigInteger('major_id')->nullable()->after('faculty_id');

        $table->foreign('faculty_id')->references('id')->on('faculties');
        $table->foreign('major_id')->references('id')->on('majors');
        $table->string('phone')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};

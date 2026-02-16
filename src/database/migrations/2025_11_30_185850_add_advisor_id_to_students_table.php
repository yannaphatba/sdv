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
        $table->unsignedBigInteger('advisor_id')->nullable()->after('phone');

        $table->foreign('advisor_id')->references('id')->on('advisors')->nullOnDelete();
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropForeign(['advisor_id']);
        $table->dropColumn('advisor_id');
    });
}

};

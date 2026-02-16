<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('students', 'vehicle_image')) {
                $table->string('vehicle_image')->nullable()->after('phone');
            }
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
            if (Schema::hasColumn('students', 'vehicle_image')) {
                $table->dropColumn('vehicle_image');
            }
        });
    }
};

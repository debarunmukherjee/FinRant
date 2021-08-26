<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToUserInformationForFusion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_information', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->unique();
            $table->string('pan')->nullable()->unique();
            $table->unsignedInteger('dob_year')->nullable();
            $table->unsignedInteger('dob_month')->nullable();
            $table->unsignedInteger('dob_day')->nullable();
            $table->string('gender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_information', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('pan');
            $table->dropColumn('dob_year');
            $table->dropColumn('dob_month');
            $table->dropColumn('dob_day');
            $table->dropColumn('gender');
        });
    }
}

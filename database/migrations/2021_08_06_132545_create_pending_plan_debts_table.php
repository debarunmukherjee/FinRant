<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingPlanDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_plan_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->references('id')->on('plans');
            $table->foreignId('src_user_id')->references('id')->on('users');
            $table->foreignId('dest_user_id')->references('id')->on('users');
            $table->string('action');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_plan_debts');
    }
}

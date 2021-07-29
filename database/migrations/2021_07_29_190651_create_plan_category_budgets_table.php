<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCategoryBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_category_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('amount');
            $table->foreignId('category_id')->references('id')->on('expend_categories')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->unique(['category_id', 'user_id', 'plan_id']);
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
        Schema::dropIfExists('plan_category_budgets');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEqualDistributionColumnToSharedExpenseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shared_expense_details', function (Blueprint $table) {
            $table->boolean('is_equal_distribution')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shared_expense_details', function (Blueprint $table) {
            $table->dropColumn('is_equal_distribution');
        });
    }
}

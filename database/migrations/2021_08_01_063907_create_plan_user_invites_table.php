<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanUserInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_user_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreignId('sent_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('sent_to')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('has_accepted')->default(0);
            $table->boolean('is_rejected')->default(0);
            $table->unique(['plan_id', 'sent_by', 'sent_to']);
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
        Schema::dropIfExists('plan_user_invites');
    }
}

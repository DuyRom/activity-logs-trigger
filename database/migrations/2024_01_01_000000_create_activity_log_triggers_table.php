<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log_triggers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('action');
            $table->string('table_name');
            $table->string('primary_key'); 
            $table->json('old_values')->nullable(); 
            $table->json('new_values')->nullable(); 
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
        Schema::dropIfExists('activity_log_triggers');
    }
}



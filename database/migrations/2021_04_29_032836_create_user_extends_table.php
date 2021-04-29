<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExtendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extends', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('level');
            $table->foreignId('uid')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_extends', function (Blueprint $table) {

            $table->dropForeign(['uid']);
        });
        Schema::dropIfExists('user_extends');
    }
}

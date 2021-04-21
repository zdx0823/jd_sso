<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTgc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_st', function (Blueprint $table) {
            $table->text('tgt');
            $table->bigInteger('timeout');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_st', function (Blueprint $table) {
            $table->dropColumn('tgt');
            $table->dropColumn('timeout');
        });
    }
}

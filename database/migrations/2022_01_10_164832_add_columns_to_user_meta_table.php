<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserMetaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('user_meta', function (Blueprint $table) {
            $table->string('cover')->nullable()->after('display_picture');
            $table->enum('gender',['male','female','other'])->nullable()->after('cover');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_meta', function($table) {
            $table->dropColumns(['gender','cover']);
        });
    }
}

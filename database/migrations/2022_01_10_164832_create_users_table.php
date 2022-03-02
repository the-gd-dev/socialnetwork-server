<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->string('name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email',100)->unique()->notNullable();
            $table->string('alt_email',100)->unique()->nullable();
            $table->string('password');
            $table->string('phone_number')->unique()->notNullable();
            $table->string('alt_phone_number')->nullable();
            $table->enum('is_deactivated',[0,1])->default(0);
            $table->enum('is_disabled',[0,1])->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

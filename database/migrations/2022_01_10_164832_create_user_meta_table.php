<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMetaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('user_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('display_picture')->nullable();
            $table->string('cover')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->string('birthday')->nullable();
            $table->longText('bio_text')->nullable();
            $table->string('education')->nullable();
            $table->string('employment')->nullable();
            $table->string('relationship_status')->nullable();
            $table->string('intersted_in')->nullable();
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
        Schema::dropIfExists('user_meta');
    }
}

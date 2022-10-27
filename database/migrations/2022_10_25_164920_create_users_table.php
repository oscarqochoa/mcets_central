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
            $table->id();
            $table->string("fullname", 100);
            $table->string("email", 100);
            $table->enum('user_type', ['admin', 'user']);
            $table->string("username", 30);
            $table->string("password");
            $table->boolean("status")->default(true);
            $table->integer("created_by")->nullable();
            $table->integer("deleted_by")->nullable();
            $table->timestamps();
            $table->dateTime("deleted_at")->nullable();
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

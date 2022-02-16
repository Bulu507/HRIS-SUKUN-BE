<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsers extends Migration
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
            $table->string('admin_user_code')->unique();
            $table->string('name')->default('-');
            $table->bigInteger('role_code')->unsigned()->unique();
            $table->integer('type')->default(0)->comment("0=user 1=admin");
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('no_telp')->default('0');
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('admin_user_code')->references('code')->on('admin')->onDelete('cascade');
            $table->foreign('role_code')->references('id')->on('role')->onDelete('restrict');
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

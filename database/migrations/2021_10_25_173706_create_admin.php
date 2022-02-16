<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->default('-');
            $table->string('nama');
            $table->string('no_telp')->default('-');
            $table->text('alamat')->default('-');
            $table->string('code_alamat_ro', 99)->default('-')->unique();
            $table->text('alamat_ro')->default('-');
            $table->text('path_foto')->default('-');
            $table->string('status_admin')->default('aktif')->comment("aktif, non_aktif");
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('code')->references('admin_code')->on('admin_menu_access')->onDelete('cascade');
            $table->foreign('code_alamat_ro')->references('id_alamat')->on('ms_alamat_ro')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}

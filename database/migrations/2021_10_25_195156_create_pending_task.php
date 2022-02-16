<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePendingTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_task', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('menu_code')->unsigned()->unique();
            $table->string('user_id')->unique();
            $table->string('laporan_code')->default('-');
            $table->string('judul');
            $table->bigInteger('status')->default(0)->comment("0=pending 1=approved 2=rejected")->unsigned()->unique();
            $table->bigInteger('admin_id')->unsigned()->unique();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('menu_code')->references('id')->on('menu_access')->onDelete('restrict');
            $table->foreign('user_id')->references('admin_user_code')->on('users')->onDelete('restrict');
            $table->foreign('admin_id')->references('id')->on('admin')->onDelete('restrict');
            $table->foreign('status')->references('id')->on('ms_status_task')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_task');
    }
}

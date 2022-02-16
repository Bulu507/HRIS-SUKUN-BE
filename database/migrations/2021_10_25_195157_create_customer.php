<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('code_customer');
            $table->string('nama_customer');
            $table->string('nama_pemilik')->default('-');
            $table->text('alamat_customer')->default('-');
            $table->string('code_alamat_cust_ro', 99)->default('-')->unique();
            $table->text('alamat_cust_ro')->default('-');
            $table->text('alamat_pemilik')->default('-');
            $table->string('no_ktp_pemilik')->default('-');
            $table->string('kode_rayon')->default('-');
            $table->string('profil_pelanggan')->default('-');
            $table->string('bentuk_bidang_usaha')->default('-');
            $table->string('bidang_usaha')->default('-');
            $table->string('telephone')->default('-');
            $table->string('no_hp_wa')->default('-');
            $table->text('no_npwp')->default('-');
            $table->string('kepemilikan_usaha')->default('-');
            $table->string('permintaan_barang')->default('-');
            $table->string('metode_pembayaran')->default('-');
            $table->string('no_rek')->default('-');
            $table->string('bank')->default('-');
            $table->string('code_limit')->default('-');
            $table->string('code_salesman')->default('-')->unique();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('code_alamat_cust_ro')->references('id_alamat')->on('ms_alamat_ro')->onDelete('restrict');
            $table->foreign('code_salesman')->references('code')->on('admin')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer');
    }
}

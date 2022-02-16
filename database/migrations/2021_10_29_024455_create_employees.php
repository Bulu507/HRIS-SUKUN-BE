<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->index('id');
            $table->string('nik', 191)->unique();
            $table->string('parent_no', 99)->default('-');
            $table->string('name', 191);
            $table->string('alias', 191)->nullable();
            $table->string('ktp_no', 191)->nullable();
            $table->string('kk_no', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 191)->nullable();
            $table->string('kelurahan', 191)->nullable();
            $table->string('kecamatan', 191)->nullable();
            $table->string('province', 191)->nullable();
            $table->date('birthday')->nullable();
            $table->string('birthplace', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('marrital', 191)->nullable();
            $table->enum('blood_type', ['-', 'A', 'AB', 'B', 'O']);
            $table->enum('religion', ['Islam', 'Kristen', 'Khatolik', 'Hindu', 'Buddha', 'Konghuchu', 'Others']);
            $table->string('zipcode', 191)->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->string('bank_account', 191)->nullable();
            $table->string('npwp', 191)->nullable();
            $table->string('bank_branch', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->enum('job_status', ['Active', 'Skorsing', 'Move', 'Retired', 'Fired', 'Training']);
            $table->date('job_start')->nullable();
            $table->date('job_end')->nullable();
            $table->string('bpjs_kesehatan_no', 191)->nullable();
            $table->string('bpjs_tenagakerja_no', 191)->nullable();
            $table->string('position_no', 191)->nullable()->index();;
            $table->string('mother_name')->nullable();
            $table->string('employee_photo')->nullable();
            $table->addColumn('tinyInteger', 'is_user', ['lenght'   => 1, 'default' => '0']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

<?php

namespace App\Http\Controllers;

use App\EmpDaftarAnak;
use App\EmpHistoryKedisiplinan;
use App\Employee;
use App\EmpPengalamanKerja;
use App\EmpRiwayatPekerjaan;
use App\Exports\EmployeeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Nonstandard\Uuid;
use Barryvdh\DomPDF\Facade as PDF;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class EmployeeController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/api/AddEmployee",
     *   tags={"Employee"},
     *  security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Tambah Data Employee",
     *   operationId="AddEmployee",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Tambah Data Employee",
     *          required=true, 
     *          type="object",
     *   		@SWG\Schema(
     *              @SWG\Property(property="foto", type="string", example="name.jpg"),
     *              @SWG\Property(property="no_induk_karyawan", type="string", example="212"),
     *              @SWG\Property(property="no_ktp", type="string", example="00000000001"),
     *				@SWG\Property(property="nama_lengkap", type="string", example="Yuda Keling"),
     *              @SWG\Property(property="jenis_kelamin", type="string", enum = {"Laki-Laki", "Perempuan"}, example="Laki-Laki"),
     *              @SWG\Property(property="tempat_lahir", type="string", example="Demak"),
     *              @SWG\Property(property="tanggal_lahir", type="date", example="2020-01-01"),
     *              @SWG\Property(property="nama_ortu", type="string", example="Paimin"),
     *				@SWG\Property(property="agama", type="string", enum = {"Islam", "Kristen", "Khatolik", "Hindu", "Buddha", "Konghuchu", "Others"}, example="Islam"),
     *              @SWG\Property(property="unit_perusahan", type="string", example="Unit 1"),
     *              @SWG\Property(property="pangkat", type="string", example="pangkat 1"),
     *              @SWG\Property(property="jabatan", type="string", example="Jabatan 1"),
     *              @SWG\Property(property="divisi", type="string", example="Divisi 1"),
     *				@SWG\Property(property="departement", type="string", example="Departement 1"),
     *              @SWG\Property(property="RT", type="string", example="02"),
     *              @SWG\Property(property="RW", type="string", example="03"),
     *              @SWG\Property(property="No_Rumah", type="string", example="7A"),
     *              @SWG\Property(property="Desa", type="string", example="JL. Kalisombo"),
     *				@SWG\Property(property="Kec", type="string", example="Sidorejo"),
     *              @SWG\Property(property="Kab", type="string", example="Salatiga"),
     *              @SWG\Property(property="status_nikah", type="string", example="Kawin"),
     *              @SWG\Property(property="nama_istri_suami", type="string", example="41.305.357.0-446.000"),
     *              @SWG\Property(property="pekerjaan_istri_suami", type="string", example="IRT"),
     *				@SWG\Property(property="bin_binti", type="string", example="Supardi"),
     *              @SWG\Property(property="gol_darah", type="string", enum={"-", "A", "B", "AB", "O"}, example="A"),
     *              @SWG\Property(property="status_karyawan", type="string", example="Kontrak"),
     *              @SWG\Property(property="telpon", type="string", example="082134567569"),
     *              @SWG\Property(property="no_telpon_darurat", type="string", example="082134567577"),
     *				@SWG\Property(property="pengalaman_kerja_sebelum", type="string", example="PT A"),
     *              @SWG\Property(property="mulai_masuk_kerja", type="date", example="2020-01-01"),
     *              @SWG\Property(property="bagian", type="string", example="HRD"),
     *              @SWG\Property(property="ditetapkan", type="date", example="2020-01-01"),
     *              @SWG\Property(property="nomor_jamsostek", type="string", example="01234567"),
     *              @SWG\Property(property="scan_kartu_jamsostek", type="string", example="A.jpg"),
     *              @SWG\Property(property="bpjs_kesehatan", type="string", example="123456789"),
     *              @SWG\Property(property="scan_kartu_bpjs", type="string", example="B.JPG"),
     *				@SWG\Property(property="no_rek", type="string", example="10028965274"),
     *              @SWG\Property(property="no_npwp", type="string", example="41.305.357.0-446.000"),
     *              @SWG\Property(property="pendidikan_terakhir", type="string", example="SMA"),
     *              @SWG\Property(property="no_anggota_koperasi", type="string", example="12345678"),
     *              @SWG\Property(property="listAnak", type="array", 
     *                  @SWG\Items(@SWG\Property(property="nama_anak", type="string",example="Rosa"),
     *                             @SWG\Property(property="anak_ke", type="string",example="3"),
     *                             @SWG\Property(property="tempat_lahir", type="string",example="Salatiga"),
     *                             @SWG\Property(property="tanggal_lahir", type="date",example="1992-04-16"),
     *                             @SWG\Property(property="status_nikah", type="string",example="Kawin"),
     *                             @SWG\Property(property="status_status", type="string",example="Kandung"),
     *                 ),
     *              ),
     *              @SWG\Property(property="listPengalamanKerja", type="array", 
     *                  @SWG\Items(@SWG\Property(property="perusahaan", type="string",example="PT Telkom"),
     *                             @SWG\Property(property="jabatan", type="string",example="Direktur"),
     *                             @SWG\Property(property="alasan_kepindahan", type="string",example="Habis Kontrak"),
     *                  ),
     *              ),
     *            @SWG\Property(property="listRiwayatPekerjaan", type="array", 
     *                  @SWG\Items(@SWG\Property(property="unit_perusahaan", type="string",example="Perusahaan X"),
     *                             @SWG\Property(property="tgl", type="string",example="1992-04-16"),
     *                             @SWG\Property(property="jabatan", type="string",example="Manager"),
     *                             @SWG\Property(property="alasan_kepindahan", type="date",example="Resign"),
     *                 ),
     *              ),
     *          @SWG\Property(property="listHistoryKedisiplinan", type="array", 
     *                  @SWG\Items(@SWG\Property(property="nama_kedisiplinan", type="string",example="Disiplin"),
     *                 ),
     *              ),
     *         ),
     *      )
     * )
     *
     */

    public function AddEmployee(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //Employee
            'foto'                      => 'string|max:255',
            'no_induk_karyawan'         => 'required|string|max:255',
            'no_ktp'                    => 'required|string|max:255',
            'nama_lengkap'              => 'required|string|max:255',
            'jenis_kelamin'             => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir'              => 'string|max:255',
            'tanggal_lahir'             => 'date|nullable',
            'nama_ortu'                 => 'string|max:255|nullable',
            'agama'                     => 'required|in:-,Islam,Kristen,Khatolik,Hindu,Buddha,Konghuchu,Others',
            'unit_perusahan'            => 'string|max:255|nullable',
            'pangkat'                   => 'string|max:255|nullable',
            'jabatan'                   => 'string|max:255|nullable',
            'divisi'                    => 'string|max:255|nullable',
            'departement'               => 'string|max:255|nullable',
            'rt'                        => 'string|max:255|nullable',
            'rw'                        => 'string|max:255|nullable',
            'no_rumah'                  => 'string|max:255|nullable',
            'desa'                      => 'string|max:255|nullable',
            'kec'                       => 'string|max:255|nullable',
            'kab'                       => 'string|max:255|nullable',
            'status_nikah'              => 'string|max:255|nullable',
            'nama_istri_suami'          => 'string|max:255|nullable',
            'pekerjaan_istri_suami'     => 'string|max:255|nullable',
            'bin_binti'                 => 'string|max:255|nullable',
            'gol_darah'                 => 'required|in:-,A,AB,B,O',
            'status_karyawan'           => 'nullable',
            'telpon'                    => 'string|nullable',
            'no_telpon_darurat'         => 'string|max:255|nullable',
            'pengalaman_kerja_sebelum'  => 'string|max:255|nullable',
            'mulai_masuk_kerja'         => 'date|nullable',
            'bagian'                    => 'string|max:255|nullable',
            'ditetapkan'                => 'date|nullable',
            'nomor_jamsostek'           => 'string|max:255|nullable',
            'scan_kartu_jamsostek'      => 'string|max:255|nullable',
            'bpjs_kesehatan'            => 'string|max:255|nullable',
            'scan_kartu_bpjs'           => 'string|max:255|nullable',
            'no_rek'                    => 'string|max:255|nullable',
            'no_npwp'                   => 'string|max:255|nullable',
            'pendidikan_terakhir'       => 'string|max:255|nullable',
            'no_anggota_koperasi'       => 'string|max:255|nullable',
            //'job_start' => 'date|date_format:Y-m-d|nullable',
            //'job_end' => 'date|date_format:Y-m-d|after:start_date|nullable',
            //'is_user' => 'numeric|max:1'


        ]);

        if ($validator->fails()) {
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }


        try {

            $success = false;
            DB::beginTransaction();

            try {
                Employee::create([
                    'foto' => $request->foto,
                    'no_induk_karyawan' => $request->no_induk_karyawan,
                    'no_ktp' => $request->no_ktp,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'nama_ortu' => $request->nama_ortu,
                    'agama' => $request->agama,
                    'unit_perusahan' => $request->unit_perusahan,
                    'pangkat' => $request->pangkat,
                    'jabatan' => $request->jabatan,
                    'divisi' => $request->divisi,
                    'departement' => $request->departement,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'no_rumah' => $request->no_rumah,
                    'desa' => $request->desa,
                    'kec' => $request->kec,
                    'kab' => $request->kab,
                    'status_nikah' => $request->status_nikah,
                    'nama_istri_suami' => $request->nama_istri_suami,
                    'pekerjaan_istri_suami' => $request->pekerjaan_istri_suami,
                    'bin_binti' => $request->bin_binti,
                    'gol_darah' => $request->gol_darah,
                    'status_karyawan' => $request->status_karyawan,
                    'telpon' => $request->telpon,
                    'no_telpon_darurat' => $request->no_telpon_darurat,
                    'pengalaman_kerja_sebelum' => $request->pengalaman_kerja_sebelum,
                    'mulai_masuk_kerja' => $request->mulai_masuk_kerja,
                    'bagian' => $request->bagian,
                    'ditetapkan' => $request->ditetapkan,
                    'nomor_jamsostek' => $request->nomor_jamsostek,
                    'scan_kartu_jamsostek' => $request->scan_kartu_jamsostek,
                    'bpjs_kesehatan' => $request->bpjs_kesehatan,
                    'scan_kartu_bpjs' => $request->scan_kartu_bpjs,
                    'no_rek' => $request->no_rek,
                    'no_npwp' => $request->no_npwp,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'no_anggota_koperasi' => $request->no_anggota_koperasi,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $list_anak = json_decode($request->listAnak, true);            
                if (count($list_anak) != 0) {

                    foreach ($list_anak as $key) {
                        $daftarAnak = array(
                            'emp_nik'       => $request->no_induk_karyawan,
                            'nama'          => $key['nama'],
                            'anak_ke'       => $key['anak_ke'],
                            'tempat_lahir'  => $key['tempat_lahir'],
                            'tanggal_lahir' => $key['tanggal_lahir'],
                            'status_nikah'  => $key['status_nikah'],
                            'status_status' => $key['status_status'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );
                        //var_dump($daftarAnak);
                        EmpDaftarAnak::create($daftarAnak);
                    }
                }

                // if (!empty($request->listHistoryKedisiplinan)) {

                //     foreach ($request->listHistoryKedisiplinan as $key) {
                //         $historyKedisiplinan = array(
                //             'emp_nik'   => $request->no_induk_karyawan,
                //             'nama'      => $key['nama_kedisiplinan']
                //         );
                //         EmpHistoryKedisiplinan::create($historyKedisiplinan);
                //     }
                // }

                
                $list_riwayat_pekerjaan = json_decode($request->listRiwayatPekerjaan, true);            
                if (count($list_riwayat_pekerjaan) != 0) {

                    foreach ($list_riwayat_pekerjaan as $key) {
                        $riwayatPekerjaan = array(
                            'emp_nik'           => $request->no_induk_karyawan,
                            'unit_perusahaan'   => $key['unit_perusahaan'],
                            'tgl'               => $key['tgl'],
                            'jabatan'           => $key['jabatan'],
                            'alasan_kepindahan' => $key['alasan_kepindahan'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );

                        EmpRiwayatPekerjaan::create($riwayatPekerjaan);
                    }
                }

                $list_pengalaman_kerja = json_decode($request->listPengalamanKerja, true);            
                if (count($list_pengalaman_kerja) != 0) {

                    foreach ($list_pengalaman_kerja as $key) {
                        $pengalamanKerja = array(
                            'emp_nik'             => $request->no_induk_karyawan,
                            'perusahaan'          => $key['perusahaan'],
                            'jabatan'             => $key['jabatan'],
                            'alasan_kepindahan'   => $key['alasan_kepindahan'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );

                        EmpPengalamanKerja::create($pengalamanKerja);
                    }
                }

                $success = true;
                if ($success) {
                    DB::commit();
                }
                
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200);
            } catch (\Exception $e) {
                DB::rollback();
                $success = false;
                throw $e;
            }
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/EditEmployee",
     *   tags={"Employee"},
     *  security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Edit Data Employee",
     *   operationId="EditEmployee",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Edit Data Employee",
     *          required=true, 
     *         type="object",
     *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="foto", type="string", example="name.jpg"),
     *              @SWG\Property(property="no_induk_karyawan", type="string", example="212"),
     *              @SWG\Property(property="no_ktp", type="string", example="00000000001"),
     *				@SWG\Property(property="nama_lengkap", type="string", example="Yuda Keling"),
     *              @SWG\Property(property="jenis_kelamin", type="string", enum = {"Laki-Laki", "Perempuan"}, example="Laki-Laki"),
     *              @SWG\Property(property="tempat_lahir", type="string", example="Demak"),
     *              @SWG\Property(property="tanggal_lahir", type="date", example="2020-01-01"),
     *              @SWG\Property(property="nama_ortu", type="string", example="Paimin"),
     *				@SWG\Property(property="agama", type="string", enum = {"Islam", "Kristen", "Khatolik", "Hindu", "Buddha", "Konghuchu", "Others"}, example="Islam"),
     *              @SWG\Property(property="unit_perusahan", type="string", example="Unit 1"),
     *              @SWG\Property(property="pangkat", type="string", example="pangkat 1"),
     *              @SWG\Property(property="jabatan", type="string", example="Jabatan 1"),
     *              @SWG\Property(property="divisi", type="string", example="Divisi 1"),
     *				@SWG\Property(property="departement", type="string", example="Departement 1"),
     *              @SWG\Property(property="RT", type="string", example="02"),
     *              @SWG\Property(property="RW", type="string", example="03"),
     *              @SWG\Property(property="No_Rumah", type="string", example="7A"),
     *              @SWG\Property(property="Desa", type="string", example="JL. Kalisombo"),
     *				@SWG\Property(property="Kec", type="string", example="Sidorejo"),
     *              @SWG\Property(property="Kab", type="string", example="Salatiga"),
     *              @SWG\Property(property="status_nikah", type="string", example="Kawin"),
     *              @SWG\Property(property="nama_istri_suami", type="string", example="41.305.357.0-446.000"),
     *              @SWG\Property(property="pekerjaan_istri_suami", type="string", example="IRT"),
     *				@SWG\Property(property="bin_binti", type="string", example="Supardi"),
     *              @SWG\Property(property="gol_darah", type="string", enum={"-", "A", "B", "AB", "O"}, example="A"),
     *              @SWG\Property(property="status_karyawan", type="string", example="Kontrak"),
     *              @SWG\Property(property="telpon", type="string", example="082134567569"),
     *              @SWG\Property(property="no_telpon_darurat", type="string", example="082134567577"),
     *				@SWG\Property(property="pengalaman_kerja_sebelum", type="string", example="PT A"),
     *              @SWG\Property(property="mulai_masuk_kerja", type="date", example="2020-01-01"),
     *              @SWG\Property(property="bagian", type="string", example="HRD"),
     *              @SWG\Property(property="ditetapkan", type="date", example="2020-01-01"),
     *              @SWG\Property(property="nomor_jamsostek", type="string", example="01234567"),
     *              @SWG\Property(property="scan_kartu_jamsostek", type="string", example="A.jpg"),
     *              @SWG\Property(property="bpjs_kesehatan", type="string", example="123456789"),
     *              @SWG\Property(property="scan_kartu_bpjs", type="string", example="B.JPG"),
     *				@SWG\Property(property="no_rek", type="string", example="10028965274"),
     *              @SWG\Property(property="no_npwp", type="string", example="41.305.357.0-446.000"),
     *              @SWG\Property(property="pendidikan_terakhir", type="string", example="SMA"),
     *              @SWG\Property(property="no_anggota_koperasi", type="string", example="12345678"),
     *              @SWG\Property(property="listAnak", type="array", 
     *                  @SWG\Items(@SWG\Property(property="nama_anak", type="string",example="Rosa"),
     *                             @SWG\Property(property="anak_ke", type="string",example="3"),
     *                             @SWG\Property(property="tempat_lahir", type="string",example="Salatiga"),
     *                             @SWG\Property(property="tanggal_lahir", type="date",example="1992-04-16"),
     *                             @SWG\Property(property="status_nikah", type="string",example="Kawin"),
     *                             @SWG\Property(property="status_status", type="string",example="Kandung"),
     *                 ),
     *              ),
     *              @SWG\Property(property="listPengalamanKerja", type="array", 
     *                  @SWG\Items(@SWG\Property(property="perusahaan", type="string",example="PT Telkom"),
     *                             @SWG\Property(property="jabatan", type="string",example="Direktur"),
     *                             @SWG\Property(property="alasan_kepindahan", type="string",example="Habis Kontrak"),
     *                  ),
     *              ),
     *            @SWG\Property(property="listRiwayatPekerjaan", type="array", 
     *                  @SWG\Items(@SWG\Property(property="unit_perusahaan", type="string",example="Perusahaan X"),
     *                             @SWG\Property(property="tgl", type="string",example="1992-04-16"),
     *                             @SWG\Property(property="jabatan", type="string",example="Manager"),
     *                             @SWG\Property(property="alasan_kepindahan", type="date",example="Resign"),
     *                 ),
     *              ),
     *          @SWG\Property(property="listHistoryKedisiplinan", type="array", 
     *                  @SWG\Items(@SWG\Property(property="nama_kedisiplinan", type="string",example="Disiplin"),
     *                 ),
     *              ),
     *         ),
     *      )
     * )
     *
     */

    public function EditEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Employee
            'foto'                      => 'string|max:255',
            'no_induk_karyawan'         => 'required|string|max:255',
            'no_ktp'                    => 'required|string|max:255',
            'nama_lengkap'              => 'required|string|max:255',
            'jenis_kelamin'             => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir'              => 'string|max:255',
            'tanggal_lahir'             => 'date|nullable',
            'nama_ortu'                 => 'string|max:255|nullable',
            'agama'                     => 'required|in:-,Islam,Kristen,Khatolik,Hindu,Buddha,Konghuchu,Others',
            'unit_perusahan'            => 'string|max:255|nullable',
            'pangkat'                   => 'string|max:255|nullable',
            'jabatan'                   => 'string|max:255|nullable',
            'divisi'                    => 'string|max:255|nullable',
            'departement'               => 'string|max:255|nullable',
            'rt'                        => 'string|max:255|nullable',
            'rw'                        => 'string|max:255|nullable',
            'no_rumah'                  => 'string|max:255|nullable',
            'desa'                      => 'string|max:255|nullable',
            'kec'                       => 'string|max:255|nullable',
            'kab'                       => 'string|max:255|nullable',
            'status_nikah'              => 'string|max:255|nullable',
            'nama_istri_suami'          => 'string|max:255|nullable',
            'pekerjaan_istri_suami'     => 'string|max:255|nullable',
            'bin_binti'                 => 'string|max:255|nullable',
            'gol_darah'                 => 'required|in:-,A,AB,B,O',
            'status_karyawan'           => 'nullable',
            'telpon'                    => 'string|nullable',
            'no_telpon_darurat'         => 'string|max:255|nullable',
            'pengalaman_kerja_sebelum'  => 'string|max:255|nullable',
            'mulai_masuk_kerja'         => 'date|nullable',
            'bagian'                    => 'string|max:255|nullable',
            'ditetapkan'                => 'date|nullable',
            'nomor_jamsostek'           => 'string|max:255|nullable',
            'scan_kartu_jamsostek'      => 'string|max:255|nullable',
            'bpjs_kesehatan'            => 'string|max:255|nullable',
            'scan_kartu_bpjs'           => 'string|max:255|nullable',
            'no_rek'                    => 'string|max:255|nullable',
            'no_npwp'                   => 'string|max:255|nullable',
            'pendidikan_terakhir'       => 'string|max:255|nullable',
            'no_anggota_koperasi'       => 'string|max:255|nullable',
            //'job_start' => 'date|date_format:Y-m-d|nullable',
            //'job_end' => 'date|date_format:Y-m-d|after:start_date|nullable',
            //'is_user' => 'numeric|max:1'


        ]);

        if ($validator->fails()) {
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        try {

            $success = false;
            DB::beginTransaction();

            try {
                $employee = Employee::find($request->id);

                $employeeById = DB::table('employees')
                    ->where('id', '=', $request->id)
                    // ->whereNull('deleted_at')
                    ->select(
                        'employees.no_induk_karyawan',
                    )
                    ->first();

                if ($employeeById) {
                    $daftar_anak = DB::table('emp_daftar_anak')
                        ->where('emp_nik', '=', $employeeById->no_induk_karyawan);
                    $daftar_anak->delete();

                    $pengalamanKerja = DB::table('emp_pengalaman_kerja')
                        ->where('emp_nik', '=', $employeeById->no_induk_karyawan);
                    $pengalamanKerja->delete();

                    $riwayatPekerjaan = DB::table('emp_riwayat_pekerjaan')
                        ->where('emp_nik', '=', $employeeById->no_induk_karyawan);
                    $riwayatPekerjaan->delete();
                }

                $employee->update([
                    'foto' => $request->foto,
                    'no_induk_karyawan' => $request->no_induk_karyawan,
                    'no_ktp' => $request->no_ktp,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'nama_ortu' => $request->nama_ortu,
                    'agama' => $request->agama,
                    'unit_perusahan' => $request->unit_perusahan,
                    'pangkat' => $request->pangkat,
                    'jabatan' => $request->jabatan,
                    'divisi' => $request->divisi,
                    'departement' => $request->departement,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'no_rumah' => $request->no_rumah,
                    'desa' => $request->desa,
                    'kec' => $request->kec,
                    'kab' => $request->kab,
                    'status_nikah' => $request->status_nikah,
                    'nama_istri_suami' => $request->nama_istri_suami,
                    'pekerjaan_istri_suami' => $request->pekerjaan_istri_suami,
                    'bin_binti' => $request->bin_binti,
                    'gol_darah' => $request->gol_darah,
                    'status_karyawan' => $request->status_karyawan,
                    'telpon' => $request->telpon,
                    'no_telpon_darurat' => $request->no_telpon_darurat,
                    'pengalaman_kerja_sebelum' => $request->pengalaman_kerja_sebelum,
                    'mulai_masuk_kerja' => $request->mulai_masuk_kerja,
                    'bagian' => $request->bagian,
                    'ditetapkan' => $request->ditetapkan,
                    'nomor_jamsostek' => $request->nomor_jamsostek,
                    'scan_kartu_jamsostek' => $request->scan_kartu_jamsostek,
                    'bpjs_kesehatan' => $request->bpjs_kesehatan,
                    'scan_kartu_bpjs' => $request->scan_kartu_bpjs,
                    'no_rek' => $request->no_rek,
                    'no_npwp' => $request->no_npwp,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'no_anggota_koperasi' => $request->no_anggota_koperasi,
                    // 'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $list_anak = json_decode($request->listAnak, true);            
                if (count($list_anak) != 0) {

                    foreach ($list_anak as $key) {
                        $daftarAnak = array(
                            'emp_nik'       => $request->no_induk_karyawan,
                            'nama'          => $key['nama'],
                            'anak_ke'       => $key['anak_ke'],
                            'tempat_lahir'  => $key['tempat_lahir'],
                            'tanggal_lahir' => $key['tanggal_lahir'],
                            'status_nikah'  => $key['status_nikah'],
                            'status_status' => $key['status_status'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );
                        //var_dump($daftarAnak);
                        EmpDaftarAnak::create($daftarAnak);
                    }
                }

                // if (!empty($request->listHistoryKedisiplinan)) {

                //     foreach ($request->listHistoryKedisiplinan as $key) {
                //         $historyKedisiplinan = array(
                //             'emp_nik'   => $request->no_induk_karyawan,
                //             'nama'      => $key['nama_kedisiplinan']
                //         );
                //         EmpHistoryKedisiplinan::create($historyKedisiplinan);
                //     }
                // }

                
                $list_riwayat_pekerjaan = json_decode($request->listRiwayatPekerjaan, true);            
                if (count($list_riwayat_pekerjaan) != 0) {

                    foreach ($list_riwayat_pekerjaan as $key) {
                        $riwayatPekerjaan = array(
                            'emp_nik'           => $request->no_induk_karyawan,
                            'unit_perusahaan'   => $key['unit_perusahaan'],
                            'tgl'               => $key['tgl'],
                            'jabatan'           => $key['jabatan'],
                            'alasan_kepindahan' => $key['alasan_kepindahan'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );

                        EmpRiwayatPekerjaan::create($riwayatPekerjaan);
                    }
                }

                $list_pengalaman_kerja = json_decode($request->listPengalamanKerja, true);            
                if (count($list_pengalaman_kerja) != 0) {

                    foreach ($list_pengalaman_kerja as $key) {
                        $pengalamanKerja = array(
                            'emp_nik'             => $request->no_induk_karyawan,
                            'perusahaan'          => $key['perusahaan'],
                            'jabatan'             => $key['jabatan'],
                            'alasan_kepindahan'   => $key['alasan_kepindahan'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );

                        EmpPengalamanKerja::create($pengalamanKerja);
                    }
                }

                $success = true;
                if ($success) {
                    DB::commit();
                }
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200);
            } catch (\Exception $e) {
                DB::rollback();
                $success = false;
                throw $e;
            }
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllEmployee",
     *   tags={"Employee"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Employee",
     *   operationId="GetAllEmployee",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="divisi_code",in="query",  type="string"),
     *      @SWG\Parameter(name="department_code",in="query",  type="string"),
     *      @SWG\Parameter(name="unit_perusahaan_code",in="query",  type="string"),
     *      @SWG\Parameter(name="status",in="query",  type="string"),
     * )
     */

    public function GetAllEmployee(Request $request)
    {
        try {                  
            
            $getstatus = $request->status;
            if($getstatus){$status='%'.$getstatus.'%';}
            else{$status='%%';}

            $getup = $request->unit_perusahaan_code;
            if($getup){$unit_perusahaan='%'.$getup.'%';}
            else{$unit_perusahaan='%%';}

            $getdivisi = $request->divisi_code;
            if($getdivisi){$divisi='%'.$getdivisi.'%';}
            else{$divisi='%%';}

            $getdepartment = $request->department_code;
            if($getdepartment){$department='%'.$getdepartment.'%';}
            else{$department='%%';}
        
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select(
                    'role_code'
                )
                ->first();
            $roleCode = ['role_code' => $getRole->role_code];

            $query = DB::table('employees')
                ->select('employees.id','employees.no_induk_karyawan','employees.nama_lengkap','employees.jenis_kelamin','employees.jabatan','employees.pangkat',
                'employees.status_karyawan','ms_status_karyawan.nama as status_karyawan_nama',
                'ms_department.divisi_code', 'ms_department.department_code','ms_department.nama as dept_nama','ms_divisi.nama as divisi_nama','ms_unit_perusahaan.nama as unit_perusahan_nama')
                ->join('ms_unit_perusahaan','ms_unit_perusahaan.unit_perusahaan_code','=', 'employees.unit_perusahan')
                ->join('ms_divisi','ms_divisi.divisi_code','=', 'employees.divisi')
                ->join('ms_department','ms_department.department_code','=', 'employees.departement')
                ->join('ms_status_karyawan','ms_status_karyawan.id','=', 'employees.status_karyawan')
                ->where('ms_unit_perusahaan.unit_perusahaan_code', 'Like', $unit_perusahaan)
                ->where('ms_divisi.divisi_code', 'Like', $divisi)
                ->where('ms_department.department_code', 'Like', $department)   
                ->where('employees.status_karyawan', 'Like', $status)             
                ->orderBy('employees.nama_lengkap', 'asc');

            if (in_array('99', $roleCode)) {
                $query =  $query->get();
                $count = $query->count();
            } else {
                $query =  $query->where('employees.is_dell', 0)->get();
                $count =  $query->where('employees.is_dell', 0)->count();
            }

            if(count($query)!=0){
                $data = ['data' => $query, 'count' => $count];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }

        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetDetailEmployeeById",
     *   tags={"Employee"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get Detail Employee By Id",
     *   operationId="GetDetailEmployeeById",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *   @SWG\Parameter(name="id", in="query",  type="string", required=true),
     *  
     * )
     */

    public function GetDetailEmployeeById(Request $request)
    {
        try {
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select(
                    'role_code'
                )
                ->first();
            $roleCode = ['role_code' => $getRole->role_code];

            $employee = DB::table('employees')
                ->select(
                    'employees.foto',
                    'employees.no_induk_karyawan',
                    'employees.no_ktp',
                    'employees.nama_lengkap',
                    'employees.jenis_kelamin',
                    'employees.tempat_lahir',
                    'employees.tanggal_lahir',
                    'employees.nama_ortu',
                    'employees.agama',
                    'employees.unit_perusahan',
                    'employees.pangkat',
                    'employees.jabatan',
                    'employees.divisi',
                    'employees.departement',
                    'employees.rt',
                    'employees.rw',
                    'employees.no_rumah',
                    'employees.desa',
                    'employees.kec',
                    'employees.kab',
                    'employees.status_nikah',
                    'employees.nama_istri_suami',
                    'employees.pekerjaan_istri_suami',
                    'employees.bin_binti',
                    'employees.gol_darah',
                    'employees.status_karyawan',
                    'employees.telpon',
                    'employees.no_telpon_darurat',
                    'employees.pengalaman_kerja_sebelum',
                    'employees.mulai_masuk_kerja',
                    'employees.bagian',
                    'employees.ditetapkan',
                    'employees.nomor_jamsostek',
                    'employees.scan_kartu_jamsostek',
                    'employees.bpjs_kesehatan',
                    'employees.scan_kartu_bpjs',
                    'employees.no_rek',
                    'employees.no_npwp',
                    'employees.pendidikan_terakhir',
                    'employees.no_anggota_koperasi',
                    'employees.created_at',
                    'employees.updated_at',
                    'ms_status_karyawan.nama as status_karyawan_nama',
                    'ms_department.divisi_code', 'ms_department.department_code',
                    'ms_department.nama as dept_nama','ms_divisi.nama as divisi_nama',
                    'ms_unit_perusahaan.nama as unit_perusahan_nama')
                ->join('ms_unit_perusahaan','ms_unit_perusahaan.unit_perusahaan_code','=', 'employees.unit_perusahan')
                ->join('ms_divisi','ms_divisi.divisi_code','=', 'employees.divisi')
                ->join('ms_department','ms_department.department_code','=', 'employees.departement')
                ->join('ms_status_karyawan','ms_status_karyawan.id','=', 'employees.status_karyawan')
                ->where('employees.id', '=', $request->id);

            if (in_array('99', $roleCode)) {
                $employee =  $employee->first();
            } else {
                $employee =  $employee->whereNull('deleted_at')->first();
            }

            if ($employee) {
                $daftar_anak = DB::table('emp_daftar_anak')
                    ->where('emp_nik', '=', $employee->no_induk_karyawan)
                    ->select(
                        'emp_daftar_anak.*'
                    );
                if (in_array('99', $roleCode)) {
                    $daftar_anak =  $daftar_anak->get();
                } else {
                    $daftar_anak =  $daftar_anak->whereNull('deleted_at')->get();
                }

                $history_kedisiplinan = DB::table('emp_history_kedisiplinan')
                    ->where('emp_nik', '=', $employee->no_induk_karyawan)
                    ->select(
                        'emp_history_kedisiplinan.*'
                    );
                if (in_array('99', $roleCode)) {
                    $history_kedisiplinan =  $history_kedisiplinan->get();
                } else {
                    $history_kedisiplinan =  $history_kedisiplinan->whereNull('deleted_at')->get();
                }

                $pengalamanKerja = DB::table('emp_pengalaman_kerja')
                    ->where('emp_nik', '=', $employee->no_induk_karyawan)
                    ->select(
                        'emp_pengalaman_kerja.*'
                    );
                if (in_array('99', $roleCode)) {
                    $pengalamanKerja =  $pengalamanKerja->get();
                } else {
                    $pengalamanKerja =  $pengalamanKerja->whereNull('deleted_at')->get();
                }

                $riwayatPekerjaan = DB::table('emp_riwayat_pekerjaan')
                    ->where('emp_nik', '=', $employee->no_induk_karyawan)
                    ->select(
                        'emp_riwayat_pekerjaan.*'
                    );
                if (in_array('99', $roleCode)) {
                    $riwayatPekerjaan =  $riwayatPekerjaan->get();
                } else {
                    $riwayatPekerjaan =  $riwayatPekerjaan->whereNull('deleted_at')->get();
                }

                $data = [
                    'no_induk_karyawan'         => $employee->no_induk_karyawan,
                    "foto"                      => $employee->foto,
                    "no_ktp"                    => $employee->no_ktp,
                    "nama_lengkap"              => $employee->nama_lengkap,
                    "jenis_kelamin"             => $employee->jenis_kelamin,
                    "tempat_lahir"              => $employee->tempat_lahir,
                    "tanggal_lahir"             => $employee->tanggal_lahir,
                    "nama_ortu"                 => $employee->nama_ortu,
                    "agama"                     => $employee->agama,
                    "unit_perusahan"            => $employee->unit_perusahan,
                    "pangkat"                   => $employee->pangkat,
                    "jabatan"                   => $employee->jabatan,
                    "divisi"                    => $employee->divisi,
                    "departement"               => $employee->departement,
                    "rt"                        => $employee->rt,
                    "rw"                        => $employee->rw,
                    "no_rumah"                  => $employee->no_rumah,
                    "desa"                      => $employee->desa,
                    "kec"                       => $employee->kec,
                    "kab"                       => $employee->kab,
                    "status_nikah"              => $employee->status_nikah,
                    "nama_istri_suami"          => $employee->nama_istri_suami,
                    "pekerjaan_istri_suami"     => $employee->pekerjaan_istri_suami,
                    "bin_binti"                 => $employee->bin_binti,
                    "gol_darah"                 => $employee->gol_darah,
                    "status_karyawan"           => $employee->status_karyawan,
                    "telpon"                    => $employee->telpon,
                    "no_telpon_darurat"         => $employee->no_telpon_darurat,
                    "pengalaman_kerja_sebelum"  => $employee->pengalaman_kerja_sebelum,
                    "mulai_masuk_kerja"         => $employee->mulai_masuk_kerja,
                    "bagian"                    => $employee->bagian,
                    "ditetapkan"                => $employee->ditetapkan,
                    "nomor_jamsostek"           => $employee->nomor_jamsostek,
                    "scan_kartu_jamsostek"      => $employee->scan_kartu_jamsostek,
                    "bpjs_kesehatan"            => $employee->bpjs_kesehatan,
                    "scan_kartu_bpjs"           => $employee->scan_kartu_bpjs,
                    "no_rek"                    => $employee->no_rek,
                    "no_npwp"                   => $employee->no_npwp,
                    "pendidikan_terakhir"       => $employee->pendidikan_terakhir,
                    "no_anggota_koperasi"       => $employee->no_anggota_koperasi,
                    "status_karyawan_nama"      => $employee->status_karyawan_nama,
                    "dept_nama"                 => $employee->dept_nama,
                    "divisi_nama"               => $employee->divisi_nama,
                    "unit_perusahan_nama"       => $employee->unit_perusahan_nama,
                    "created_at"                => $employee->created_at,
                    "updated_at"                => $employee->updated_at,
                    "listAnak"                  => $daftar_anak,
                    "listHistoryKedisiplinan"   => $history_kedisiplinan,
                    "listPengalamanKerja"       => $pengalamanKerja,
                    "listRiwayatPekerjaan"      => $riwayatPekerjaan,
                    "User"                      => $userId
                ];

                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }

            
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteEmployee",
     *   tags={"Employee"},
     *    security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Delete Employee",
     *   operationId="DeleteEmployee",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Delete Customer",
     *          required=true, 
     *          type="string",
     *   		@SWG\Schema(
     *               @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="no_induk_karyawan", type="string", example="123456"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        try {

            $success = false;
            DB::beginTransaction();

            try {

                $userId = Auth::id();
                $getIdEmp = DB::table('employees')->select('employees.id as idEmp')
                    ->join('users','users.nik','=', 'employees.no_induk_karyawan')
                    ->where('users.id', '=', $userId)                    
                    ->first();
                $getRole = DB::table('users')
                    ->where('id', '=', $userId)
                    ->select(
                        'role_code'
                    )
                    ->first();
                $roleCode = ['role_code' => $getRole->role_code];

                if($getIdEmp->idEmp == $request->id){
                    $rslt =  $this->ResultReturn(400, 'error', 'tidak bisa hapus diri sendiri');
                    return response()->json($rslt, 400);
                }else{
                    if (in_array('99', $roleCode)) {
                        DB::table('employees')->where('id', '=', $request->id)->delete();
    
                        DB::table('emp_daftar_anak')->where('emp_nik', '=', $request->no_induk_karyawan)->delete();
    
                        DB::table('emp_history_kedisiplinan')->where('emp_nik', '=', $request->no_induk_karyawan)->delete();
    
                        DB::table('emp_pengalaman_kerja')->where('emp_nik', '=', $request->no_induk_karyawan)->delete();
    
                        DB::table('emp_riwayat_pekerjaan')->where('emp_nik', '=', $request->no_induk_karyawan)->delete();
                    } else {
    
                        Db::table('employees')
                        ->where('id', '=',$request->id)
                        ->update([
                            'is_dell' => 1,
                            'updated_at' => Carbon::now(),
                        ]);
                    }
    
                    $success = true;
                    if ($success) {
                        DB::commit();
                    }
                    $rslt =  $this->ResultReturn(200, 'success', 'success');
                    return response()->json($rslt, 200);
                }
                
            } catch (\Exception $e) {
                DB::rollback();
                $success = false;
                throw $e;
            }

        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllEmployeePrintPDF",
     *   tags={"Employee"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Employee",
     *   operationId="GetAllEmployeePrintPDF",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetAllEmployeePrintPDF(Request $request)
    {
        try {
            $employee = Employee::all();
            $pdf = PDF::loadView('employee', compact('employee'));
            $output = $pdf->output();

            return new Response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>  'inline; filename="Employee.pdf"',
            ]);
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllEmployeePrintExcel",
     *   tags={"Employee"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Employee",
     *   operationId="GetAllEmployeePrintExcel",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetAllEmployeePrintExcel(Request $request)
    {
        try {
            return Excel::download(new EmployeeExport, 'Employee.xlsx');
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class AttendanceController extends Controller
{

    /**
     * @SWG\Get(
     *   path="/api/GetAllDailyAttendance",
     *   tags={"Daily Attendance"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Daily Attendance",
     *   operationId="GetAllDailyAttendance",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllDailyAttendance()
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

            $query = DB::table('attendance')
                ->select('*')
                ->where('tanggal', '=', date('Y-m-d'))
                ->orderBy('id', 'desc');

            if (in_array('99', $roleCode)) {
                $query =  $query->get();
                $count = $query->count();
            } else {
                $query =  $query->whereNull('deleted_at')->get();
                $count =  $query->whereNull('deleted_at')->count();
            }
            foreach ($query as $key) {
                $emp_nik = $key->emp_nik;

                if ($key->permit_id != null) {
                    $getPermitName = DB::table('atd_permit')
                        ->leftJoin('atd_jenis_cuti', 'atd_permit.jenis_cuti_id', '=', 'atd_jenis_cuti.id')
                        ->select('atd_jenis_cuti.nama_cuti')
                        ->where('atd_permit.id', '=', $key->permit_id)
                        ->orderBy('atd_jenis_cuti.id', 'desc');
                    if (in_array('99', $roleCode)) {
                        $getPermitName =  $getPermitName->get();
                    } else {
                        $getPermitName =  $getPermitName->whereNull('atd_permit.deleted_at')->get();
                    }
                    foreach ($getPermitName as $permitValue) {
                        $permitName = $permitValue->nama_cuti;
                    }
                } else {
                    $permitName = null;
                }

                if ($key->history_shift_id != null) {
                    $getShiftName = DB::table('atd_history_shift')
                        ->leftJoin('atd_shift', 'atd_history_shift.atd_shift_id', '=', 'atd_shift.id')
                        ->select('atd_shift.keterangan_sift')
                        ->where('atd_history_shift.id', '=', $key->history_shift_id)
                        ->orderBy('atd_history_shift.id', 'desc');
                    if (in_array('99', $roleCode)) {
                        $getShiftName =  $getShiftName->get();
                    } else {
                        $getShiftName =  $getShiftName->whereNull('atd_history_shift.deleted_at')->get();
                    }
                    foreach ($getShiftName as $shiftNameValue) {
                        $shiftName = $shiftNameValue->keterangan_sift;
                    }
                } else {
                    $shiftName = null;
                }

                $empGet = DB::table('employees')
                    ->select(
                        'employees.id',
                        'employees.nama_lengkap',
                        'employees.unit_perusahan',
                        'employees.pangkat',
                        'employees.jabatan',
                        'employees.divisi',
                        'employees.departement'
                    )
                    ->where('no_induk_karyawan', '=', $emp_nik)
                    ->orderBy('id', 'desc');

                if (in_array('99', $roleCode)) {
                    $empGet =  $empGet->get();
                } else {
                    $empGet =  $empGet->whereNull('deleted_at')->get();
                }
                foreach ($empGet as $emp) {
                    //var_dump($qur);
                    $atd[] = [
                        'id'                    => $key->id,
                        'no_induk_karyawan'     => $key->emp_nik,
                        'nama_lengkap'          => $emp->nama_lengkap,
                        'pangkat'               => $emp->pangkat,
                        'jabatan'               => $emp->jabatan,
                        'divisi'                => $emp->divisi,
                        'departement'           => $emp->departement,
                        'check_in'              => $key->check_in,
                        'check_out'             => $key->check_out,
                        'statusCuti'            => $permitName,
                        'tanggal'               => $key->tanggal,
                        'jenisShift'            => $shiftName
                    ];
                }
            }

            $data = ['data' => $atd, 'count' => $count];
            $rslt = $this->ResultReturn(200, 'success', $data);
            return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetDailyAttendanceById",
     *   tags={"Daily Attendance"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get Daily Attendance By Id",
     *   operationId="GetDailyAttendanceById",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *   @SWG\Parameter(name="id", in="query",  type="string", required=true),
     *
     * )
     */

    public function GetDailyAttendanceById(Request $request)
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

            $getAtd = DB::table('attendance')
                ->leftJoin('employees', 'attendance.emp_nik', '=', 'employees.no_induk_karyawan')
                ->select(
                    'attendance.id',
                    'attendance.emp_nik',
                    'attendance.check_in',
                    'attendance.check_out',
                    'attendance.permit_id',
                    'attendance.history_shift_id',
                    'attendance.history_overtime_id',
                    'attendance.tanggal',
                    'employees.nama_lengkap',
                    'employees.unit_perusahan',
                    'employees.pangkat',
                    'employees.jabatan',
                    'employees.divisi',
                    'employees.departement'
                )
                ->where('attendance.id', '=', $request->id);

            if (in_array('99', $roleCode)) {
                $getAtd =  $getAtd->get();
            } else {
                $getAtd =  $getAtd->whereNull('deleted_at')->get();
            }

            if ($getAtd) {
                foreach ($getAtd as $atdKey) {
                    $id                 = $atdKey->id;
                    $empNik             = $atdKey->emp_nik;
                    $checkIn            = $atdKey->check_in;
                    $checkOut           = $atdKey->check_out;
                    $atdTanggal         = $atdKey->tanggal;
                    $empNamaLengkap     = $atdKey->nama_lengkap;
                    $empUnitPerusahaan  = $atdKey->unit_perusahan;
                    $empPangkat         = $atdKey->pangkat;
                    $empJabatan         = $atdKey->jabatan;
                    $empDivisi          = $atdKey->divisi;
                    $empDepartement     = $atdKey->departement;

                    if ($atdKey->permit_id != null) {
                        $getPermitName = DB::table('atd_permit')
                            ->leftJoin('atd_jenis_cuti', 'atd_permit.jenis_cuti_id', '=', 'atd_jenis_cuti.id')
                            ->select(
                                'atd_jenis_cuti.nama_cuti',
                                'atd_permit.tgl_mulai_cuti',
                                'atd_permit.jumlah_cuti',
                                'atd_permit.tgl_akhir_cuti',
                                'atd_permit.keterangan',
                                'atd_permit.status',
                                'atd_permit.sisa_cuti',
                                'atd_permit.tgl_pengajuan_cuti'
                            )
                            ->where('atd_permit.id', '=', $atdKey->permit_id)
                            ->orderBy('atd_jenis_cuti.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getPermitName =  $getPermitName->get();
                        } else {
                            $getPermitName =  $getPermitName->whereNull('atd_permit.deleted_at')->get();
                        }

                        foreach ($getPermitName as $permitValue) {
                            //var_dump($permitValue);
                            $permitNamaCuti         = $permitValue->nama_cuti;
                            $permitTglMulaiCuti     = $permitValue->tgl_mulai_cuti;
                            $permitTglAkhirCuti     = $permitValue->tgl_akhir_cuti;
                            $permitTglPengajuanCuti = $permitValue->tgl_pengajuan_cuti;
                            $permitJumlahCuti       = $permitValue->jumlah_cuti;
                            $permitSisaCuti         = $permitValue->sisa_cuti;
                            $permitKeterangan       = $permitValue->keterangan;
                            $permitStatus           = $permitValue->status;
                        }
                    } else {
                        $permitNamaCuti         = null;
                        $permitTglMulaiCuti     = null;
                        $permitTglAkhirCuti     = null;
                        $permitTglPengajuanCuti = null;
                        $permitJumlahCuti       = null;
                        $permitSisaCuti         = null;
                        $permitKeterangan       = null;
                        $permitStatus           = null;
                    }

                    if ($atdKey->history_shift_id != null) {
                        $getShiftName = DB::table('atd_history_shift')
                            ->leftJoin('atd_shift', 'atd_history_shift.atd_shift_id', '=', 'atd_shift.id')
                            ->select(
                                'atd_history_shift.tanggal',
                                'atd_shift.sechedule_in',
                                'atd_shift.sechedule_out',
                                'atd_shift.keterangan_sift'
                            )
                            ->where('atd_history_shift.id', '=', $atdKey->history_shift_id)
                            ->orderBy('atd_history_shift.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getShiftName =  $getShiftName->get();
                        } else {
                            $getShiftName =  $getShiftName->whereNull('atd_history_shift.deleted_at')->get();
                        }
                        foreach ($getShiftName as $shifttValue) {
                            $shiftSecheduleIn       = $shifttValue->sechedule_in;
                            $shiftSecheduleOut      = $shifttValue->sechedule_out;
                            $shiftKeteranganShift   = $shifttValue->keterangan_sift;
                            $shiftTanggal           = $shifttValue->tanggal;
                        }
                    } else {
                        $shiftSecheduleIn     = null;
                        $shiftSecheduleOut    = null;
                        $shiftKeteranganShift = null;
                        $shiftTanggal         = null;
                    }

                    if ($atdKey->history_overtime_id != null) {
                        $getOvertime = DB::table('atd_history_overtime')
                            ->select(
                                'atd_history_overtime.id',
                                'atd_history_overtime.waktu_lembur',
                                'atd_history_overtime.keterangan_lembur',
                                'atd_history_overtime.tanggal',
                                'atd_history_overtime.status'
                            )
                            ->where('atd_history_overtime.id', '=', $atdKey->history_overtime_id)
                            ->orderBy('atd_history_overtime.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getOvertime =  $getOvertime->get();
                        } else {
                            $getOvertime =  $getOvertime->whereNull('atd_history_overtime.deleted_at')->get();
                        }

                        foreach ($getOvertime as $overTimeValue) {
                            $overTimeWatuLembur         = $overTimeValue->waktu_lembur;
                            $overTimeKeteranganLembur   = $overTimeValue->keterangan_lembur;
                            $overTimeTanggal            = $overTimeValue->tanggal;
                            $overTimeStatus             = $overTimeValue->status;

                            $overTimeName = DB::table('atd_overtime')
                                ->leftJoin('atd_jenis_overtime', 'atd_overtime.jenis_overtime_id', '=', 'atd_jenis_overtime.id')
                                ->select(
                                    'atd_overtime.id',
                                    'atd_jenis_overtime.keterangan',
                                    'atd_jenis_overtime.sechedule_in',
                                    'atd_jenis_overtime.sechedule_out',
                                )
                                ->where('atd_overtime.history_overtime_id', '=', $overTimeValue->id)
                                ->orderBy('atd_overtime.id', 'asc');
                            if (in_array('99', $roleCode)) {
                                $overTimeName =  $overTimeName->get();
                            } else {
                                $overTimeName =  $overTimeName->whereNull('atd_overtime.deleted_at')->get();
                            }
                        }
                    } else {
                        $overTimeName               = null;
                        $overTimeWatuLembur         = null;
                        $overTimeKeteranganLembur   = null;
                        $overTimeTanggal            = null;
                        $overTimeStatus             = null;
                    }
                }

                $data = [
                    'id'                      => $id,
                    'empNik'                  => $empNik,
                    'empNamaLengkap'          => $empNamaLengkap,
                    'empUnitPerusahaan'       => $empUnitPerusahaan,
                    'pangkat'                 => $empPangkat,
                    'jabatan'                 => $empJabatan,
                    'divisi'                  => $empDivisi,
                    'departement'             => $empDepartement,
                    "checkIn"                 => $checkIn,
                    "checkOut"                => $checkOut,
                    "atdTanggal"              => $atdTanggal,
                    "permitNamaCuti"          => $permitNamaCuti,
                    "permitTglMulaiCuti"      => $permitTglMulaiCuti,
                    "permitTglAkhirCuti"      => $permitTglAkhirCuti,
                    "permitTglPengajuanCuti"  => $permitTglPengajuanCuti,
                    "permitJumlahCuti"        => $permitJumlahCuti,
                    "permitSisaCuti"          => $permitSisaCuti,
                    "permitKeterangan"        => $permitKeterangan,
                    "permitStatus"            => $permitStatus,
                    "shiftSecheduleIn"        => $shiftSecheduleIn,
                    "shiftSecheduleOut"       => $shiftSecheduleOut,
                    "shiftKeteranganShift"    => $shiftKeteranganShift,
                    "shiftTanggal"            => $shiftTanggal,
                    "overTimeWatuLembur"      => $overTimeWatuLembur,
                    "overTimeKeteranganLembur" => $overTimeKeteranganLembur,
                    "overTimeTanggal"         => $overTimeTanggal,
                    "overTimeStatus"          => $overTimeStatus,
                    "overTimeList"            => $overTimeName
                ];
            }

            $rslt = $this->ResultReturn(200, 'success', $data);
            return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllHistoryAttendance",
     *   tags={"History Attendance"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All History Attendance",
     *   operationId="GetAllHistoryAttendance",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllHistoryAttendance()
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

            $query = DB::table('attendance')
                ->select('*')
                ->orderBy('id', 'desc');

            if (in_array('99', $roleCode)) {
                $query =  $query->get();
                $count = $query->count();
            } else {
                $query =  $query->whereNull('deleted_at')->get();
                $count =  $query->whereNull('deleted_at')->count();
            }
            foreach ($query as $key) {
                $emp_nik = $key->emp_nik;

                if ($key->permit_id != null) {
                    $getPermitName = DB::table('atd_permit')
                        ->leftJoin('atd_jenis_cuti', 'atd_permit.jenis_cuti_id', '=', 'atd_jenis_cuti.id')
                        ->select('atd_jenis_cuti.nama_cuti')
                        ->where('atd_permit.id', '=', $key->permit_id)
                        ->orderBy('atd_jenis_cuti.id', 'desc');
                    if (in_array('99', $roleCode)) {
                        $getPermitName =  $getPermitName->get();
                    } else {
                        $getPermitName =  $getPermitName->whereNull('atd_permit.deleted_at')->get();
                    }
                    foreach ($getPermitName as $permitValue) {
                        $permitName = $permitValue->nama_cuti;
                    }
                } else {
                    $permitName = null;
                }

                if ($key->history_shift_id != null) {
                    $getShiftName = DB::table('atd_history_shift')
                        ->leftJoin('atd_shift', 'atd_history_shift.atd_shift_id', '=', 'atd_shift.id')
                        ->select('atd_shift.keterangan_sift')
                        ->where('atd_history_shift.id', '=', $key->history_shift_id)
                        ->orderBy('atd_history_shift.id', 'desc');
                    if (in_array('99', $roleCode)) {
                        $getShiftName =  $getShiftName->get();
                    } else {
                        $getShiftName =  $getShiftName->whereNull('atd_history_shift.deleted_at')->get();
                    }
                    foreach ($getShiftName as $shiftNameValue) {
                        $shiftName = $shiftNameValue->keterangan_sift;
                    }
                } else {
                    $shiftName = null;
                }

                $empGet = DB::table('employees')
                    ->select(
                        'employees.id',
                        'employees.nama_lengkap',
                        'employees.unit_perusahan',
                        'employees.pangkat',
                        'employees.jabatan',
                        'employees.divisi',
                        'employees.departement'
                    )
                    ->where('no_induk_karyawan', '=', $emp_nik)
                    ->orderBy('id', 'desc');

                if (in_array('99', $roleCode)) {
                    $empGet =  $empGet->get();
                } else {
                    $empGet =  $empGet->whereNull('deleted_at')->get();
                }
                foreach ($empGet as $emp) {
                    //var_dump($qur);
                    $atd[] = [
                        'id'                    => $key->id,
                        'no_induk_karyawan'     => $key->emp_nik,
                        'nama_lengkap'          => $emp->nama_lengkap,
                        'pangkat'               => $emp->pangkat,
                        'jabatan'               => $emp->jabatan,
                        'divisi'                => $emp->divisi,
                        'departement'           => $emp->departement,
                        'check_in'              => $key->check_in,
                        'check_out'             => $key->check_out,
                        'statusCuti'            => $permitName,
                        'tanggal'               => $key->tanggal,
                        'jenisShift'            => $shiftName
                    ];
                }
            }

            $data = ['data' => $atd, 'count' => $count];
            $rslt = $this->ResultReturn(200, 'success', $data);
            return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetHistoryAttendanceById",
     *   tags={"History Attendance"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get History Attendance By Id",
     *   operationId="GetHistoryAttendanceById",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *   @SWG\Parameter(name="id", in="query",  type="string", required=true),
     *
     * )
     */

    public function GetHistoryAttendanceById(Request $request)
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

            $getAtd = DB::table('attendance')
                ->leftJoin('employees', 'attendance.emp_nik', '=', 'employees.no_induk_karyawan')
                ->select(
                    'attendance.id',
                    'attendance.emp_nik',
                    'attendance.check_in',
                    'attendance.check_out',
                    'attendance.permit_id',
                    'attendance.history_shift_id',
                    'attendance.history_overtime_id',
                    'attendance.tanggal',
                    'employees.nama_lengkap',
                    'employees.unit_perusahan',
                    'employees.pangkat',
                    'employees.jabatan',
                    'employees.divisi',
                    'employees.departement'
                )
                ->where('attendance.id', '=', $request->id);

            if (in_array('99', $roleCode)) {
                $getAtd =  $getAtd->get();
            } else {
                $getAtd =  $getAtd->whereNull('deleted_at')->get();
            }

            if ($getAtd) {
                foreach ($getAtd as $atdKey) {
                    $id                 = $atdKey->id;
                    $empNik             = $atdKey->emp_nik;
                    $checkIn            = $atdKey->check_in;
                    $checkOut           = $atdKey->check_out;
                    $atdTanggal         = $atdKey->tanggal;
                    $empNamaLengkap     = $atdKey->nama_lengkap;
                    $empUnitPerusahaan  = $atdKey->unit_perusahan;
                    $empPangkat         = $atdKey->pangkat;
                    $empJabatan         = $atdKey->jabatan;
                    $empDivisi          = $atdKey->divisi;
                    $empDepartement     = $atdKey->departement;

                    if ($atdKey->permit_id != null) {
                        $getPermitName = DB::table('atd_permit')
                            ->leftJoin('atd_jenis_cuti', 'atd_permit.jenis_cuti_id', '=', 'atd_jenis_cuti.id')
                            ->select(
                                'atd_jenis_cuti.nama_cuti',
                                'atd_permit.tgl_mulai_cuti',
                                'atd_permit.jumlah_cuti',
                                'atd_permit.tgl_akhir_cuti',
                                'atd_permit.keterangan',
                                'atd_permit.status',
                                'atd_permit.sisa_cuti',
                                'atd_permit.tgl_pengajuan_cuti'
                            )
                            ->where('atd_permit.id', '=', $atdKey->permit_id)
                            ->orderBy('atd_jenis_cuti.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getPermitName =  $getPermitName->get();
                        } else {
                            $getPermitName =  $getPermitName->whereNull('atd_permit.deleted_at')->get();
                        }

                        foreach ($getPermitName as $permitValue) {
                            //var_dump($permitValue);
                            $permitNamaCuti         = $permitValue->nama_cuti;
                            $permitTglMulaiCuti     = $permitValue->tgl_mulai_cuti;
                            $permitTglAkhirCuti     = $permitValue->tgl_akhir_cuti;
                            $permitTglPengajuanCuti = $permitValue->tgl_pengajuan_cuti;
                            $permitJumlahCuti       = $permitValue->jumlah_cuti;
                            $permitSisaCuti         = $permitValue->sisa_cuti;
                            $permitKeterangan       = $permitValue->keterangan;
                            $permitStatus           = $permitValue->status;
                        }
                    } else {
                        $permitNamaCuti         = null;
                        $permitTglMulaiCuti     = null;
                        $permitTglAkhirCuti     = null;
                        $permitTglPengajuanCuti = null;
                        $permitJumlahCuti       = null;
                        $permitSisaCuti         = null;
                        $permitKeterangan       = null;
                        $permitStatus           = null;
                    }

                    //var_dump($atdKey);
                    if ($atdKey->history_shift_id != null) {
                        $getShiftName = DB::table('atd_history_shift')
                            ->leftJoin('atd_shift', 'atd_history_shift.atd_shift_id', '=', 'atd_shift.id')
                            ->select(
                                'atd_history_shift.tanggal',
                                'atd_shift.sechedule_in',
                                'atd_shift.sechedule_out',
                                'atd_shift.keterangan_sift'
                            )
                            ->where('atd_history_shift.id', '=', $atdKey->history_shift_id)
                            ->orderBy('atd_history_shift.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getShiftName =  $getShiftName->get();
                        } else {
                            $getShiftName =  $getShiftName->whereNull('atd_history_shift.deleted_at')->get();
                        }
                        foreach ($getShiftName as $shifttValue) {
                            $shiftSecheduleIn       = $shifttValue->sechedule_in;
                            $shiftSecheduleOut      = $shifttValue->sechedule_out;
                            $shiftKeteranganShift   = $shifttValue->keterangan_sift;
                            $shiftTanggal           = $shifttValue->tanggal;
                        }
                    } else {
                        $shiftSecheduleIn     = null;
                        $shiftSecheduleOut    = null;
                        $shiftKeteranganShift = null;
                        $shiftTanggal         = null;
                    }

                    if ($atdKey->history_overtime_id != null) {
                        $getOvertime = DB::table('atd_history_overtime')
                            ->select(
                                'atd_history_overtime.id',
                                'atd_history_overtime.waktu_lembur',
                                'atd_history_overtime.keterangan_lembur',
                                'atd_history_overtime.tanggal',
                                'atd_history_overtime.status'
                            )
                            ->where('atd_history_overtime.id', '=', $atdKey->history_overtime_id)
                            ->orderBy('atd_history_overtime.id', 'desc');
                        if (in_array('99', $roleCode)) {
                            $getOvertime =  $getOvertime->get();
                        } else {
                            $getOvertime =  $getOvertime->whereNull('atd_history_overtime.deleted_at')->get();
                        }

                        foreach ($getOvertime as $overTimeValue) {
                            $overTimeWatuLembur         = $overTimeValue->waktu_lembur;
                            $overTimeKeteranganLembur   = $overTimeValue->keterangan_lembur;
                            $overTimeTanggal            = $overTimeValue->tanggal;
                            $overTimeStatus             = $overTimeValue->status;

                            $overTimeName = DB::table('atd_overtime')
                                ->leftJoin('atd_jenis_overtime', 'atd_overtime.jenis_overtime_id', '=', 'atd_jenis_overtime.id')
                                ->select(
                                    'atd_overtime.id',
                                    'atd_jenis_overtime.keterangan',
                                    'atd_jenis_overtime.sechedule_in',
                                    'atd_jenis_overtime.sechedule_out',
                                )
                                ->where('atd_overtime.history_overtime_id', '=', $overTimeValue->id)
                                ->orderBy('atd_overtime.id', 'asc');
                            if (in_array('99', $roleCode)) {
                                $overTimeName =  $overTimeName->get();
                            } else {
                                $overTimeName =  $overTimeName->whereNull('atd_overtime.deleted_at')->get();
                            }
                        }
                    } else {
                        $overTimeName               = null;
                        $overTimeWatuLembur         = null;
                        $overTimeKeteranganLembur   = null;
                        $overTimeTanggal            = null;
                        $overTimeStatus             = null;
                    }
                }

                $data = [
                    'id'                      => $id,
                    'empNik'                  => $empNik,
                    'empNamaLengkap'          => $empNamaLengkap,
                    'empUnitPerusahaan'       => $empUnitPerusahaan,
                    'pangkat'                 => $empPangkat,
                    'jabatan'                 => $empJabatan,
                    'divisi'                  => $empDivisi,
                    'departement'             => $empDepartement,
                    "checkIn"                 => $checkIn,
                    "checkOut"                => $checkOut,
                    "atdTanggal"              => $atdTanggal,
                    "permitNamaCuti"          => $permitNamaCuti,
                    "permitTglMulaiCuti"      => $permitTglMulaiCuti,
                    "permitTglAkhirCuti"      => $permitTglAkhirCuti,
                    "permitTglPengajuanCuti"  => $permitTglPengajuanCuti,
                    "permitJumlahCuti"        => $permitJumlahCuti,
                    "permitSisaCuti"          => $permitSisaCuti,
                    "permitKeterangan"        => $permitKeterangan,
                    "permitStatus"            => $permitStatus,
                    "shiftSecheduleIn"        => $shiftSecheduleIn,
                    "shiftSecheduleOut"       => $shiftSecheduleOut,
                    "shiftKeteranganShift"    => $shiftKeteranganShift,
                    "shiftTanggal"            => $shiftTanggal,
                    "overTimeWatuLembur"      => $overTimeWatuLembur,
                    "overTimeKeteranganLembur" => $overTimeKeteranganLembur,
                    "overTimeTanggal"         => $overTimeTanggal,
                    "overTimeStatus"          => $overTimeStatus,
                    "overTimeList"            => $overTimeName
                ];
            }

            $rslt = $this->ResultReturn(200, 'success', $data);
            return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            $rslt =  $this->ResultReturn(500, 'error', $ex);
            return response()->json($rslt, 500);
        }
    }
}

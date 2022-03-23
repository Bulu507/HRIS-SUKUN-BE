<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Recruitment;
use App\RecruitmentJobSeeker;
use App\RecruitmentJobSeekerMany;
use App\RecruitmentSchedule;
use App\RecruitmentResult;

class RecruitmentController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetRecruitment",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Recruitment",
     *   operationId="GetRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="department_code",in="query",  type="string"),
     * )
     */
    public function GetRecruitment(Request $request){
        try{
            $getdepartment = $request->department_code;
            if($getdepartment){$department='%'.$getdepartment.'%';}
            else{$department='%%';}

            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
            if (in_array($roleCode, $RoleSA)) {
                $GetRecruitment = DB::table('recruitment')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recruitment.jabatan',
                'recruitment.unit','recruitment.department','ms_department.nama as dept_nama',
                'recruitment.lokasi_penempatan','recruitment.jumlah_kebutuhan','recruitment.source_pemenuhan',
                'recruitment.tanggal_permintaan','recruitment.target_tanggal_pemenuhan','recruitment.status','recruitment.status_schedule',
                'recruitment.is_dell','recruitment.created_at','recruitment.updated_at')
                ->join('ms_department','ms_department.department_code','=','recruitment.department' )
                ->where('ms_department.department_code', 'Like', $department)   
                ->get();
            }else{
                $GetRecruitment = DB::table('recruitment')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recruitment.jabatan',
                'recruitment.unit','recruitment.department','ms_department.nama as dept_nama',
                'recruitment.lokasi_penempatan','recruitment.jumlah_kebutuhan','recruitment.source_pemenuhan',
                'recruitment.tanggal_permintaan','recruitment.target_tanggal_pemenuhan','recruitment.status','recruitment.status_schedule',
                'recruitment.is_dell','recruitment.created_at','recruitment.updated_at')
                ->join('ms_department','ms_department.department_code','=','recruitment.department' )
                ->where('ms_department.department_code', 'Like', $department)   
                ->where('recruitment.is_dell','=',0)
                ->get();
            }
            if(count($GetRecruitment)!=0){ 
                if (in_array($roleCode, $RoleSA)) {
                    $count = DB::table('recruitment')->count();
                }else{
                    $count = DB::table('recruitment')
                    ->where('recruitment.is_dell','=',0)
                    ->count();
                }
                $data = ['count'=>$count, 'data'=>$GetRecruitment];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetJobSeeker",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get JobSeeker",
     *   operationId="GetJobSeeker",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetJobSeeker(Request $request){
        try{
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
                
            if (in_array($roleCode, $RoleSA)) {
                $GetJobSeeker = DB::table('recr_job_seeker')->get();
            }else{
                $GetJobSeeker = DB::table('recr_job_seeker')
                ->where('recr_job_seeker.is_dell','=',0)
                ->get();
            }

            if(count($GetJobSeeker)!=0){ 
                if (in_array($roleCode, $RoleSA)) {
                    $count = DB::table('recr_job_seeker')->count();
                }else{
                    $count = DB::table('recr_job_seeker')
                    ->where('recr_job_seeker.is_dell','=',0)
                    ->count();
                }
                $data = ['count'=>$count, 'data'=>$GetJobSeeker];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetSchedule",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Schedule",
     *   operationId="GetSchedule",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="department_code",in="query",  type="string"),
     * )
     */
    public function GetSchedule(Request $request){
        try{
            $getdepartment = $request->department_code;
            if($getdepartment){$department='%'.$getdepartment.'%';}
            else{$department='%%';}

            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
                    
            if (in_array($roleCode, $RoleSA)) {
                $GetSchedule = DB::table('recruitment')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recruitment.jabatan',
                'recruitment.unit','recruitment.department','ms_department.nama as dept_nama',
                'recruitment.lokasi_penempatan','recruitment.jumlah_kebutuhan','recruitment.source_pemenuhan',
                'recruitment.tanggal_permintaan','recruitment.target_tanggal_pemenuhan','recruitment.status','recruitment.status_schedule',
                'recruitment.is_dell','recruitment.created_at','recruitment.updated_at')
                ->join('ms_department','ms_department.department_code','=','recruitment.department' )
                ->where('ms_department.department_code', 'Like', $department) 
                ->where('recruitment.status','=','On_Progress')->get();
            }else{
                $GetSchedule = DB::table('recruitment')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recruitment.jabatan',
                'recruitment.unit','recruitment.department','ms_department.nama as dept_nama',
                'recruitment.lokasi_penempatan','recruitment.jumlah_kebutuhan','recruitment.source_pemenuhan',
                'recruitment.tanggal_permintaan','recruitment.target_tanggal_pemenuhan','recruitment.status','recruitment.status_schedule',
                'recruitment.is_dell','recruitment.created_at','recruitment.updated_at')
                ->join('ms_department','ms_department.department_code','=','recruitment.department' )
                ->where('ms_department.department_code', 'Like', $department) 
                ->where('recruitment.status','=','On_Progress')
                ->where('recruitment.is_dell','=',0)
                ->get();
            }

            if(count($GetSchedule)!=0){ 
                if (in_array($roleCode, $RoleSA)) {
                    $count = DB::table('recruitment')
                    ->where('recruitment.status','=','On_Progress')->count();
                }else{
                    $count = DB::table('recruitment')
                    ->where('recruitment.status','=','On_Progress')
                    ->where('recruitment.is_dell','=',0)
                    ->count();
                }
                $data = ['count'=>$count, 'data'=>$GetSchedule];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetResult",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Result",
     *   operationId="GetResult",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetResult(Request $request){
        try{
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
                
            if (in_array($roleCode, $RoleSA)) {
                $GetResult = DB::table('recr_result')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recr_result.list_job_seeker',
                'recr_result.status','recr_result.is_dell','recr_result.created_at','recr_result.updated_at')
                ->join('recruitment','recruitment.id_recruitment','=', 'recr_result.code_recruitment')
                ->where('recruitment.status','=','Terpenuhi')->get();
            }else{
                $GetResult = DB::table('recr_result')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recr_result.list_job_seeker',
                'recr_result.status','recr_result.is_dell','recr_result.created_at','recr_result.updated_at')
                ->join('recruitment','recruitment.id_recruitment','=', 'recr_result.code_recruitment')
                ->where('recruitment.status','=','Terpenuhi')
                ->where('recruitment.is_dell','=',0)
                ->get();
            }

            if(count($GetResult)!=0){ 
                if (in_array($roleCode, $RoleSA)) {
                    $count = DB::table('recr_result')
                    ->join('recruitment','recruitment.id_recruitment','=', 'recr_result.code_recruitment')
                    ->where('recruitment.status','=','Terpenuhi')->count();
                }else{
                    $count = DB::table('recr_result')
                    ->join('recruitment','recruitment.id_recruitment','=', 'recr_result.code_recruitment')
                    ->where('recruitment.status','=','Terpenuhi')
                    ->where('recruitment.is_dell','=',0)
                    ->count();
                }

                
            $datavalresult = [];
            $listval=array();
                foreach ($GetResult as $val){

                    $list_job_seeker = $val->list_job_seeker;
                    $arrayjobseeker = json_decode($list_job_seeker);
                    
                    $datavaljobseeker = [];
                    $listvaljobseeker=array();
                    foreach ($arrayjobseeker as $valemp){
                        $jobseeker = DB::table('recr_job_seeker')
                        ->select('recr_job_seeker.id_job_seeker','recr_job_seeker.nama')
                        ->where('recr_job_seeker.id_job_seeker','=',$valemp)
                        ->first();

                        $datavaljobseeker = [ 'id_job_seeker'=>  $jobseeker->id_job_seeker, 'nama'=>$jobseeker->nama];
                        array_push($listvaljobseeker,$datavaljobseeker);
                    }
                        
                    $datavalresult = [ 'id_recruitment'=>  $val->id_recruitment, 'nama_recruitment'=>$val->nama_recruitment,
                                        'status'=>$val->status, 'is_dell'=>$val->is_dell,'created_at'=>$val->created_at,'updated_at'=>$val->updated_at,
                                        'listvaljobseeker'=> $listvaljobseeker];
                    array_push($listval,$datavalresult);
                }

                $data = ['count'=>$count, 'data'=>$listval];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetDetailRecruitment",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Detail Recruitment",
     *   operationId="GetDetailRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="id_recruitment",in="query", type="string", description="id")
     * )
     */
    public function GetDetailRecruitment(Request $request){
        $validator = Validator::make($request->all(), [ 
            'id_recruitment' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try{
            $GetDetailRecruitment = DB::table('recruitment')
                ->select('recruitment.id_recruitment', 'recruitment.nama_recruitment','recruitment.jabatan',
                'recruitment.unit','recruitment.department','ms_department.nama as dept_nama',
                'recruitment.lokasi_penempatan','recruitment.jumlah_kebutuhan','recruitment.source_pemenuhan',
                'recruitment.tanggal_permintaan','recruitment.target_tanggal_pemenuhan','recruitment.status','recruitment.status_schedule',
                'recruitment.is_dell','recruitment.created_at','recruitment.updated_at')
                ->join('ms_department','recruitment.department','=', 'ms_department.department_code')
                ->where('recruitment.id_recruitment','=',$request->id_recruitment)
                ->first();

            if($GetDetailRecruitment){ 
                $GetJobSeekerAll = DB::table('recr_job_seeker_many')
                ->select('recr_job_seeker.id_job_seeker', 'recr_job_seeker.no_ktp','recr_job_seeker.nama',
                'recr_job_seeker.telp','recr_job_seeker.email','recr_job_seeker.alamat','recr_job_seeker.berkas_1',
                'recr_job_seeker.berkas_2','recr_job_seeker.berkas_3','recr_job_seeker.is_dell','recr_job_seeker.created_by')
                ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                ->where('recr_job_seeker_many.code_recruitment','=',$request->id_recruitment)->get();

                $GetSchedule = DB::table('recr_schedule')
                ->where('recr_schedule.code_recruitment','=',$request->id_recruitment)->get();

                $datavalschedule = [];
                $listvalschedule=array();
                if(count($GetSchedule)>0){
                    foreach ($GetSchedule as $val){
    
                        $list_job_seeker = $val->list_job_seeker;
                        $arrayjobseeker = json_decode($list_job_seeker);
                        
                        $jmlh_job_seeker = 0;
                        $datavaljobseeker = [];
                        $listvaljobseeker=array();
                        if(count($arrayjobseeker)>0){
                            foreach ($arrayjobseeker as $valemp){
                                $jobseeker = DB::table('recr_job_seeker')
                                ->select('recr_job_seeker.id_job_seeker','recr_job_seeker.nama')
                                ->where('recr_job_seeker.id_job_seeker','=',$valemp)
                                ->first();
        
                                $datavaljobseeker = [ 'id_job_seeker'=>  $jobseeker->id_job_seeker, 'nama'=>$jobseeker->nama];
                                array_push($listvaljobseeker,$datavaljobseeker);
                            }
    
                            $jmlh_job_seeker = count($arrayjobseeker);
                        }
                            
                        $datavalschedule = [ 'id_schedule'=>  $val->id_schedule, 'nama_schedule'=>$val->nama_schedule,
                                            'tanggal_schedule'=>$val->tanggal_schedule, 'no_urut_schedule'=>$val->no_urut_schedule,
                                            'created_at'=>$val->created_at,'created_by'=>$val->created_by, 
                                            'jmlh_job_seeker'=> $jmlh_job_seeker,'listvaljobseeker'=> $listvaljobseeker];
                        array_push($listvalschedule,$datavalschedule);
                    }
                }

                $GetResult = DB::table('recr_result')
                ->where('recr_result.code_recruitment','=',$request->id_recruitment)->get();

                $datavalrslt = [];
                $listvalrslt=array();
                if(count($GetResult)>0){
                    foreach ($GetResult as $val){
    
                        $list_job_seeker = $val->list_job_seeker;
                        $arrayjobseeker = json_decode($list_job_seeker);
                        
                        $jmlh_job_seeker = 0;
                        $datavaljobseeker = [];
                        $listvaljobseeker=array();
                        
                        if(count($arrayjobseeker)>0){
                            foreach ($arrayjobseeker as $valemp){
                                $jobseeker = DB::table('recr_job_seeker')
                                ->select('recr_job_seeker.id_job_seeker','recr_job_seeker.nama')
                                ->where('recr_job_seeker.id_job_seeker','=',$valemp)
                                ->first();
        
                                $datavaljobseeker = [ 'id_job_seeker'=>  $jobseeker->id_job_seeker, 'nama'=>$jobseeker->nama];
                                array_push($listvaljobseeker,$datavaljobseeker);
                            }
                            $jmlh_job_seeker = count($arrayjobseeker);
                        }
                            
                        $datavalrslt = [ 'id_result'=>  $val->id_result, 'status'=>$val->status,
                                            'created_at'=>$val->created_at,'created_by'=>$val->created_by, 
                                            'jmlh_job_seeker'=> $jmlh_job_seeker,'listvaljobseeker'=> $listvaljobseeker];
                        array_push($listvalrslt,$datavalrslt);
                    }
                }

                $data = ['GetDetailRecruitment'=>$GetDetailRecruitment, 'GetJobSeekerAll'=>$GetJobSeekerAll,
                'GetSchedule'=>$listvalschedule, 'GetResult'=>$listvalrslt];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetJobSeekerByRecruitment",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get JobSeeker By Recruitment",
     *   operationId="GetJobSeekerByRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetJobSeekerByRecruitment(Request $request){
        $validator = Validator::make($request->all(), [ 
            'id_recruitment' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try{
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
                
            if (in_array($roleCode, $RoleSA)) {
                $GetJobSeekerAll = DB::table('recr_job_seeker_many')
                ->select('recr_job_seeker.id_job_seeker', 'recr_job_seeker.no_ktp','recr_job_seeker.nama',
                'recr_job_seeker.telp','recr_job_seeker.email','recr_job_seeker.alamat','recr_job_seeker.berkas_1',
                'recr_job_seeker.berkas_2','recr_job_seeker.berkas_3','recr_job_seeker.is_dell','recr_job_seeker.created_by')
                ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                ->where('recr_job_seeker_many.code_recruitment','=',$request->id_recruitment)->get();
            }else{
                $GetJobSeekerAll = DB::table('recr_job_seeker_many')
                ->select('recr_job_seeker.id_job_seeker', 'recr_job_seeker.no_ktp','recr_job_seeker.nama',
                'recr_job_seeker.telp','recr_job_seeker.email','recr_job_seeker.alamat','recr_job_seeker.berkas_1',
                'recr_job_seeker.berkas_2','recr_job_seeker.berkas_3','recr_job_seeker.is_dell','recr_job_seeker.created_by')
                ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                ->where('recr_job_seeker_many.code_recruitment','=',$request->id_recruitment)
                ->where('recr_job_seeker.is_dell','=',0)
                ->get();
            }

            if(count($GetJobSeekerAll)!=0){ 
                if (in_array($roleCode, $RoleSA)) {
                    $count = DB::table('recr_job_seeker_many')
                    ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                    ->where('recr_job_seeker_many.code_recruitment','=',$request->id_recruitment)->count();
                }else{
                    $count = DB::table('recr_job_seeker_many')
                    ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                    ->where('recr_job_seeker_many.code_recruitment','=',$request->id_recruitment)
                    ->where('recruitment.is_dell','=',0)
                    ->count();
                }
                $data = ['count'=>$count, 'data'=>$GetJobSeekerAll];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetJobSeekerStatus",
     *   tags={"Recruitment"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get JobSeeker Status",
     *   operationId="GetJobSeekerStatus",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetJobSeekerStatus(Request $request){
        $validator = Validator::make($request->all(), [ 
            'id_job_seeker' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try{
                $GetJobSeekerAll = DB::table('recr_job_seeker_many')
                ->select('recr_job_seeker.id_job_seeker', 'recr_job_seeker.no_ktp','recr_job_seeker.nama',
                'recr_job_seeker.telp','recr_job_seeker.email','recr_job_seeker.alamat','recr_job_seeker.berkas_1',
                'recr_job_seeker.berkas_2','recr_job_seeker.berkas_3','recr_job_seeker.is_dell',
                'recr_job_seeker.created_by','recr_job_seeker_many.code_recruitment')
                ->join('recr_job_seeker','recr_job_seeker.id_job_seeker','=', 'recr_job_seeker_many.code_job_seeker')
                ->where('recr_job_seeker_many.code_job_seeker','=',$request->id_job_seeker)
                ->get();

            if(count($GetJobSeekerAll)!=0){ 
                
                $datavaljobseeker = [];
                $listvaljobseeker=array();
                foreach ($GetJobSeekerAll as $val){
                    $GetRecruitment = DB::table('recruitment')
                        ->where('recruitment.id_recruitment','=',$val->code_recruitment)->first();

                    $GetSchedule = DB::table('recr_schedule')
                        ->where('recr_schedule.code_recruitment','=',$val->code_recruitment)->get();
                    
                        if(count($GetSchedule)>0){
                            foreach ($GetSchedule as $valschedule){
            
                                $list_job_seeker = $valschedule->list_job_seeker;
                                $arrayjobseeker = json_decode($list_job_seeker);

                                $listschedulejobseeker=array();
                                if(count($arrayjobseeker)>0){
                                    if (in_array($val->id_job_seeker, $arrayjobseeker)){
                                        array_push($listschedulejobseeker,$valschedule->nama_schedule);
                                    }
                                }
                            }
                        }
                                
                        $datavaljobseeker = [ 'id_job_seeker'=>  $val->id_job_seeker, 'nama'=>$val->nama,
                                            'no_ktp'=>$val->no_ktp, 'telp'=>$val->telp,'email'=>$val->email,'alamat'=>$val->alamat,
                                            'berkas_1'=>$val->berkas_1,'berkas_2'=>$val->berkas_2,'berkas_3'=>$val->berkas_3, 
                                            'listschedulejobseeker'=> $listschedulejobseeker, 
                                            'code_recruitment'=>$val->code_recruitment, 'nama_recruitment'=>$GetRecruitment->nama_recruitment];
                        array_push($listvaljobseeker,$datavaljobseeker);
                }

                $data = $listvaljobseeker;
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddRecruitment",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Recruitment",
     *   operationId="AddRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Recruitment",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="nama_recruitment", type="string", example="nama"),
     *              @SWG\Property(property="jabatan", type="string", example="jabatan"),
     *              @SWG\Property(property="department", type="string", example="department"),
     *              @SWG\Property(property="lokasi_penempatan", type="string", example="lokasi_penempatan"),
     *              @SWG\Property(property="jumlah_kebutuhan", type="string", example="jumlah_kebutuhan"),
     *              @SWG\Property(property="source_pemenuhan", type="string", example="internal/external"),
     *              @SWG\Property(property="tanggal_permintaan", type="string", example="2021-10-30"),
     *              @SWG\Property(property="target_tanggal_pemenuhan", type="string", example="2021-10-30"),
     *              @SWG\Property(property="listSchedule", type="string", example="encode list isian nama_schedule dan tanggal_schedule"),
     *          ),
     *      )
     * )
     *
     */

    public function AddRecruitment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'nama_recruitment' => 'required',        
            'jabatan' => 'required',        
            'department' => 'required',        
            'lokasi_penempatan' => 'required',        
            'jumlah_kebutuhan' => 'required',        
            'source_pemenuhan' => 'required',     
            'tanggal_permintaan' => 'required',     
            'target_tanggal_pemenuhan' => 'required',     
            'listSchedule' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {

            $getLastId= Recruitment::orderBy('id_recruitment','desc')->first(); 
            if($getLastId){
                $code = 'Recr_'.str_pad(((int)substr($getLastId->id_recruitment,-7) + 1), 7, '0', STR_PAD_LEFT);
            }else{
                $code = 'Recr_0000001';
            }
            
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;

            Recruitment::create([
                'id_recruitment' => $code,
                'nama_recruitment' => $request->nama_recruitment,
                'jabatan' => $request->jabatan,
                'department' => $request->department,
                'lokasi_penempatan' => $request->lokasi_penempatan,
                'jumlah_kebutuhan' => $request->jumlah_kebutuhan,
                'source_pemenuhan' => $request->source_pemenuhan,
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'target_tanggal_pemenuhan' => $request->target_tanggal_pemenuhan,
                'status' => 'On_Progress',
                'status_schedule' => 'Buka_Lowongan',
                'created_by' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $listSchedule = json_decode($request->listSchedule, true);
            if (count($listSchedule) != 0) {
                foreach ($listSchedule as $key) {

                    $getLastIdSchedule= RecruitmentSchedule::orderBy('id_schedule','desc')->first(); 
                    if($getLastIdSchedule){
                        $codeSchedule = 'Sche_'.str_pad(((int)substr($getLastIdSchedule->id_schedule,-10) + 1), 10, '0', STR_PAD_LEFT);
                    }else{
                        $codeSchedule = 'Sche_0000000001';
                    }

                    $daftarSchedule = array(
                        'id_schedule'      => $codeSchedule,
                        'code_recruitment' => $code,
                        'list_job_seeker'  => '[]',
                        'nama_schedule'    => $key['nama_schedule'],
                        'tanggal_schedule' => $key['tanggal_schedule'],
                        'created_by' => $userId,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    );

                    RecruitmentSchedule::create($daftarSchedule);
                }
            }
            
            $getLastIdRslt= RecruitmentResult::orderBy('id_result','desc')->first(); 
            if($getLastIdRslt){
                $codeRslt = 'Rslt_'.str_pad(((int)substr($getLastIdRslt->id_result,-7) + 1), 7, '0', STR_PAD_LEFT);
            }else{
                $codeRslt = 'Rslt_0000001';
            }

            RecruitmentResult::create([
                'code_recruitment' => $code,
                'id_result' => $codeRslt,
                'list_job_seeker' => '[]',
                'status' => 'Pending',
                'created_by' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateRecruitment",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Recruitment",
     *   operationId="UpdateRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Recruitment",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_recruitment", type="string", example="nama"),
     *              @SWG\Property(property="nama_recruitment", type="string", example="nama"),
     *              @SWG\Property(property="jabatan", type="string", example="jabatan"),
     *              @SWG\Property(property="department", type="string", example="department"),
     *              @SWG\Property(property="lokasi_penempatan", type="string", example="lokasi_penempatan"),
     *              @SWG\Property(property="jumlah_kebutuhan", type="string", example="jumlah_kebutuhan"),
     *              @SWG\Property(property="source_pemenuhan", type="string", example="internal/external"),
     *              @SWG\Property(property="tanggal_permintaan", type="string", example="2021-10-30"),
     *              @SWG\Property(property="target_tanggal_pemenuhan", type="string", example="2021-10-30"),
     *              @SWG\Property(property="listSchedule", type="string", example="encode list isian nama_schedule dan tanggal_schedule"),
     *          ),
     *      )
     * )
     *
     */

    public function UpdateRecruitment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_recruitment' => 'required',                
            'nama_recruitment' => 'required',        
            'jabatan' => 'required',       
            'department' => 'required',        
            'lokasi_penempatan' => 'required',        
            'jumlah_kebutuhan' => 'required',        
            'source_pemenuhan' => 'required',     
            'tanggal_permintaan' => 'required',     
            'target_tanggal_pemenuhan' => 'required',     
            'listSchedule' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {
            
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;
            
            Recruitment::where('id_recruitment', '=', $request->id_recruitment)
            ->update([
                'nama_recruitment' => $request->nama_recruitment,
                'jabatan' => $request->jabatan,
                'department' => $request->department,
                'lokasi_penempatan' => $request->lokasi_penempatan,
                'jumlah_kebutuhan' => $request->jumlah_kebutuhan,
                'source_pemenuhan' => $request->source_pemenuhan,
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'target_tanggal_pemenuhan' => $request->target_tanggal_pemenuhan,
                // 'status' => 'On_Progress',
                // 'status_schedule' => 'Buka_Lowongan',
                'updated_by' => $userId,
                'updated_at' => Carbon::now(),
            ]);
            
            // $getSchedule= RecruitmentSchedule::where('code_recruitment',$request->id_recruitment)->get();
            // if(count($getSchedule)>0){                
            //     DB::table('recr_schedule')->where('code_recruitment', $request->id_recruitment)->delete();
            // } 

            $listSchedule = json_decode($request->listSchedule, true);
            if (count($listSchedule) != 0) {
                foreach ($listSchedule as $key) {
                    
                    // $RecruitmentSchedule = RecruitmentSchedule::find($key['id_schedule']);

                    RecruitmentSchedule::where('id_schedule', '=', $key['id_schedule'])
                    ->update([
                        'nama_schedule'    => $key['nama_schedule'],
                        'tanggal_schedule' => $key['tanggal_schedule'],
                        'updated_by' => $userId,
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateStatusRecruitment",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Status Recruitment",
     *   operationId="UpdateStatusRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Status Recruitment",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_recruitment", type="string", example="nama"),
     *              @SWG\Property(property="status_schedule", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */

    public function UpdateStatusRecruitment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_recruitment' => 'required',                
            'status_schedule' => 'required',   
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {
            
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;
            
            Recruitment::where('id_recruitment', '=', $request->id_recruitment)
            ->update([
                'status_schedule' => $request->status_schedule,
                'updated_by' => $userId,
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteRecruitment",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Recruitment",
     *   operationId="DeleteRecruitment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Recruitment",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_recruitment", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteRecruitment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_recruitment' => 'required',                
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {

            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
            
            if (in_array($roleCode, $RoleSA)) {
                DB::table('recruitment')->where('id_recruitment', '=', $request->id_recruitment)->delete();
                DB::table('recr_schedule')->where('code_recruitment', '=', $request->id_recruitment)->delete();
                DB::table('recr_result')->where('code_recruitment', '=', $request->id_recruitment)->delete();
            }else{
                
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;

                Db::table('recruitment')
                        ->where('id_recruitment', '=',$request->id_recruitment)
                        ->update([
                            'is_dell' => 1,
                            'updated_by' => $userId,
                            'updated_at' => Carbon::now(),
                        ]);
            }

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateSchedule",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Schedule",
     *   operationId="UpdateSchedule",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Schedule",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_schedule", type="string", example="nama"),
     *              @SWG\Property(property="nama_schedule", type="string", example="nama"),
     *              @SWG\Property(property="tanggal_schedule", type="string", example="2021-10-30"),
     *              @SWG\Property(property="list_job_seeker", type="string", example="encode list isian nama_schedule dan tanggal_schedule"),
     *          ),
     *      )
     * )
     *
     */

    public function UpdateSchedule(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_schedule' => 'required',            
            'nama_schedule' => 'required',          
            'tanggal_schedule' => 'required',   
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {
            
            
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;
            
            // $RecruitmentSchedule = RecruitmentSchedule::find($request->id_schedule);
            $list = $request->list_job_seeker;            
            $listjobseeker=array();
            if( $list){
                foreach ($list as $key) {
                    array_push($listjobseeker,$key['id_job_seeker']);
                }
            }

            RecruitmentSchedule::where('id_schedule', '=', $request->id_schedule)
            ->update([
                'list_job_seeker'  => $listjobseeker,
                'nama_schedule'    => $request->nama_schedule,
                'tanggal_schedule' => $request->tanggal_schedule,
                'updated_by' => $userId,
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateResult",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Result",
     *   operationId="UpdateResult",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Result",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_result", type="string", example="nama"),
     *              @SWG\Property(property="status", type="string", example="nama"),
     *              @SWG\Property(property="list_job_seeker", type="string", example="encode list isian nama_schedule dan tanggal_schedule"),
     *          ),
     *      )
     * )
     *
     */

    public function UpdateResult(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_result' => 'required',               
            'status' => 'required',    
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {
            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;
            
            // $RecruitmentResult = RecruitmentResult::find($request->id_result);
            // $list = $request->list_job_seeker;
            
            // $listjobseeker=array();
            // foreach ($list as $key) {
            //     array_push($listjobseeker,$key['id_job_seeker']);
            // }

            $list = $request->list_job_seeker;            
            $listjobseeker=array();
            $status = 'Pending';
            if( $list){
                foreach ($list as $key) {
                    array_push($listjobseeker,$key['id_job_seeker']);
                }
                $status = $request->status;
            }
            RecruitmentResult::where('id_result', '=', $request->id_result)
            ->update([
                'list_job_seeker'  => $listjobseeker,
                'status'    => $status,
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/AddJobSeeker",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add JobSeeker",
     *   operationId="AddJobSeeker",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add JobSeeker",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="no_ktp", type="string", example="no_ktp"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *              @SWG\Property(property="telp", type="string", example="telp"),
     *              @SWG\Property(property="alamat", type="string", example="alamat"),
     *              @SWG\Property(property="email", type="string", example="email"),
     *              @SWG\Property(property="code_recruitment", type="string", example="code_recruitment"),
     *              @SWG\Property(property="berkas_1", type="string", example="nullable"),
     *              @SWG\Property(property="berkas_2", type="string", example="nullable"),
     *              @SWG\Property(property="berkas_3", type="string", example="nullable"),
     *          ),
     *      )
     * )
     *
     */

    public function AddJobSeeker(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'no_ktp' => 'required',        
            'nama' => 'required',        
            'telp' => 'required',        
            'alamat' => 'required',        
            'email' => 'required',        
            'code_recruitment' => 'required',        
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {

            $getLastId= RecruitmentJobSeeker::orderBy('id_job_seeker','desc')->first(); 
            if($getLastId){
                $code = 'JSK_'.str_pad(((int)substr($getLastId->id_job_seeker,-11) + 1), 11, '0', STR_PAD_LEFT);
            }else{
                $code = 'JSK_00000000001';
            }

            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;

            RecruitmentJobSeeker::create([
                'id_job_seeker' => $code,
                'no_ktp' => $request->no_ktp,
                'nama' => $request->nama,
                'telp' => $request->telp,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'berkas_1' => $this->ReplaceNull($request->berkas_1, 'string'),
                'berkas_2' => $this->ReplaceNull($request->berkas_2, 'string'),
                'berkas_3' => $this->ReplaceNull($request->berkas_3, 'string'),
                'created_by' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            RecruitmentJobSeekerMany::create([
                'code_job_seeker' => $code,
                'code_recruitment' => $request->code_recruitment,
                'created_by' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/UpdateJobSeeker",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update JobSeeker",
     *   operationId="UpdateJobSeeker",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update JobSeeker",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_job_seeker", type="string", example="id_job_seeker"),
     *              @SWG\Property(property="no_ktp", type="string", example="no_ktp"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *              @SWG\Property(property="telp", type="string", example="telp"),
     *              @SWG\Property(property="alamat", type="string", example="alamat"),
     *              @SWG\Property(property="email", type="string", example="email"),
     *              @SWG\Property(property="code_recruitment", type="string", example="code_recruitment"),
     *              @SWG\Property(property="berkas_1", type="string", example="nullable"),
     *              @SWG\Property(property="berkas_2", type="string", example="nullable"),
     *              @SWG\Property(property="berkas_3", type="string", example="nullable"),
     *          ),
     *      )
     * )
     *
     */

    public function UpdateJobSeeker(Request $request)
    {   
        $validator = Validator::make($request->all(), [          
            'id_job_seeker' => 'required',                 
            'no_ktp' => 'required',        
            'nama' => 'required',        
            'telp' => 'required',        
            'alamat' => 'required',        
            'email' => 'required',        
            'code_recruitment' => 'required',        
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {

            $idUSer = Auth::id();
            $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
            $userId = $getIdAdmin->user_id;

            // $RecruitmentJobSeeker = RecruitmentJobSeeker::find($request->id_job_seeker);

            RecruitmentJobSeeker::where('id_job_seeker', '=', $request->id_job_seeker)
            ->update([                
                'no_ktp' => $request->no_ktp,
                'nama' => $request->nama,
                'telp' => $request->telp,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'berkas_1' => $this->ReplaceNull($request->berkas_1, 'string'),
                'berkas_2' => $this->ReplaceNull($request->berkas_2, 'string'),
                'berkas_3' => $this->ReplaceNull($request->berkas_3, 'string'),
                'updated_by' => $userId,
                'updated_at' => Carbon::now(),
            ]);

            
            // $RecruitmentJobSeekerMany = RecruitmentJobSeekerMany::where('code_job_seeker', $request->id_job_seeker)->first();

            RecruitmentJobSeekerMany::where('code_job_seeker', '=', $request->id_job_seeker)
            ->update([
                'code_recruitment' => $request->code_recruitment,
                'created_by' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/DeleteJobSeeker",
	 *   tags={"Recruitment"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete JobSeeker",
     *   operationId="DeleteJobSeeker",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete JobSeeker",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id_job_seeker", type="string", example="id_job_seeker"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteJobSeeker(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id_job_seeker' => 'required',                
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        
        DB::beginTransaction();
        try {

            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = $getRole->role_code;
            $RoleSA = array("99", "88");
            
            if (in_array($roleCode, $RoleSA)) {
                DB::table('recr_job_seeker')->where('id_job_seeker', '=', $request->id_job_seeker)->delete();
                DB::table('recr_job_seeker_many')->where('code_job_seeker', '=', $request->id_job_seeker)->delete();
            }else{
                
                $idUSer = Auth::id();
                $getIdAdmin= DB::table('users')->select('user_id')->where('id',$idUSer)->first(); 
                $userId = $getIdAdmin->user_id;
                Db::table('recr_job_seeker')
                        ->where('id_job_seeker', '=',$request->id_job_seeker)
                        ->update([
                            'is_dell' => 1,
                            'updated_by' => $userId,
                            'updated_at' => Carbon::now(),
                        ]);
            }

            DB::commit();            
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            DB::rollback();
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
}

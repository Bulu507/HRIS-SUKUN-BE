<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserProfile;
use App\UserMenu;
use App\Admin;
use App\AdminMenu;
use App\PendingTask;
use App\WarungTonggo;
use App\KentonganLapor;

use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetPendingTask",
     *   tags={"Task"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get PendingTask",
     *   operationId="GetPendingTask",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetPendingTask(Request $request){
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetPendingTask = DB::table('pending_task')
            ->select('pending_task.id','pending_task.menu_code','menu_access.name as nama_menu',
            'pending_task.user_id','users.name','pending_task.laporan_code','pending_task.judul',
            'pending_task.status','pending_task.admin_id','pending_task.created_at')
            ->join('users','users.admin_user_code','=', 'pending_task.user_id')
            ->join('menu_access','menu_access.id','=', 'pending_task.menu_code')
            ->orderBy('pending_task.created_at','desc')
            ->get();
            if(count($GetPendingTask)!=0){ 
                $count = DB::table('pending_task')
                ->join('users','users.admin_user_code','=', 'pending_task.user_id')
                ->join('menu_access','menu_access.id','=', 'pending_task.menu_code')
                ->count();
                $data = ['count'=>$count, 'data'=>$GetPendingTask];
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
     *   path="/api/GetDetailPendingTask",
     *   tags={"Task"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Detail PendingTask",
     *   operationId="GetDetailPendingTask",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="id",in="query", required=true, type="string")
     * )
     */
    public function GetDetailPendingTask(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            // var_dump(count($GetLahanNotComplete));
            $GetDetailPendingTask = DB::table('pending_task')
            ->select('pending_task.id','pending_task.menu_code','menu_access.name as nama_menu',
            'pending_task.user_id','users.name','pending_task.laporan_code','pending_task.judul',
            'pending_task.status','pending_task.admin_id','pending_task.created_at')
            ->join('users','users.admin_user_code','=', 'pending_task.user_id')
            ->join('menu_access','menu_access.id','=', 'pending_task.menu_code')
            ->where('pending_task.id', '=', $request->id)
            ->first();
            if($GetDetailPendingTask){ 
                if($GetDetailPendingTask->menu_code == '13'){
                    $GetDetailTask = DB::table('warung_tonggo')
                    ->select('warung_tonggo.id','warung_tonggo.user_id','users.name as nama_user',
                    'warung_tonggo.nama as judul','warung_tonggo.kategori as kategori','warung_tonggo.lokasi as isi',
                    'warung_tonggo.no_telp',
                    'warung_tonggo.status','warung_tonggo.created_at')
                    ->join('users','users.admin_user_code','=', 'warung_tonggo.user_id')
                    ->where('warung_tonggo.id', '=', $GetDetailPendingTask->laporan_code)
                    ->first();

                    $data = ['GetDetailPendingTask'=>$GetDetailPendingTask, 'GetDetailTask'=>$GetDetailTask];
                    $rslt =  $this->ResultReturn(200, 'success', $data);
                    return response()->json($rslt, 200);

                }elseif($GetDetailPendingTask->menu_code == '16'){
                    $GetDetailTask = DB::table('kentongan_lapor')
                    ->select('kentongan_lapor.id','kentongan_lapor.user_id','users.name as nama_user',
                    'kentongan_lapor.judul as judul','kentongan_lapor.isi_berita as isi','kentongan_lapor.status_berita',
                    'kentongan_lapor.status','kentongan_lapor.tanggal_laporan as kategori','kentongan_lapor.created_at')
                    ->join('users','users.admin_user_code','=', 'kentongan_lapor.user_id')
                    ->where('kentongan_lapor.id', '=', $GetDetailPendingTask->laporan_code)
                    ->first();

                    $data = ['GetDetailPendingTask'=>$GetDetailPendingTask, 'GetDetailTask'=>$GetDetailTask];
                    $rslt =  $this->ResultReturn(200, 'success', $data);
                    return response()->json($rslt, 200);
                }else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                }                  
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
     *   path="/api/ApproveTask",
	 *   tags={"Task"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Approve Task",
     *   operationId="ApproveTask",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Approve Task",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="status", type="string", example="0/1/2"),
	 *				@SWG\Property(property="admin_id", type="string", example="ADM000001"),
     *          ),
     *      )
     * )
     *
     */

    public function ApproveTask(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
            'status' => 'required',
            'admin_id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $GetDetailPendingTask = DB::table('pending_task')
            ->where('pending_task.id', '=', $request->id)
            ->first();        

        if($GetDetailPendingTask->menu_code == '13'){
            PendingTask::where('id', '=', $request->id)
            ->update([
                'status' => $request->status,
                'admin_id' => $request->admin_id,
                'updated_at' => Carbon::now(),
            ]);

            WarungTonggo::where('id', '=', $GetDetailPendingTask->laporan_code)
            ->update([
                'status' => $request->status,
                'updated_at' => Carbon::now(),
            ]);

            $GetDetailWarung = DB::table('warung_tonggo')
            ->where('id', '=',$GetDetailPendingTask->laporan_code)
            ->first(); 

            if($request->status == 1){
                $fcm = $this->SendNotifFCM($GetDetailWarung->nama,$GetDetailWarung->no_telp);
            }

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }elseif($GetDetailPendingTask->menu_code == '16'){
            PendingTask::where('id', '=', $request->id)
            ->update([
                'status' => $request->status,
                'admin_id' => $request->admin_id,
                'updated_at' => Carbon::now(),
            ]);

            KentonganLapor::where('id', '=', $GetDetailPendingTask->laporan_code)
            ->update([
                'status' => $request->status,
                'updated_at' => Carbon::now(),
            ]);

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }else{
            $rslt =  $this->ResultReturn(400, 'bad request', 'bad request');
            return response()->json($rslt, 400);
        }
    }
    
}

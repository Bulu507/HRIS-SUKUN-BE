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

use App\YuhPlesir;

use Carbon\Carbon;

class LaporanController extends Controller
{
    

    /**
     * @SWG\Get(
     *   path="/api/GetYuhPlesirUser",
     *   tags={"YuhPlesir"},
     *   summary="Get YuhPlesir User",
     *   operationId="GetYuhPlesirUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", type="string", description="nullable")
     * )
     */
    public function GetYuhPlesirUser(Request $request){
        $getname = $request->user_id;
        if($getname){$user_id='%'.$getname.'%';}
        else{$user_id='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetYuhPlesirUser = DB::table('yuh_plesir')
            ->select('yuh_plesir.id','yuh_plesir.nama_wisata','yuh_plesir.deskripsi','yuh_plesir.gambar1','yuh_plesir.gambar2',
            'yuh_plesir.gambar3','yuh_plesir.tanggal','yuh_plesir.syarat_masuk','yuh_plesir.hari','yuh_plesir.jam',
            'yuh_plesir.harga_tiket','yuh_plesir.status_open','yuh_plesir.alamat','yuh_plesir.status_task',
            'users.name as nama_user','yuh_plesir.created_at')
            ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
            ->where('yuh_plesir.user_id', 'Like', $user_id)
            ->where('yuh_plesir.status_task', '=', 'approved')
            ->orderby('yuh_plesir.nama_wisata','asc')
            ->get();
            if(count($GetYuhPlesirUser)!=0){
                $count = DB::table('yuh_plesir')
                ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
                ->where('yuh_plesir.user_id', 'Like', $user_id)
                ->where('yuh_plesir.status_task', '=', 'approved')
                ->count();
                $data = ['count'=>$count, 'data'=>$GetYuhPlesirUser];
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
     *   path="/api/GetYuhPlesirAdmin",
     *   tags={"YuhPlesir"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get YuhPlesir Admin",
     *   operationId="GetYuhPlesirAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", type="string", description="nullable")
     * )
     */
    public function GetYuhPlesirAdmin(Request $request){
        $getname = $request->user_id;
        if($getname){$user_id='%'.$getname.'%';}
        else{$user_id='%%';}
        try{
            $GetYuhPlesirAdmin = DB::table('yuh_plesir')
            ->select('yuh_plesir.id','yuh_plesir.nama_wisata','yuh_plesir.deskripsi','yuh_plesir.gambar1','yuh_plesir.gambar2',
            'yuh_plesir.gambar3','yuh_plesir.tanggal','yuh_plesir.syarat_masuk','yuh_plesir.hari','yuh_plesir.jam',
            'yuh_plesir.harga_tiket','yuh_plesir.status_open','yuh_plesir.alamat','yuh_plesir.status_task',
            'users.name as nama_user','yuh_plesir.created_at')
            ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
            ->where('yuh_plesir.user_id', 'Like', $user_id)
            ->orderby('yuh_plesir.nama_wisata','asc')
            ->get();
            if(count($GetYuhPlesirAdmin)!=0){
                $count = DB::table('yuh_plesir')
                ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
                ->where('yuh_plesir.user_id', 'Like', $user_id)                
                ->count();
                $data = ['count'=>$count, 'data'=>$GetYuhPlesirAdmin];
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
     *   path="/api/GetYuhPlesirDetail",
     *   tags={"YuhPlesir"},
     *   summary="Get YuhPlesir Detail",
     *   operationId="GetYuhPlesirDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="id",in="query", type="string", description="id")
     * )
     */
    public function GetYuhPlesirDetail(Request $request){
        try{
            $GetYuhPlesirAdmin = DB::table('yuh_plesir')
            ->select('yuh_plesir.id','yuh_plesir.nama_wisata','yuh_plesir.deskripsi','yuh_plesir.gambar1','yuh_plesir.gambar2',
            'yuh_plesir.gambar3','yuh_plesir.tanggal','yuh_plesir.syarat_masuk','yuh_plesir.hari','yuh_plesir.jam',
            'yuh_plesir.harga_tiket','yuh_plesir.status_open','yuh_plesir.alamat','yuh_plesir.status_task',
            'users.name as nama_user','yuh_plesir.created_at')
            ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
            ->where('yuh_plesir.id', '=', $request->id)
            ->orderby('yuh_plesir.nama_wisata','asc')
            ->first();
            if($GetYuhPlesirAdmin){
                $strhari = $GetYuhPlesirAdmin->hari;
                $hariarray = explode(",",$strhari);
                $hariawal = $this->gethariinteger( trim($hariarray[0]));
                $hariakhir = $this->gethariinteger( trim($hariarray[1]));
                $strjam = $GetYuhPlesirAdmin->jam;
                $jamarray = explode(",",$strjam);                
                $jamawal = trim($jamarray[0]);
                $jamakhir = trim($jamarray[1]);
                $jamawalsubstr = substr($jamawal,0,2);
                $jamakhirsubstr = substr($jamakhir,0,2);

                
                $now = Carbon::now();
                $daynow = now()->day;
                $daynow1 = now()->isoFormat("dddd");
                $dayintegernow = $this->gethariinteger($daynow1);
                
                $timenow = now()->toTimeString();
                $timenowsubstr = substr($timenow,0,2);

                $statushari = 'tutup';
                if($hariawal <= $dayintegernow && $hariakhir >= $dayintegernow){
                    $statushari = 'buka';
                }
                $statusjam = 'tutup';
                if((int)$jamawalsubstr<= (int)$timenowsubstr && (int)$jamakhirsubstr >= (int)$timenowsubstr){
                    $statusjam = 'buka';
                }
                $statusadmin ='tutup';
                if($GetYuhPlesirAdmin->status_open = 1){
                    $statusadmin ='buka'; 
                }

                $statusakhir = 'tutup';
                if($statushari == 'buka' && $statusjam == 'buka'&& $statusadmin == 'buka'){
                    $statusakhir = 'buka';
                }
                // $count = DB::table('yuh_plesir')
                // ->join('users','users.admin_user_code','=', 'yuh_plesir.user_id')
                // ->where('yuh_plesir.user_id', 'Like', $user_id)                
                // ->count();
                $data = ['data'=>$GetYuhPlesirAdmin, 'hariarray'=>$hariarray,'hariawal'=>trim($hariarray[0]),'hariakhir'=>trim($hariarray[1]),
                'harisekarang'=>$daynow1,'jamarray'=>$jamarray,'jamawal'=>$jamawal,'jamakhir'=>$jamakhir,'timenow'=>$timenow,
                'statushari'=>$statushari,'statusjam'=>$statusjam,'statusadmin'=>$statusadmin,'statusakhir'=>$statusakhir];
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

    public function gethariinteger($value){
        if(strtolower($value)=='senin'){
            return 1;
        }elseif(strtolower($value)=='selasa'){
            return 2;
        }elseif(strtolower($value)=='rabu'){
            return 3;
        }elseif(strtolower($value)=='kamis'){
            return 4;
        }elseif(strtolower($value)=='jumat'){
            return 5;
        }elseif(strtolower($value)=='sabtu'){
            return 6;
        }else{
            return 7;
        }
    }

    public function ExportWarungTonggo(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}

        // print_r('test');
        try{
            $GetWarungTonggo = DB::table('warung_tonggo')
            ->select('warung_tonggo.id','warung_tonggo.nama','warung_tonggo.kategori','warung_tonggo.lokasi','warung_tonggo.gambar','warung_tonggo.coordinate',
            'warung_tonggo.user_id','users.name as nama_user','warung_tonggo.status','warung_tonggo.no_telp','warung_tonggo.created_at')
            ->join('users','users.admin_user_code','=', 'warung_tonggo.user_id')
            // ->orderby('warung_tonggo.created_at','desc')
            ->get();
            
            $nama_title = 'Cetak Excel WarungTonggo E-Jogotonggo';

            if(count($GetWarungTonggo)!=0){ 
                return view('exportwarungtonggo', compact('GetWarungTonggo', 'nama_title'));
            }
            else{
                $GetWarungTonggo = [];
                return view('exportwarungtonggo', compact('GetWarungTonggo', 'nama_title'));
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/ConvertCoordinate",
     *   tags={"ConvertCoordinate"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Convert Coordinate",
     *   operationId="ConvertCoordinate",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="longitude",in="query", type="string"),
     *      @SWG\Parameter(name="latitude",in="query", type="string")
     * )
     */

    public function ConvertCoordinate(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'longitude' => 'required',         
            'latitude' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

            // var_dump($getLastIdUser->id);
            // var_dump($code);
            $long = $request->longitude;
            $lat = $request->latitude;
            $coordinate = $this->getCordinate($long, $lat);


        $rslt =  $this->ResultReturn(200, 'success', $coordinate);
        return response()->json($rslt, 200);
    
    }


    /**
     * @SWG\Post(
     *   path="/api/AddYuhPlesir",
	 *   tags={"YuhPlesir"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add YuhPlesir",
     *   operationId="AddYuhPlesir",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add YuhPlesir",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="nama_wisata", type="string", example="nama"),
     *              @SWG\Property(property="deskripsi", type="string", example="isi berita"),
     *              @SWG\Property(property="gambar1", type="string", example="path gambar"),
     *              @SWG\Property(property="syarat_masuk", type="string", example="sumber"),
     *              @SWG\Property(property="hari", type="string", example="hari"),
     *              @SWG\Property(property="jam", type="string", example="sumber"),
     *              @SWG\Property(property="harga_tiket", type="string", example="sumber"),
     *              @SWG\Property(property="status_open", type="string", example="sumber"),
     *              @SWG\Property(property="alamat", type="string", example="sumber"),
     *              @SWG\Property(property="user_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */
    public function AddYuhPlesir(Request $request)
    {   
        $validator = Validator::make($request->all(), [  
            'nama_wisata' => 'required',
            'deskripsi' => 'required',      
            'syarat_masuk' => 'required',
            'hari' => 'required',
            'jam' => 'required',
            'harga_tiket' => 'required',
            'status_open' => 'required',
            'alamat' => 'required',
            'gambar1' => 'required',
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

            $getLastIdUser = DB::table('yuh_plesir')
                            ->select('yuh_plesir.id')
                            ->orderBy('id','desc')->first(); 
            if($getLastIdUser){
                $code = 'YP'.str_pad(((int)substr($getLastIdUser->id,-10) + 1), 10, '0', STR_PAD_LEFT);
            }else{
                $code = 'YP0000000001';
            }

            // var_dump($getLastIdUser->id);
            // var_dump($code);

            $gambar1 = $this->ReplaceNull($request->gambar1, 'image');
            $gambar2 = $this->ReplaceNull($request->gambar2, 'image');
            $gambar3 = $this->ReplaceNull($request->gambar3, 'image');
            // $coordinate = $this->ReplaceNull($request->coordinate, 'string');

            YuhPlesir::create([
                'id' => $code,
                'nama_wisata' => $request->nama_wisata,
                'gambar1' => $gambar1,
                'gambar2' => $gambar2,
                'gambar3' => $gambar3,
                'deskripsi' => $request->deskripsi,
                'tanggal' => Carbon::now(),
                'syarat_masuk' => $request->syarat_masuk,
                'hari' => $request->hari,
                'jam' => $request->jam,
                'harga_tiket' => $request->harga_tiket,
                'status_open' => $request->status_open,
                'alamat' => $request->alamat,
                'status_task' => 'pending',
                'user_id' => $request->user_id,
                'created_at' => Carbon::now(),
            ]);

            PendingTask::create([
                'menu_code' => 12,
                'laporan_code' => $code,
                'judul' => $request->nama_wisata,
                'status' => 0,
                'user_id' => $request->user_id,
                'admin_id' => '-',
                'created_at' => Carbon::now(),
            ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditStatusYuhPlesir",
	 *   tags={"YuhPlesir"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit Status YuhPlesir",
     *   operationId="EditStatusYuhPlesir",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit Status YuhPlesir",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="id"),
     *              @SWG\Property(property="status_open", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */
    public function EditStatusYuhPlesir(Request $request)
    {   
        $validator = Validator::make($request->all(), [  
            'id' => 'required',
            'status_open' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

            WarungTonggo::where('id', '=', $request->id)
            ->update([
                'status_open' => $request->status_open,
                'updated_at' => Carbon::now(),
            ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteYuhPlesir",
	 *   tags={"YuhPlesir"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete YuhPlesir",
     *   operationId="DeleteYuhPlesir",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete YuhPlesir",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="id"),
     *          ),
     *      )
     * )
     *
     */
    public function DeleteYuhPlesir(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required',      
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('warung_tonggo')->where('id', $request->id)->delete();

        DB::table('pending_task')->where('laporan_code', $request->id)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    
}

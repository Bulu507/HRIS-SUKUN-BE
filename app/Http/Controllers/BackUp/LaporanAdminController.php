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

use App\DokterFaskes;
use App\GoletGawean;
use App\JagaAwak;
use App\JagaSekolah;


use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Psr7;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;

use Carbon\Carbon;

class LaporanAdminController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetJagaAwak",
     *   tags={"JagaAwak"},
     *   summary="Get JagaAwak",
     *   operationId="GetJagaAwak",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="admin_id",in="query", type="string", description="null untuk get all")
     * )
     */
    public function GetJagaAwak(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetJagaAwak = DB::table('jaga_awak')
            ->select('jaga_awak.id','jaga_awak.judul','jaga_awak.isi_artikel',
            'jaga_awak.sumber','jaga_awak.gambar','jaga_awak.tanggal',
            'jaga_awak.admin_id','admin.nama as nama_admin','jaga_awak.created_at')
            ->join('admin','admin.code','=', 'jaga_awak.admin_id')
            ->where('jaga_awak.admin_id', 'Like', $admin_id)
            ->orderby('jaga_awak.created_at','desc')
            ->get();
            if(count($GetJagaAwak)!=0){
                $count = DB::table('jaga_awak')
                ->join('admin','admin.code','=', 'jaga_awak.admin_id')
                ->where('jaga_awak.admin_id', 'Like', $admin_id)
                ->count();
                $data = ['count'=>$count, 'data'=>$GetJagaAwak];
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

    public function ExportJagaAwak(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}

        // print_r('test');
        try{
            // print_r('test');
            // var_dump(count($GetLahanNotComplete));
            $GetJagaAwak = DB::table('jaga_awak')
            ->select('jaga_awak.id','jaga_awak.judul','jaga_awak.isi_artikel',
            'jaga_awak.sumber','jaga_awak.gambar','jaga_awak.tanggal',
            'jaga_awak.admin_id','admin.nama as nama_admin','jaga_awak.created_at')
            ->join('admin','admin.code','=', 'jaga_awak.admin_id')
            ->where('jaga_awak.admin_id', 'Like', $admin_id)
            ->orderby('jaga_awak.created_at','desc')
            ->get();
            
            $nama_title = 'Cetak Excel JagaAwak E-Jogotonggo';

            if(count($GetJagaAwak)!=0){ 
                return view('exportpesenpemerentah', compact('GetJagaAwak', 'nama_title'));
            }
            else{
                $GetJagaAwak = [];
                return view('exportpesenpemerentah', compact('GetJagaAwak', 'nama_title'));
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddJagaAwak",
	 *   tags={"JagaAwak"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add JagaAwak",
     *   operationId="AddJagaAwak",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add JagaAwak",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="isi_artikel", type="string", example="isi berita"),
     *              @SWG\Property(property="gambar", type="string", example="path gambar"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="sumber", type="string", example="sumber"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function AddJagaAwak(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'admin_id' => 'required',
            'judul' => 'required',
            'isi_artikel' => 'required',
            'gambar' => 'required',
            'tanggal' => 'required',
            'sumber' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        JagaAwak::create([
            'judul' => $request->judul,
            'isi_artikel' => $request->isi_artikel,
            'gambar' => $request->gambar,
            'tanggal' => $request->tanggal,
            'sumber' => $request->sumber,
            'admin_id' => $request->admin_id,
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditJagaAwak",
	 *   tags={"JagaAwak"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit JagaAwak",
     *   operationId="EditJagaAwak",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit JagaAwak",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="isi_artikel", type="string", example="isi artikel"),
     *              @SWG\Property(property="gambar", type="string", example="path gambar"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="sumber", type="string", example="kutipan"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function EditJagaAwak(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
            'admin_id' => 'required',
            'judul' => 'required',
            'isi_artikel' => 'required',
            'gambar' => 'required',
            'tanggal' => 'required',
            'sumber' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        JagaAwak::where('id', '=', $request->id)
        ->update([
            'judul' => $request->judul,
            'isi_artikel' => $request->isi_artikel,
            'gambar' => $request->gambar,
            'tanggal' => $request->tanggal,
            'sumber' => $request->sumber,
            'admin_id' => $request->admin_id,
            'updated_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteJagaAwak",
	 *   tags={"JagaAwak"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete JagaAwak",
     *   operationId="DeleteJagaAwak",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete JagaAwak",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteJagaAwak(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('jaga_awak')->where('id', $request->id)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }


    public function BlastJagaAwak(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $Get = DB::table('jaga_awak')
            ->select('jaga_awak.judul','jaga_awak.isi_artikel')
            ->where('jaga_awak.id', '=', $request->id)
            ->first();

        // $isi = substr($Get->isi_artikel,0,20) .".....";
        if($Get){
            $fcm = $this->SendNotifFCM("Update Info Jaga Awak", $Get->judul);
        }

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Get(
     *   path="/api/GetJagaSekolah",
     *   tags={"JagaSekolah"},
     *   summary="Get JagaSekolah",
     *   operationId="GetJagaSekolah",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="admin_id",in="query", type="string", description="null untuk get all")
     * )
     */
    public function GetJagaSekolah(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetJagaSekolah = DB::table('jaga_sekolah')
            ->select('jaga_sekolah.id','jaga_sekolah.judul','jaga_sekolah.isi_berita',
            'jaga_sekolah.lampiran_file','jaga_sekolah.gambar1','jaga_sekolah.gambar2','jaga_sekolah.gambar3','jaga_sekolah.tanggal',
            'jaga_sekolah.admin_id','admin.nama as nama_admin','jaga_sekolah.created_at')
            ->join('admin','admin.code','=', 'jaga_sekolah.admin_id')
            ->where('jaga_sekolah.admin_id', 'Like', $admin_id)
            ->orderby('jaga_sekolah.created_at','desc')
            ->get();
            if(count($GetJagaSekolah)!=0){
                $count = DB::table('jaga_sekolah')
                ->join('admin','admin.code','=', 'jaga_sekolah.admin_id')
                ->where('jaga_sekolah.admin_id', 'Like', $admin_id)
                ->count();
                $data = ['count'=>$count, 'data'=>$GetJagaSekolah];
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

    public function ExportJagaSekolah(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}

        // print_r('test');
        try{
            // print_r('test');
            // var_dump(count($GetLahanNotComplete));
            $GetJagaSekolah = DB::table('jaga_sekolah')
            ->select('jaga_sekolah.id','jaga_sekolah.judul','jaga_sekolah.isi_berita',
            'jaga_sekolah.lampiran_file','jaga_sekolah.gambar1','jaga_sekolah.gambar2','jaga_sekolah.gambar3','jaga_sekolah.tanggal',
            'jaga_sekolah.admin_id','admin.nama as nama_admin','jaga_sekolah.created_at')
            ->join('admin','admin.code','=', 'jaga_sekolah.admin_id')
            ->where('jaga_sekolah.admin_id', 'Like', $admin_id)
            ->orderby('jaga_sekolah.created_at','desc')
            ->get();
            
            $nama_title = 'Cetak Excel JagaSekolah E-Jogotonggo';

            if(count($GetJagaSekolah)!=0){ 
                return view('exportpesenpemerentah', compact('GetJagaSekolah', 'nama_title'));
            }
            else{
                $GetJagaSekolah = [];
                return view('exportpesenpemerentah', compact('GetJagaSekolah', 'nama_title'));
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddJagaSekolah",
	 *   tags={"JagaSekolah"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add JagaSekolah",
     *   operationId="AddJagaSekolah",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add JagaSekolah",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="isi_berita", type="string", example="isi berita"),
     *              @SWG\Property(property="gambar1", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar2", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar3", type="string", example="path gambar1"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="lampiran_file", type="string", example="lampiran_file"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function AddJagaSekolah(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'admin_id' => 'required',
            'judul' => 'required',
            'isi_berita' => 'required',
            'gambar1' => 'required',
            'gambar2' => 'required',
            'gambar3' => 'required',
            'tanggal' => 'required',
            'lampiran_file' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        JagaSekolah::create([
            'judul' => $request->judul,
            'isi_berita' => $request->isi_berita,
            'gambar1' => $request->gambar1,
            'gambar2' => $request->gambar2,
            'gambar3' => $request->gambar3,
            'tanggal' => $request->tanggal,
            'lampiran_file' => $request->lampiran_file,
            'admin_id' => $request->admin_id,
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditJagaSekolah",
	 *   tags={"JagaSekolah"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit JagaSekolah",
     *   operationId="EditJagaSekolah",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit JagaSekolah",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="isi_berita", type="string", example="isi artikel"),
     *              @SWG\Property(property="gambar1", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar2", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar3", type="string", example="path gambar1"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="lampiran_file", type="string", example="kutipan"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function EditJagaSekolah(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
            'admin_id' => 'required',
            'judul' => 'required',
            'isi_berita' => 'required',
            'gambar1' => 'required',
            'gambar2' => 'required',
            'gambar3' => 'required',
            'tanggal' => 'required',
            'lampiran_file' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        JagaSekolah::where('id', '=', $request->id)
        ->update([
            'judul' => $request->judul,
            'isi_berita' => $request->isi_berita,
            'gambar1' => $request->gambar1,
            'gambar2' => $request->gambar2,
            'gambar3' => $request->gambar3,
            'tanggal' => $request->tanggal,
            'lampiran_file' => $request->lampiran_file,
            'admin_id' => $request->admin_id,
            'updated_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteJagaSekolah",
	 *   tags={"JagaSekolah"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete JagaSekolah",
     *   operationId="DeleteJagaSekolah",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete JagaSekolah",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteJagaSekolah(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('jaga_sekolah')->where('id', $request->id)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    public function BlastJagaSekolah(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $Get = DB::table('jaga_sekolah')
            ->select('jaga_sekolah.judul','jaga_sekolah.isi_berita')
            ->where('jaga_sekolah.id', '=', $request->id)
            ->first();

        // $isi = substr($Get->isi_artikel,0,20) .".....";
        if($Get){
            $fcm = $this->SendNotifFCM("Update Info Jaga Sekolah", $Get->judul);
        }

        $rslt =  $this->ResultReturn(200, 'success', $fcm);
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Get(
     *   path="/api/GetDokterFaskes",
     *   tags={"DokterFaskes"},
     *   summary="Get Dokter Faskes",
     *   operationId="GetDokterFaskes",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="admin_id",in="query", type="string", description="null untuk get all")
     * )
     */
    public function GetDokterFaskes(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetDokterFaskes = DB::table('dokter_faskes')
            ->select('dokter_faskes.id','dokter_faskes.nama','dokter_faskes.jam_operasional',
            'dokter_faskes.no_darurat','dokter_faskes.lokasi','dokter_faskes.daftar_layanan',
            'dokter_faskes.admin_id','admin.nama as nama_admin','dokter_faskes.created_at')
            ->join('admin','admin.code','=', 'dokter_faskes.admin_id')
            ->where('dokter_faskes.admin_id', 'Like', $admin_id)
            ->orderby('dokter_faskes.nama','asc')
            ->get();

            // var_dump( $GetDokterFaskes);
            if(count($GetDokterFaskes)!=0){
                $count = DB::table('dokter_faskes')
                ->join('admin','admin.code','=', 'dokter_faskes.admin_id')
                ->where('dokter_faskes.admin_id', 'Like', $admin_id)
                ->count();
                $data = ['count'=>$count, 'data'=>$GetDokterFaskes];
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
     *   path="/api/AddDokterFaskes",
	 *   tags={"DokterFaskes"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add DokterFaskes",
     *   operationId="AddDokterFaskes",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add DokterFaskes",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="nama", type="string", example="RS Magelang"),
     *              @SWG\Property(property="jam_operasional", type="string", example="09:00 - 10:00"),
     *              @SWG\Property(property="no_darurat", type="string", example="08111010101"),
     *              @SWG\Property(property="lokasi", type="string", example="magelang"),
     *              @SWG\Property(property="daftar_layanan", type="string", example="magelang"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function AddDokterFaskes(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'nama' => 'required',
            'jam_operasional' => 'required',
            'no_darurat' => 'required',
            'lokasi' => 'required',
            'daftar_layanan' => 'required',
            'admin_id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DokterFaskes::create([
            'nama' => $request->nama,
            'jam_operasional' => $request->jam_operasional,
            'no_darurat' => $request->no_darurat,
            'lokasi' => $request->lokasi,
            'daftar_layanan' => $request->daftar_layanan,
            'admin_id' => $request->admin_id,
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditDokterFaskes",
	 *   tags={"DokterFaskes"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit DokterFaskes",
     *   operationId="EditDokterFaskes",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit DokterFaskes",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="nama", type="string", example="RS Magelang"),
     *              @SWG\Property(property="jam_operasional", type="string", example="09:00 - 10:00"),
     *              @SWG\Property(property="no_darurat", type="string", example="08111010101"),
     *              @SWG\Property(property="lokasi", type="string", example="magelang"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function EditDokterFaskes(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
            'nama' => 'required',
            'jam_operasional' => 'required',
            'no_darurat' => 'required',
            'lokasi' => 'required',
            'daftar_layanan' => 'required',
            'admin_id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DokterFaskes::where('id', '=', $request->id)
        ->update([
            'nama' => $request->nama,
            'jam_operasional' => $request->jam_operasional,
            'no_darurat' => $request->no_darurat,
            'lokasi' => $request->lokasi,
            'daftar_layanan' => $request->daftar_layanan,
            'admin_id' => $request->admin_id,
            'updated_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteDokterFaskes",
	 *   tags={"DokterFaskes"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete DokterFaskes",
     *   operationId="DeleteDokterFaskes",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete DokterFaskes",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteDokterFaskes(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('dokter_faskes')->where('id', $request->id)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    public function ExportDokterFaskes(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}

        // print_r('test');
        try{
            // print_r('test');
            // var_dump(count($GetLahanNotComplete));
            $GetDokterFaskes = DB::table('dokter_faskes')
            ->select('dokter_faskes.id','dokter_faskes.nama','dokter_faskes.jam_operasional',
            'dokter_faskes.no_darurat','dokter_faskes.lokasi','dokter_faskes.daftar_layanan',
            'dokter_faskes.admin_id','admin.nama as nama_admin','dokter_faskes.created_at')
            ->join('admin','admin.code','=', 'dokter_faskes.admin_id')
            ->where('dokter_faskes.admin_id', 'Like', $admin_id)
            // ->orderby('dokter_faskes.created_at','desc')
            ->get();
            
            if(count($GetDokterFaskes)!=0){ 

                $nama_title = 'Cetak Excel DokterFaskes E-Jogotonggo';
                // print_r($nama_title);
                // print_r($GetInfodemic);

                return view('exportdokterfaskes', compact('GetDokterFaskes', 'nama_title'));
            }
            else{
                $nama_title = 'Cetak Excel DokterFaskes E-Jogotonggo';
                $GetDokterFaskes = [];
                return view('exportdokterfaskes', compact('GetDokterFaskes', 'nama_title'));
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetGoletGawean",
     *   tags={"GoletGawean"},
     *   summary="Get GoletGawean",
     *   operationId="GetGoletGawean",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="admin_id",in="query", type="string", description="null untuk get all"),
     *      @SWG\Parameter(name="kategori",in="query", type="string", description="null untuk get all")
     * )
     */
    public function GetGoletGawean(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}
        $getname1 = $request->kategori;
        if($getname1){$kategori='%'.$getname1.'%';}
        else{$kategori='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetGoletGawean = DB::table('golet_gawean')
            ->select('golet_gawean.id','golet_gawean.judul','golet_gawean.kategori',
            'golet_gawean.lampiran_file','golet_gawean.gambar1','golet_gawean.gambar2','golet_gawean.gambar3','golet_gawean.tanggal',
            'golet_gawean.nama_perusahaan','golet_gawean.posisi_pekerjaan','golet_gawean.deskripsi','golet_gawean.syarat',
            'golet_gawean.kontak_person','golet_gawean.info_tambahan','golet_gawean.status','golet_gawean.lampiran_file',
            'golet_gawean.admin_id','admin.nama as nama_admin','golet_gawean.created_at')
            ->join('admin','admin.code','=', 'golet_gawean.admin_id')
            ->where('golet_gawean.admin_id', 'Like', $admin_id)
            ->where('golet_gawean.kategori', 'Like', $kategori)
            ->orderby('golet_gawean.created_at','desc')
            ->get();
            if(count($GetGoletGawean)!=0){
                $count = DB::table('golet_gawean')
                ->join('admin','admin.code','=', 'golet_gawean.admin_id')
                ->where('golet_gawean.admin_id', 'Like', $admin_id)
                ->where('golet_gawean.kategori', 'Like', $kategori)
                ->count();
                $data = ['count'=>$count, 'data'=>$GetGoletGawean];
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

    public function ExportGoletGawean(Request $request){
        $getname = $request->admin_id;
        if($getname){$admin_id='%'.$getname.'%';}
        else{$admin_id='%%';}

        // print_r('test');
        try{
            $GetGoletGawean = DB::table('golet_gawean')
            ->select('golet_gawean.id','golet_gawean.judul','golet_gawean.kategori',
            'golet_gawean.lampiran_file','golet_gawean.gambar1','golet_gawean.tanggal',
            'golet_gawean.nama_perusahaan','golet_gawean.posisi_pekerjaan','golet_gawean.deskripsi','golet_gawean.syarat',
            'golet_gawean.kontak_person','golet_gawean.info_tambahan','golet_gawean.status','golet_gawean.lampiran_file',
            'golet_gawean.admin_id','admin.nama as nama_admin','golet_gawean.created_at')
            ->join('admin','admin.code','=', 'golet_gawean.admin_id')
            ->where('golet_gawean.admin_id', 'Like', $admin_id)
            ->orderby('golet_gawean.created_at','desc')
            ->get();
            
            $nama_title = 'Cetak Excel GoletGawean E-Jogotonggo';

            if(count($GetGoletGawean)!=0){ 
                return view('exportpesenpemerentah', compact('GetGoletGawean', 'nama_title'));
            }
            else{
                $GetGoletGawean = [];
                return view('exportpesenpemerentah', compact('GetGoletGawean', 'nama_title'));
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddGoletGawean",
	 *   tags={"GoletGawean"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add GoletGawean",
     *   operationId="AddGoletGawean",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add GoletGawean",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="kategori", type="string", example="isi berita"),
     *              @SWG\Property(property="gambar1", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar2", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar3", type="string", example="path gambar1"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="nama_perusahaan", type="string", example="nama_perusahaan"),
     *              @SWG\Property(property="posisi_pekerjaan", type="string", example="posisi_pekerjaan"),
     *              @SWG\Property(property="deskripsi", type="string", example="deskripsi"),
     *              @SWG\Property(property="syarat", type="string", example="syarat"),
     *              @SWG\Property(property="kontak_person", type="string", example="kontak_person"),
     *              @SWG\Property(property="info_tambahan", type="string", example="info_tambahan"),
     *              @SWG\Property(property="lampiran_file", type="string", example="lampiran_file"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function AddGoletGawean(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'admin_id' => 'required',
            'judul' => 'required',
            'kategori' => 'required',
            'gambar1' => 'required',
            'gambar1' => 'required',
            'gambar2' => 'required',
            'gambar3' => 'required',
            'tanggal' => 'required',
            'nama_perusahaan' => 'required',
            'posisi_pekerjaan' => 'required',
            'deskripsi' => 'required',
            'syarat' => 'required',
            'kontak_person' => 'required',
            'info_tambahan' => 'required',
            'lampiran_file' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        GoletGawean::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar1' => $request->gambar1,
            'gambar1' => $request->gambar1,
            'gambar2' => $request->gambar2,
            'gambar3' => $request->gambar3,
            'tanggal' => $request->tanggal,
            'nama_perusahaan' => $request->nama_perusahaan,
            'posisi_pekerjaan' => $request->posisi_pekerjaan,
            'deskripsi' => $request->deskripsi,
            'syarat' => $request->syarat,
            'kontak_person' => $request->kontak_person,
            'info_tambahan' => $request->info_tambahan,
            'status' => 'aktif',
            'lampiran_file' => $request->lampiran_file,
            'admin_id' => $request->admin_id,
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditGoletGawean",
	 *   tags={"GoletGawean"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit GoletGawean",
     *   operationId="EditGoletGawean",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit GoletGawean",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="judul", type="string", example="nama"),
     *              @SWG\Property(property="kategori", type="string", example="isi artikel"),
     *              @SWG\Property(property="gambar1", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar2", type="string", example="path gambar1"),
     *              @SWG\Property(property="gambar3", type="string", example="path gambar1"),
     *              @SWG\Property(property="tanggal", type="string", example="2021-08-10"),
     *              @SWG\Property(property="nama_perusahaan", type="string", example="nama_perusahaan"),
     *              @SWG\Property(property="posisi_pekerjaan", type="string", example="posisi_pekerjaan"),
     *              @SWG\Property(property="deskripsi", type="string", example="deskripsi"),
     *              @SWG\Property(property="syarat", type="string", example="syarat"),
     *              @SWG\Property(property="kontak_person", type="string", example="kontak_person"),
     *              @SWG\Property(property="info_tambahan", type="string", example="info_tambahan"),
     *              @SWG\Property(property="status", type="string", example="status"),
     *              @SWG\Property(property="lampiran_file", type="string", example="kutipan"),
     *              @SWG\Property(property="admin_id", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */

    public function EditGoletGawean(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
            'admin_id' => 'required',
            'judul' => 'required',
            'kategori' => 'required',
            'gambar1' => 'required',
            'gambar1' => 'required',
            'gambar2' => 'required',
            'gambar3' => 'required',
            'tanggal' => 'required',
            'nama_perusahaan' => 'required',
            'posisi_pekerjaan' => 'required',
            'deskripsi' => 'required',
            'syarat' => 'required',
            'kontak_person' => 'required',
            'info_tambahan' => 'required',
            'status' => 'required',
            'lampiran_file' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        GoletGawean::where('id', '=', $request->id)
        ->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar1' => $request->gambar1,
            'gambar1' => $request->gambar1,
            'gambar2' => $request->gambar2,
            'gambar3' => $request->gambar3,
            'tanggal' => $request->tanggal,
            'nama_perusahaan' => $request->nama_perusahaan,
            'posisi_pekerjaan' => $request->posisi_pekerjaan,
            'deskripsi' => $request->deskripsi,
            'syarat' => $request->syarat,
            'kontak_person' => $request->kontak_person,
            'info_tambahan' => $request->info_tambahan,
            'status' => $request->status,
            'lampiran_file' => $request->lampiran_file,
            'admin_id' => $request->admin_id,
            'updated_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteGoletGawean",
	 *   tags={"GoletGawean"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete GoletGawean",
     *   operationId="DeleteGoletGawean",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete GoletGawean",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteGoletGawean(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('golet_gawean')->where('id', $request->id)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    public function BlastGoletGawean(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'id' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $Get = DB::table('golet_gawean')
            ->select('golet_gawean.judul','golet_gawean.kategori')
            ->where('golet_gawean.id', '=', $request->id)
            ->first();

        // $isi = substr($Get->isi_artikel,0,20) .".....";
        if($Get){
            $fcm = $this->SendNotifFCM("Update Info Gawean", $Get->judul);
        }

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Get(
     *   path="/api/GetApiInfoDodolan",
     *   tags={"InfoDodolan"},
     *   summary="Get Info Dodolan",
     *   operationId="GetApiInfoDodolan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="kategori",in="query", type="string", description="null untuk get all")
     * )
     */
    public function GetApiInfoDodolan(Request $request){
        // $baseurl = $this->BaseUrl();
        // $tokenauth = $this->TokenAuth();
        
            // $url = $baseurl.$valUrl;
            // $url = 'https://mutan.tegalkab.go.id/api/product/for-research';
            
            // $headers = array();
            // // $headers[] = 'service: covid';
            // // $headers[] = 'token:' .$tokenauth;
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");
            // // curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            // //Send the request
            // $response = curl_exec($ch);

            // curl_close($ch);
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mutan.tegalkab.go.id/api/product/for-research',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // echo $response;

        // $arrayResponse = json_decode($response, true);

        // $response = Http::acceptJson()->get('https://mutan.tegalkab.go.id/api/product/for-research');

        $rslt =  $this->ResultReturn(200,  'success', $response);
                return response()->json($rslt, 200);

        // if($arrayResponse){
        //         $rslt =  $this->ResultReturn(200,  'success', $arrayResponse);
        //         return response()->json($rslt, 200);
        //     // if($arrayResponse['kabmagelang']['status']['code'] == 200){
        //     //     $data = $arrayResponse['kabmagelang']['result']['data'];
        //     //     $rslt =  $this->ResultReturn(200,  'success', $data);
        //     //     return response()->json($rslt, 200);
        //     // }else{
        //     //     $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
        //     //     return response()->json($rslt, 404); 
        //     // }
        // }else{
        //     $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
        //     return response()->json($rslt, 404);
        // }
    }
}

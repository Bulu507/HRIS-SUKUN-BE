<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Divisi;
use App\Department;
use App\StatusKaryawan;
use App\Role;
use App\UnitPerusahaan;

use Illuminate\Support\Facades\Http;
// use GuzzleHttp\Psr7;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;

use Carbon\Carbon;

class SystemParameterController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetAllRole",
     *   tags={"SystemParameter"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Role",
     *   operationId="GetAllRole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllRole()
    {

        try {
            $getRole = Role::all();

            if (count($getRole) != 0) {
                $count = Role::count();
                $data = ['count' => $count, 'data' => $getRole];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllUnitPerusahaan",
     *   tags={"SystemParameter"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All UnitPerusahaan",
     *   operationId="GetAllUnitPerusahaan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllUnitPerusahaan()
    {

        try {
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = ['role_code' => $getRole->role_code];
            
            if (in_array('99', $roleCode)) {
                $getUnitPerusahaan = UnitPerusahaan::get();
            }else{                
                $getUnitPerusahaan = UnitPerusahaan::where('is_dell', '=',0)->get();
            }

            if (count($getUnitPerusahaan) != 0) {
                $count = UnitPerusahaan::where('is_dell', '=',0)->count();
                $data = ['count' => $count, 'data' => $getUnitPerusahaan];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllDivisi",
     *   tags={"SystemParameter"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Divisi",
     *   operationId="GetAllDivisi",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllDivisi()
    {

        try {
            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = ['role_code' => $getRole->role_code];
            
            if (in_array('99', $roleCode)) {
                $getDivisi = Divisi::get();
            }else{                
                $getDivisi = Divisi::where('is_dell', '=',0)->get();
            }

            if (count($getDivisi) != 0) {
                $count = Divisi::where('is_dell', '=',0)->count();
                $data = ['count' => $count, 'data' => $getDivisi];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllDepartment",
     *   tags={"SystemParameter"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All Department",
     *   operationId="GetAllDepartment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="divisi_code",in="query",  type="string"),
     * )
     */

    public function GetAllDepartment(Request $request)
    {

        try {
            $getdivisi = $request->divisi_code;
            if($getdivisi){$divisi='%'.$getdivisi.'%';}
            else{$divisi='%%';}

            $userId = Auth::id();
            $getRole = DB::table('users')
                ->where('id', '=', $userId)
                ->select('role_code')
                ->first();
            $roleCode = ['role_code' => $getRole->role_code];
            
            if (in_array('99', $roleCode)) {
                $getDepartment = DB::table('ms_department')
                ->select('ms_department.id','ms_department.divisi_code', 'ms_department.department_code',
                'ms_department.nama as dept_nama','ms_department.is_dell','ms_divisi.nama as divisi_nama')
                ->join('ms_divisi','ms_divisi.divisi_code','=', 'ms_department.divisi_code')
                ->where('ms_divisi.divisi_code', 'Like', $divisi)
                ->get();
            }else{                
                $getDepartment = DB::table('ms_department')
                ->select('ms_department.id','ms_department.divisi_code', 'ms_department.department_code',
                'ms_department.nama as dept_nama','ms_department.is_dell','ms_divisi.nama as divisi_nama')
                ->join('ms_divisi','ms_divisi.divisi_code','=', 'ms_department.divisi_code')
                ->where('ms_divisi.divisi_code', 'Like', $divisi)
                ->where('ms_department.is_dell', '=',0)->get();
            }

            if (count($getDepartment) != 0) {
                $count = Department::where('is_dell', '=',0)->count();
                $data = ['count' => $count, 'data' => $getDepartment];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllStatusKaryawan",
     *   tags={"SystemParameter"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get All StatusKaryawan",
     *   operationId="GetAllStatusKaryawan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    public function GetAllStatusKaryawan()
    {

        try {
            $getStatusKaryawan = StatusKaryawan::where('is_dell', '=',0)->get();

            if (count($getStatusKaryawan) != 0) {
                $count = StatusKaryawan::where('is_dell', '=',0)->count();
                $data = ['count' => $count, 'data' => $getStatusKaryawan];
                $rslt = $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Post(
     *   path="/api/AddUnitPerusahaan",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add UnitPerusahaan",
     *   operationId="AddUnitPerusahaan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add UnitPerusahaan",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */

    public function AddUnitPerusahaan(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {

            $getLastId= UnitPerusahaan::orderBy('unit_perusahaan_code','desc')->first(); 
            if($getLastId){
                $code = 'UP_'.str_pad(((int)substr($getLastId->divisi_code,-3) + 1), 3, '0', STR_PAD_LEFT);
            }else{
                $code = 'UP_001';
            }

            UnitPerusahaan::create([
                'unit_perusahaan_code' => $code,
                'nama' => $request->nama,
                'created_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/AddDivisi",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Divisi",
     *   operationId="AddDivisi",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Divisi",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */

    public function AddDivisi(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {

            $getLastId= Divisi::orderBy('divisi_code','desc')->first(); 
            if($getLastId){
                $code = 'Div_'.str_pad(((int)substr($getLastId->divisi_code,-3) + 1), 3, '0', STR_PAD_LEFT);
            }else{
                $code = 'Div_001';
            }

            Divisi::create([
                'divisi_code' => $code,
                'nama' => $request->nama,
                'created_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/AddDepartment",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Department",
     *   operationId="AddDepartment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Department",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="divisi_code", type="string", example="123"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */

    public function AddDepartment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'divisi_code' => 'required',          
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {
            $getLastId= Department::orderBy('department_code','desc')->first(); 
            if($getLastId){
                $code = 'Dept_'.str_pad(((int)substr($getLastId->department_code,-3) + 1), 3, '0', STR_PAD_LEFT);
            }else{
                $code = 'Dept_001';
            }
            Department::create([
                'divisi_code' => $request->divisi_code,
                'department_code' => $code,
                'nama' => $request->nama,
                'created_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/EditUnitPerusahaan",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit UnitPerusahaan",
     *   operationId="EditUnitPerusahaan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit UnitPerusahaan",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="unit_perusahaan_code", type="string", example="Div_001"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */ 

    public function EditUnitPerusahaan(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'unit_perusahaan_code' => 'required',
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {

            Db::table('ms_unit_perusahaan')->where('unit_perusahaan_code', '=',$request->unit_perusahaan_code)
            ->update([
                'nama' => $request->nama,
                'updated_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/EditDivisi",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit Divisi",
     *   operationId="EditDivisi",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit Divisi",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="divisi_code", type="string", example="Div_001"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */ 

    public function EditDivisi(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'divisi_code' => 'required',
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {

            Db::table('ms_divisi')->where('divisi_code', '=',$request->divisi_code)
            ->update([
                'nama' => $request->nama,
                'updated_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditDepartment",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit Department",
     *   operationId="EditDepartment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit Department",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="divisi_code", type="string", example="Div_001"),
     *              @SWG\Property(property="department_code", type="string", example="Dept_001"),
     *              @SWG\Property(property="nama", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */ 

    public function EditDepartment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'divisi_code' => 'required',         
            'department_code' => 'required',
            'nama' => 'required',
        ]);
        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {

            Db::table('ms_department')->where('department_code', '=',$request->department_code)
            ->update([
                'divisi_code' => $request->divisi_code,
                'nama' => $request->nama,
                'updated_at' => Carbon::now(),
            ]);
    
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/SoftDeleteUnitPerusahaan",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Soft Delete UnitPerusahaan",
     *   operationId="SoftDeleteUnitPerusahaan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Soft Delete UnitPerusahaan",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="unit_perusahaan_code", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function SoftDeleteUnitPerusahaan(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'unit_perusahaan_code' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        try {            

            Db::table('ms_unit_perusahaan')->where('unit_perusahaan_code', '=',$request->unit_perusahaan_code)
            ->update([
                'is_dell' => 1,
                'updated_at' => Carbon::now(),
            ]);

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }
    /**
     * @SWG\Post(
     *   path="/api/SoftDeleteDivisi",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Soft Delete Divisi",
     *   operationId="SoftDeleteDivisi",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Soft Delete Divisi",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="divisi_code", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function SoftDeleteDivisi(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'divisi_code' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        try {            

            Db::table('ms_divisi')->where('divisi_code', '=',$request->divisi_code)
            ->update([
                'is_dell' => 1,
                'updated_at' => Carbon::now(),
            ]);

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Post(
     *   path="/api/SoftDeleteDepartment",
	 *   tags={"SystemParameter"},
     *    security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Soft Delete Department",
     *   operationId="SoftDeleteDepartment",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Soft Delete Department",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="department_code", type="string", example="1"),
     *          ),
     *      )
     * )
     *
     */

    public function SoftDeleteDepartment(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'department_code' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        try {            

            Db::table('ms_department')->where('department_code', '=',$request->department_code)
            ->update([
                'is_dell' => 1,
                'updated_at' => Carbon::now(),
            ]);

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);

        } catch (\Exception $ex) {
            // return response()->json($ex);
            $rslt =  $this->ResultReturn(500, 'success', $ex);
            return response()->json($rslt, 500);
        }
    
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllMenuAccess",
     *   tags={"SystemParameter"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="GetAllMenuAccess",
     *   operationId="GetAllMenuAccess",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetAllMenuAccess(Request $request){
        try{
            $GetAllMenuAccess = DB::table('menu_access')->orderBy('name', 'ASC')->get();
            if(count($GetAllMenuAccess)!=0){
                $rslt =  $this->ResultReturn(200, 'success', $GetAllMenuAccess);
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
}

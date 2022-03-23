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

use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetAdmin",
     *   tags={"Admin"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Admin",
     *   operationId="GetAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetAdmin(Request $request){
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetUser = DB::table('admin')
            ->select('admin.code','admin.nama','admin.no_telp','admin.alamat','admin.pekerjaan',
            'admin.status_admin','users.role_code as role','users.email','role.name as role_name')
            ->join('users','users.admin_user_code','=', 'admin.code')
            ->join('role','role.id','=', 'users.role_code')
            ->where('admin.code','<>','ADM00000001')
            ->get();
            if(count($GetUser)!=0){ 
                $count = DB::table('admin')
                ->join('users','users.admin_user_code','=', 'admin.code')
                ->join('role','role.id','=', 'users.role_code')
                ->where('admin.code','<>','ADM00000001')
                ->count();
                $data = ['count'=>$count, 'data'=>$GetUser];
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
     *   path="/api/EditAdmin",
	 *   tags={"Admin"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Edit Admin",
     *   operationId="EditAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Edit Admin",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="name", type="string", example="nama"),
     *              @SWG\Property(property="role", type="string", example="1/2/3"),
	 *				@SWG\Property(property="code", type="string", example="nama123"),
     *              @SWG\Property(property="no_telp", type="string", example="nullable"),
     *              @SWG\Property(property="alamat", type="string", example="nullable"),
     *              @SWG\Property(property="pekerjaan", type="string", example="nullable"),
     *              @SWG\Property(property="status_admin", type="string", example="nullable")
     *          ),
     *      )
     * )
     *
     */

    public function EditAdmin(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'name' => 'required',
            'code' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        // $getLastIdUser = Admin::orderBy('code','desc')->first(); 
        //     if($getLastIdUser){
        //         $code = 'ADM'.str_pad(((int)substr($getLastIdUser->code,-8) + 1), 8, '0', STR_PAD_LEFT);
        //     }else{
        //         $code = 'ADM00000001';
        //     }

        User::where('admin_user_code', '=', $request->code)
        ->update([
            'name' => $request->name,
            'role_code' => $request->role,
            'type' => 1,
        ]);

        Admin::where('code', '=', $request->code)
        ->update([
            'nama' => $request->name,
            'no_telp' => $this->ReplaceNull($request->no_telp, 'string'),
            'alamat' => $this->ReplaceNull($request->alamat, 'string'),
            'pekerjaan' => $this->ReplaceNull($request->pekerjaan, 'string'),
            'status_admin' => $this->ReplaceNull($request->status_admin, 'string'),
            'updated_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteAdmin",
	 *   tags={"Admin"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Admin",
     *   operationId="DeleteAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Admin",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
	 *				@SWG\Property(property="code", type="string", example="nama123"),
     *          ),
     *      )
     * )
     *
     */

    public function DeleteAdmin(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'code' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        DB::table('users')->where('admin_user_code', $request->code)->delete(); 

        DB::table('admin')->where('code', $request->code)->delete(); 

        DB::table('admin_menu_access')->where('admin_code', $request->code)->delete();

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAdminMenuAccess",
     *   tags={"Admin"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="GetAdminMenuAccess",
     *   operationId="GetAdminMenuAccess",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetAdminMenuAccess(Request $request){
        // $position_code = $request->position_code;
        try{
            $GetAdminMenuAccess = DB::table('admin')
            ->select('admin.code', 'admin.nama','users.role_code as role','users.email','role.name as role_name')
            ->join('users','users.admin_user_code','=', 'admin.code')
            ->join('role','role.id','=', 'users.role_code')
            ->where('admin.code','<>','ADM00000001')
            ->get();

            $datavalempmenu = [];
            $listval=array();

            foreach ($GetAdminMenuAccess as $val){
                        // var_d112ump($val->id);
                    $AdminMenu = DB::table('admin_menu_access')->where('admin_code','=',$val->code)->first();
                    $menuaccess = $AdminMenu->list_menu;
                    $arraymn = json_decode($menuaccess);

                    // var_dump($arraymn);
                    $menu = '';
                    $i = 1;
                    $datavalemenu = [];
                    $listvalmenu=array();
                    foreach ($arraymn as $valemp){
                        
                        $getmenu = DB::table('menu_access')
                        ->select('menu_access.name as title')
                        ->where('menu_access.id','=',$valemp)
                        ->first();
                        // var_dump($getmenu);
                        if($i == 1){
                           $menu = $getmenu->title;
                        }else{
                           $menu = $menu. ', '.$getmenu->title; 
                        }                        
                        $i+=1;

                        $datavalemenu = [ 'MenuCode'=>  $valemp, 'MenuName'=>$getmenu->title];
                        array_push($listvalmenu,$datavalemenu);
                    }

                    // var_dump($listvalmenu);
                    
                    // var_dump($menu);
                    $datavalempmenu = [ 'code'=>  $val->code, 'Nama'=>$val->nama,'role'=>$val->role, 'role_name'=>$val->role_name, 'Menu'=> $menu, 'MenuCode'=> $arraymn, 'MenuTable'=> $listvalmenu];
                    array_push($listval,$datavalempmenu);
                }

            
            if(count($GetAdminMenuAccess)!=0){
                $count = DB::table('admin')
                ->count();
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
     * @SWG\Post(
     *   path="/api/EditMenuAccessadm",
	 *   tags={"Admin"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="EditMenuAccessadm",
     *   operationId="EditMenuAccessadm",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="EditMenuAccessadm",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="code", type="string", example="11_011"),
	 *				@SWG\Property(property="list_menu", type="string", example="[1]"),
     *          ),
     *      )
     * )
     *
     */
    public function EditMenuAccessadm(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required',
                'list_menu' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            AdminMenu::where('admin_code', '=', $request->code)
                ->update([
                        'list_menu' => $request->list_menu,
                ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetAllMenuAccess",
     *   tags={"Admin"},
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

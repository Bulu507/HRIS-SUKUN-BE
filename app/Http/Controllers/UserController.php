<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserMenu;
use App\MenuParent;
use App\MenuAccess;

use Carbon\Carbon;

class UserController extends Controller
{

    /**
     * @SWG\Post(
     *   path="/api/RegistUser",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Regist User",
     *   operationId="RegistUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Regist User",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="name", type="string", example="nama"),
     *              @SWG\Property(property="email", type="string", example="aaa@mail.com"),
     *              @SWG\Property(property="password", type="string", example="123456"),
	 *				@SWG\Property(property="no_telp", type="string", example="123456"),
     *              @SWG\Property(property="nik", type="string", example="123456"),
     *              @SWG\Property(property="role_code", type="string", example="1"),
     *              @SWG\Property(property="path_foto", type="string", example="nullable"),
     *          ),
     *      )
     * )
     *
     */

    public function RegistUser(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            // 'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_telp' => 'required',
            'nik' => 'required|unique:users',
            'role_code' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $getLastIdUser = User::orderBy('user_id','desc')->first(); 
            if($getLastIdUser){
                $code = 'USR'.str_pad(((int)substr($getLastIdUser->user_id,-8) + 1), 8, '0', STR_PAD_LEFT);
            }else{
                $code = 'USR00000001';
            }

        $queryemp = DB::table('employees')
                    ->where('employees.no_induk_karyawan', $request->nik)
                    ->first();

        User::create([
            'user_id' => $code,
            'name' => $queryemp->nama_lengkap,
            'nik' => $request->nik,
            'role_code' => $request->role_code,
            'email' => $request->email,
            'no_telp' =>$request->no_telp,
            'password' => bcrypt($request->password),
            'path_foto' => $this->ReplaceNull($request->path_foto, 'string'),
            'type' => 1,
            'status' => 0,
            'created_at' => Carbon::now(),
        ]);

        UserMenu::create([
            'user_code' => $code,
            'list_menu' => '[2,3,4]',
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/EditUser",
     *   tags={"Users"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Edit User",
     *   operationId="EditUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Edit User",
     *          required=true, 
     *          type="string",
     *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="nama"),
     *				@SWG\Property(property="email", type="string", example="nama@mail.com"),
     *              @SWG\Property(property="no_telp", type="string", example="1QwOp@"),
     *				@SWG\Property(property="role_code", type="string", example="1QwOp@"),
     *              @SWG\Property(property="status_admin", type="string", example="1QwOp@")
     *          ),
     *      )
     * )
     *
     */
    public function EditUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'no_telp' => 'required',
            // 'nik' => 'required',
            'role_code' => 'required',
            'status_admin' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {       
            
            $queryeusr = DB::table('users')
                    ->where('users.user_id', $request->user_id)
                    ->first();

            if ($queryeusr) {
                User::where('user_id', '=', $request->user_id)
                ->update([
                    'no_telp' => $request->no_telp,
                    'status' => $request->status_admin,
                    'role_code' => $request->role_code,
                    'email' => $request->email,
                    // 'password' => bcrypt($request->new_password)
                ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(400, 'Data tidak ada', 'Data tidak ada');
                return response()->json($rslt, 400);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteUser",
     *   tags={"Users"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="DeleteUser",
     *   operationId="DeleteUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="DeleteUser",
     *          required=true, 
     *          type="string",
     *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="nama"),
     *          ),
     *      )
     * )
     *
     */
    public function DeleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }
        try {       
            
            $queryeusr = DB::table('users')
                    ->where('users.user_id', $request->user_id)
                    ->first();
            $userId = Auth::id();
            $getRole = DB::table('users')
                    ->where('id', '=', $userId)
                    ->select('role_code')
                    ->first();

            if ($queryeusr) {
                if($getRole->role_code == 99){
                    if($request->user_id <> 'ADM00000001'){
                        DB::table('users')->where('user_id', '=', $request->user_id)->delete();
                    }                   
                }else{
                    User::where('user_id', '=', $request->user_id)
                    ->update([
                        'status' => 1,
                        'is_dell' => 1,
                        ]);
                }

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(400, 'Data tidak ada', 'Data tidak ada');
                return response()->json($rslt, 400);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/LoginUser",
	 *   tags={"Users"},
     *   summary="Login User",
     *   operationId="LoginUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Login User",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="email", type="string", example="nama@mail.com"),
	 *				@SWG\Property(property="password", type="string", example="nama123")
     *          ),
     *      )
     * )
     *
     */

    public function LoginUser (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required|min:6',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            // var_dump('test');
            // $credentials = request(['email', 'password']);
            $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_telp';
            // var_dump($fieldType);
            $credentials = array($fieldType => $request->email, 'password' => $request->password);

            if (! $token = auth()->attempt($credentials)) {                
                $rslt =  $this->ResultReturn(401, 'Incorrect Email & password', 'Unauthorized');
                return response()->json($rslt, 401);
            }else{
                if($fieldType == 'email'){
                    $getUser = User::where('email', '=', $request->email)->where('status', '=',0)->first();
                }else{
                    $getUser = User::where('no_telp', '=', $request->email)->where('status', '=',0)->first();
                }
               

                if($getUser){
                    $Role = DB::table('role')->where('id','=',$getUser->role_code)->first();
                    // $UserProfile = UserProfile::where('code','=',$getUser->user_id)->first();
                    $menu_access = DB::table('user_menu_access')->where('user_code','=',$getUser->user_id)->first(); 
                    
                    $dataval = [];
                    $listval=array();
                    $listvalmenu=array();

                    if($menu_access){
                        $menuaccess = $menu_access->list_menu;
                        $arraymn = json_decode($menuaccess);
                        // var_dump(count($arraymn));
                        
                        $getmenuparent = DB::table('menu_access_parent')->get();
                        foreach ($getmenuparent as $val){
                            // var_d112ump($val->id);
                            $getmenu = DB::table('menu_access')
                            ->select('menu_access.name as title', 'menu_access.path as to')
                            ->join('menu_access_parent', 'menu_access_parent.id', '=', 'menu_access.parent_code')
                            ->where('menu_access_parent.id','=',$val->id)
                            ->wherein('menu_access.id',$arraymn)
                            ->orderby('menu_access_parent.id','DESC')
                            ->get();
                            if(count($getmenu) > 0){
                                $dataval = [ 'title'=>  $val->name, 'items'=>$getmenu, 'icon'=> $val->icon];
                                array_push($listval,$dataval);
                            }
                        }

                        $getmenulist = DB::table('menu_access')
                            ->select('menu_access.path as to')
                            ->wherein('menu_access.id',$arraymn)
                            ->orderby('menu_access.id','ASC')
                            ->get();

                        foreach( $getmenulist as $list){
                            array_push($listvalmenu,str_replace("/","",$list->to));
                        }

                        array_push($listvalmenu,'GantiPassword');
                    }    

                    $objUser = ['user_id'=>$getUser->user_id,'role'=>$Role->name,'role_code'=>$getUser->role_code,'name'=>$getUser->name,
                    'email'=>$getUser->email,'no_telp'=>$getUser->no_telp,'path_foto'=>$getUser->path_foto,
                    'status'=>$getUser->status,'nik'=>$getUser->nik,
                    'list_menu' => $listval, 'list_val_menu'=> $listvalmenu];


                    $usernew = ['User'=>$objUser, 'access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60 ];
                    $rslt =  $this->ResultReturn(200, 'success', $usernew);
                    return response()->json($rslt, 200);
                }else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                }
                                
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetUserMenuAccess",
     *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="GetUserMenuAccess",
     *   operationId="GetUserMenuAccess",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetUserMenuAccess(Request $request){
        // $position_code = $request->position_code;
        try{
            if($request->role_code == '99'){
                $isdell = [0, 1];
            }else{
                $isdell = [0];
            }

            $GetUserMenuAccess = DB::table('users')
            ->select('users.user_id', 'users.name','users.role_code as role','users.email','role.name as role_name')
            ->join('role','role.id','=', 'users.role_code')
            ->where('users.user_id','<>','ADM00000001')
            ->whereIn('users.is_dell', $isdell)
            ->get();

            $datavalempmenu = [];
            $listval=array();

            foreach ($GetUserMenuAccess as $val){
                        // var_d112ump($val->id);
                    $AdminMenu = DB::table('user_menu_access')->where('user_code','=',$val->user_id)->first();
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
                    $datavalempmenu = [ 'user_id'=>  $val->user_id, 'Nama'=>$val->name,'role'=>$val->role, 'role_name'=>$val->role_name, 'Menu'=> $menu, 'MenuCode'=> $arraymn, 'MenuTable'=> $listvalmenu];
                    array_push($listval,$datavalempmenu);
                }

            
            if(count($GetUserMenuAccess)!=0){
                $count = DB::table('users')
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
     *   path="/api/EditMenuAccess",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="EditMenuAccess",
     *   operationId="EditMenuAccess",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="EditMenuAccess",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_code", type="string", example="11_011"),
	 *				@SWG\Property(property="list_menu", type="string", example="[1]"),
     *          ),
     *      )
     * )
     *
     */
    public function EditMenuAccess(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_code' => 'required',
                'list_menu' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            UserMenu::where('user_code', '=', $request->user_code)
                ->update([
                        'list_menu' => $request->list_menu,
                ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    public function LoginAdmin (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            // var_dump('test');
            $credentials = request(['email', 'password']);
            if (! $token = auth()->attempt($credentials)) {                
                $rslt =  $this->ResultReturn(400, 'Incorrect Email & password', 'Unauthorized');
                return response()->json($rslt, 400);
            }else{
                $getUser = User::where('email', '=', $request->email)->where('type', '=', 1)->first();  
                // var_dump($getUser);
                
                if($getUser){
                    $Admin = Admin::where('code','=',$getUser->admin_user_code)
                        ->where('status_admin','=','aktif')->first();
                    if($Admin){
                        $Role = DB::table('role')->where('id','=',$getUser->role_code)->first();                    
                        $menu_access = DB::table('admin_menu_access')->where('admin_code','=',$getUser->admin_user_code)->first(); 
                        
                        $dataval = [];
                        $listval=array();
                        $listvalmenu=array();
                        if($menu_access){
                            $menuaccess = $menu_access->list_menu;
                            $arraymn = json_decode($menuaccess);
                            // var_dump(count($arraymn));
                            
                            $getmenuparent = DB::table('menu_access_parent')->get();
                            foreach ($getmenuparent as $val){
                                // var_d112ump($val->id);
                                $getmenu = DB::table('menu_access')
                                ->select('menu_access.name as title', 'menu_access.path as to')
                                ->join('menu_access_parent', 'menu_access_parent.id', '=', 'menu_access.parent_code')
                                ->where('menu_access_parent.id','=',$val->id)
                                ->wherein('menu_access.id',$arraymn)
                                ->orderby('menu_access_parent.id','DESC')
                                ->get();
                                if(count($getmenu) > 0){
                                    $dataval = [ 'title'=>  $val->name, 'items'=>$getmenu, 'icon'=> $val->icon];
                                    array_push($listval,$dataval);
                                }
                            }

                            $getmenulist = DB::table('menu_access')
                                ->select('menu_access.path as to')
                                ->wherein('menu_access.id',$arraymn)
                                ->orderby('menu_access.id','ASC')
                                ->get();

                            foreach( $getmenulist as $list){
                                array_push($listvalmenu,str_replace("/","",$list->to));
                            }

                            array_push($listvalmenu,'GantiPassword');
                        }  

                        $objUser = ['code'=>$Admin->code,'role_code'=>$getUser->role_code,'role'=>$Role->name,'name'=>$Admin->nama,
                        'email'=>$getUser->email,'no_telp'=>$Admin->no_telp,'status_admin'=>$Admin->status_admin,'alamat'=>$Admin->alamat,
                        'path_foto'=>$Admin->path_foto, 'list_menu' => $listval, 'list_val_menu'=> $listvalmenu];

                        $usernew = ['User'=>$objUser, 'access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60 ];
                        $rslt =  $this->ResultReturn(200, 'success', $usernew);
                        return response()->json($rslt, 200);
                    }else{
                        $rslt =  $this->ResultReturn(404, 'error', 'admin non aktif');
                        return response()->json($rslt, 404);
                    }
                }else{
                    $rslt =  $this->ResultReturn(404, 'error', 'doesnt match data');
                    return response()->json($rslt, 404);
                }
                                
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/Logout",
     *   tags={"Users"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Logout User",
     *   operationId="UsersLogout",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     *
     */

    public function Logout()
    {
        auth()->logout();
        $rslt =  $this->ResultReturn(200, 'Successfully logged out', 'Successfully logged out');
        return response()->json($rslt, 200);
    }

    /**
     * @SWG\Post(
     *   path="/api/ForgotPassword",
     *   tags={"Users"},
     *   summary="Forgot Password User",
     *   operationId="ForgotPassword",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Forgot Password User",
     *          required=true, 
     *          type="string",
     *   		@SWG\Schema(
     *              @SWG\Property(property="email", type="string", example="nama@mail.com")
     *          ),
     *      )
     * )
     *
     */
    public function ForgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails()) {
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            $getUser = User::where('email', '=', $request->email)->first();

            if ($getUser) {
                $newPass = $this->RandomPassword();
                User::where('email', '=', $request->email)
                    ->update(['password' => bcrypt($newPass)]);
                $usernew = ['User' => $getUser, 'new_password' => $newPass];
                $rslt =  $this->ResultReturn(200, 'success', $usernew);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(401, 'Incorrect Email', 'Unauthorized');
                return response()->json($rslt, 401);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/ResetPasswordUser",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="ResetPasswordUser",
     *   operationId="ResetPasswordUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="ResetPasswordUser",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="11_0222"),
	 *				@SWG\Property(property="email", type="string", example="nama@mail.com"),
     *          ),
     *      )
     * )
     *
     */
    public function ResetPasswordUser (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'email' => 'required|string|email|max:255',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }
            $password = '123456';
            User::where('user_id', '=', $request->user_id)->where('email', '=', $request->email)
                    ->update([
                        'password' => bcrypt($password),
                        ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/EditProfile",
     *   tags={"Users"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Edit Profile User",
     *   operationId="EditProfile",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
     *			description="Edit Profile User",
     *          required=true, 
     *          type="string",
     *   		@SWG\Schema(
     *              @SWG\Property(property="name", type="string", example="nama"),
     *				@SWG\Property(property="email", type="string", example="nama@mail.com"),
     *              @SWG\Property(property="password", type="string", example="1QwOp@"),
     *				@SWG\Property(property="new_password", type="string", example="1QwOp@"),
     *              @SWG\Property(property="confirm_password", type="string", example="1QwOp@")
     *          ),
     *      )
     * )
     *
     */
    public function EditProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6',
                'confirm_password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            $credentials = request(['email', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                $rslt =  $this->ResultReturn(401, 'Incorrect Email & password', 'Unauthorized');
                return response()->json($rslt, 401);
            } elseif ($request->new_password != $request->confirm_password) {
                $rslt =  $this->ResultReturn(401, 'Incorrect New Password & Confirm password', 'Unauthorized');
                return response()->json($rslt, 401);
            } else {
                User::where('email', '=', $request->email)
                    ->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->new_password)
                    ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetUser",
     *   tags={"Users"},
     *   security={
     *     {"apiAuth": {}},
     *   },
     *   summary="Get Users",
     *   operationId="GetUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="role",in="query",  type="string"),
     * )
     */
    public function GetUser(Request $request)
    {
        
        try {
            $userId = Auth::id();
            $getRole = DB::table('users')
                    ->where('id', '=', $userId)
                    ->select('role_code')
                    ->first();

            if($getRole->role_code == 99){
                $isdell = [0, 1];
                $roleid = 0;
            }else{
                $isdell = [0];
                $roleid = 99;
            }
            
            $GetUser = DB::table('users')
                ->select('users.user_id','users.nik', 'users.name as nama','users.role_code as role_code','users.email','users.no_telp','role.name as role_name','users.status as status_admin')
                ->join('role','role.id','=', 'users.role_code')
                ->whereIn('users.is_dell', $isdell)
                ->where('users.role_code', '<>', $roleid)
                ->get();
            if (count($GetUser) != 0) {
                $count = DB::table('users')
                    ->whereIn('users.is_dell', $isdell)
                    ->where('users.role_code', '<>', $roleid)
                    ->count();
                $data = ['count' => $count, 'data' => $GetUser];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);
            } else {
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    function RandomPassword()
    {
        $maxLengthPass = 8;
        $pass = array();

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char = '!@#$*';
        $number = '1234567890';

        $getNumberRandChar = rand(1, $maxLengthPass - 1);
        $getNumberRandNumber = rand(1, $maxLengthPass - 1);
        if ($getNumberRandChar == $getNumberRandNumber) {
            $getNumberRandChar = 2;
            $getNumberRandNumber = 5;
        }

        $alphaLength = strlen($alphabet) - 1;
        $charLength = strlen($char) - 1;
        $numberLength = strlen($number) - 1;

        for ($i = 0; $i < $maxLengthPass; $i++) {
            $varPass = '';
            if ($getNumberRandChar == $i) {
                $n = rand(0, $charLength);
                $varPass = $char[$n];
            } elseif ($getNumberRandNumber == $i) {
                $n = rand(0, $numberLength);
                $varPass = $number[$n];
            } else {
                $n = rand(0, $alphaLength);
                $varPass = $alphabet[$n];
            }
            $pass[] = $varPass;
        }
        return implode($pass);
    }

    protected function RespondWithToken($token, $data)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }
}

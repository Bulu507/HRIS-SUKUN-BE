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
use App\UserTokenFCM;

use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetApi",
     *   tags={"API"},
     *   summary="GetApi",
     *   operationId="GetApi",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetApi(Request $request)
    {
        try{

            $URLAPI = "servicesapitegal.distribusipelitanusantara.com/api/";
            $BASEURL = "https://servicesapitegal.distribusipelitanusantara.com/";
            
            $data = ['URLAPI'=>$URLAPI, 'BASEURL'=>$BASEURL];
            $rslt =  $this->ResultReturn(200, 'success', $data);
            return response()->json($rslt, 200); 
        }catch(\Exception $ex){
            return response()->json($ex);
        }        
    }
    
    /**
     * @SWG\Post(
     *   path="/api/RegistUser",
	 *   tags={"Users"},
     *   summary="Regist User",
     *   operationId="UsersRegist",
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
     *              @SWG\Property(property="email", type="string", example="nama@mail.com"),
	 *				@SWG\Property(property="password", type="string", example="nama123"),
     *              @SWG\Property(property="no_telp", type="string", example="mandatory boss dan unik"),
     *              @SWG\Property(property="alamat", type="string", example="nullable"),
     *              @SWG\Property(property="dusun", type="string", example="nullable"),
     *              @SWG\Property(property="desa", type="string", example="nullable"),
     *              @SWG\Property(property="kecamatan", type="string", example="nullable"),
     *              @SWG\Property(property="pekerjaan", type="string", example="nullable"),
     *              @SWG\Property(property="foto", type="string", example="nullable")
     *          ),
     *      )
     * )
     *
     */

    public function RegistUser(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_telp' => 'required|unique:users',
            'password' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $getLastIdUser = UserProfile::orderBy('code','desc')->first(); 
            if($getLastIdUser){
                $code = 'USR'.str_pad(((int)substr($getLastIdUser->code,-8) + 1), 8, '0', STR_PAD_LEFT);
            }else{
                $code = 'USR00000001';
            }

        User::create([
            'admin_user_code' => $code,
            'name' => $request->name,
            'role_code' => 4,
            'email' => $request->email,
            'no_telp' =>$request->no_telp,
            'password' => bcrypt($request->password),
            'type' => 0,
        ]);

        UserProfile::create([
            'code' => $code,
            'nama' => $request->name,
            'no_telp' => $this->ReplaceNull($request->no_telp, 'string'),
            'alamat' => $this->ReplaceNull($request->alamat, 'string'),
            'dusun' => $this->ReplaceNull($request->dusun, 'string'),
            'desa' => $this->ReplaceNull($request->desa, 'string'),
            'kecamatan' => $this->ReplaceNull($request->kecamatan, 'string'),
            'pekerjaan' => $this->ReplaceNull($request->pekerjaan, 'string'),
            'foto' => $this->ReplaceNull($request->foto, 'string'),
            'created_at' => Carbon::now(),
        ]);

        UserMenu::create([
            'user_code' => $code,
            'list_menu' => '[14]',
            'created_at' => Carbon::now(),
        ]);

        // UserTokenFCM::create([
        //     'user_id' => $code,
        //     'token' => $request->token,
        // ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }

    /**
     * @SWG\Post(
     *   path="/api/RegistAdmin",
	 *   tags={"Users"},
     *   summary="Regist Admin",
     *   operationId="RegistAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Regist Admin",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="name", type="string", example="nama"),
     *              @SWG\Property(property="role", type="string", example="1/2/3"),
     *              @SWG\Property(property="email", type="string", example="nama@mail.com"),
	 *				@SWG\Property(property="password", type="string", example="nama123"),
     *              @SWG\Property(property="no_telp", type="string", example="nullable"),
     *              @SWG\Property(property="alamat", type="string", example="nullable"),
     *              @SWG\Property(property="pekerjaan", type="string", example="nullable"),
     *              @SWG\Property(property="status_admin", type="string", example="nullable")
     *          ),
     *      )
     * )
     *
     */

    public function RegistAdmin(Request $request)
    {   
        $validator = Validator::make($request->all(), [           
            'name' => 'required|string|max:255',
            'role' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
            
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
            return response()->json($rslt, 400);
        }

        $getLastIdUser = Admin::orderBy('code','desc')->first(); 
            if($getLastIdUser){
                $code = 'ADM'.str_pad(((int)substr($getLastIdUser->code,-8) + 1), 8, '0', STR_PAD_LEFT);
            }else{
                $code = 'ADM00000001';
            }

        User::create([
            'admin_user_code' => $code,
            'name' => $request->name,
            'role_code' => $request->role,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => 1,
        ]);

        Admin::create([
            'code' => $code,
            'nama' => $request->name,
            'no_telp' => $this->ReplaceNull($request->no_telp, 'string'),
            'alamat' => $this->ReplaceNull($request->alamat, 'string'),
            'pekerjaan' => $this->ReplaceNull($request->pekerjaan, 'string'),
            'status_admin' => $this->ReplaceNull($request->status_admin, 'string'),
            'created_at' => Carbon::now(),
        ]);

        AdminMenu::create([
            'admin_code' => $code,
            'list_menu' => '[10,12]',
            'created_at' => Carbon::now(),
        ]);

        $rslt =  $this->ResultReturn(200, 'success', 'success');
        return response()->json($rslt, 200);
    
    }


    public function LoginUserTest (Request $request)
    {
        // try {
        //     $validator = Validator::make($request->all(), [
        //         'no_telp' => 'required',
        //         'password' => 'required|min:6',
        //         'token' => 'required',
        //     ]);

        //     if($validator->fails()){
        //         $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
        //         return response()->json($rslt, 400);
        //     }

        //     var_dump('test');

        //     $fieldType = filter_var($request->no_telp, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_telp';
        //     var_dump($fieldType);
        //     $credentials = array($fieldType => $request->no_telp, 'password' => $request->password);
        //     var_dump($credentials);
        //     // if(substr($request->no_telp,0,2) != '08'){
        //     //     $credentials = request(['no_telp', 'password']);
        //     // }else{
        //     //     $credentials = request(['no_telp', 'password']);
        //     // }            
        //     // var_dump($credentials);
        //     if (! $token = auth()->attempt($credentials)) {                
        //         $rslt =  $this->ResultReturn(401, 'Incorrect Email & password', 'Unauthorized');
        //         return response()->json($rslt, 401);
        //     }else{
        //         if(substr($request->no_telp,0,2) != '08'){
        //             $getUser = User::where('email', '=', $request->no_telp)->where('type', '=', 0)->first();
        //         }else{
        //             $getUser = User::where('no_telp', '=', $request->no_telp)->where('type', '=', 0)->first();
        //         }
        //         $rslt =  $this->ResultReturn(200, 'success', $getUser);
        //                     return response()->json($rslt, 200);
        //     }
        //     // $fieldType = filter_var($request->no_telp, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_telp';
 
        //     // if(auth()->attempt(array($fieldType => $input['no_telp'], 'password' => $input['password'])))
        //     // {
        //     //             $rslt =  $this->ResultReturn(200, 'success', 'success');
        //     //                 return response()->json($rslt, 200);
        //     // }else{
        //     //             $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
        //     //                 return response()->json($rslt, 404);
        //     // }
        // } catch (\Exception $ex) {
        //     return response()->json($ex);
        // }
    }

    /**
     * @SWG\Post(
     *   path="/api/LoginUser",
	 *   tags={"Users"},
     *   summary="Login User",
     *   operationId="UsersLogin",
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
	 *				@SWG\Property(property="password", type="string", example="nama123"),
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
                    $getUser = User::where('email', '=', $request->email)->where('type', '=', 0)->first();
                }else{
                    $getUser = User::where('no_telp', '=', $request->email)->where('type', '=', 0)->first();
                }
                // $getUser = User::where('email', '=', $request->email)->where('type', '=', 0)->first();  
                // var_dump($getUser);
               

                if($getUser){
                    $Role = DB::table('role')->where('id','=',$getUser->role_code)->first();
                    $UserProfile = UserProfile::where('code','=',$getUser->admin_user_code)->first();
                    $menu_access = DB::table('user_profile_menu_access')->where('user_code','=',$getUser->admin_user_code)->first(); 
                    
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

                    $objUser = ['code'=>$UserProfile->code,'role'=>$Role->name,'name'=>$UserProfile->nama,
                    'email'=>$getUser->email,'no_telp'=>$UserProfile->no_telp,'foto'=>$UserProfile->foto,'alamat'=>$UserProfile->alamat,
                    'dusun'=>$UserProfile->dusun,'desa'=>$UserProfile->desa,'kecamatan'=>$UserProfile->kecamatan,
                    'pekerjaan'=>$UserProfile->pekerjaan, 'list_menu' => $listval, 'list_val_menu'=> $listvalmenu];

                    // UserTokenFCM::where('user_id', '=', $UserProfile->code)
                    // ->update([
                    //     'token' => $request->token,
                    // ]);


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
     * @SWG\Post(
     *   path="/api/LoginAdmin",
	 *   tags={"Users"},
     *   summary="Login Admin",
     *   operationId="LoginAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Login Admin",
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
                    $Role = DB::table('role')->where('id','=',$getUser->role_code)->first();
                    $Admin = Admin::where('code','=',$getUser->admin_user_code)->first();
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

                    $objUser = ['code'=>$Admin->code,'role'=>$Role->name,'name'=>$Admin->nama,
                    'email'=>$getUser->email,'no_telp'=>$Admin->no_telp,'status_admin'=>$Admin->status_admin,'alamat'=>$Admin->alamat,
                    'pekerjaan'=>$Admin->pekerjaan, 'list_menu' => $listval, 'list_val_menu'=> $listvalmenu];

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
    public function ForgotPassword (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            $getUser = User::where('email', '=', $request->email)->first();

            if ($getUser) {                
                $newPass = $this->RandomPassword();
                User::where('email', '=', $request->email)
                    ->update(['password' => bcrypt($newPass)]);                
                $usernew = ['User'=>$getUser, 'new_password' => $newPass];
                $rslt =  $this->ResultReturn(200, 'success', $usernew);
                return response()->json($rslt, 200);
            }else{
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
     *              @SWG\Property(property="code", type="string", example="11_0222"),
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
                'code' => 'required',
                'email' => 'required|string|email|max:255',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }
            $password = '123456';
            User::where('admin_user_code', '=', $request->code)->where('email', '=', $request->email)
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
     *   path="/api/GantiPasswordUser",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="GantiPasswordUser",
     *   operationId="GantiPasswordUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="GantiPasswordUser",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
	 *				@SWG\Property(property="email", type="string", example="nama@mail.com"),
     *              @SWG\Property(property="password", type="string", example="121212"),
     *              @SWG\Property(property="new_password", type="string", example="121212"),
     *              @SWG\Property(property="confirm_password", type="string", example="121212"),
     *          ),
     *      )
     * )
     *
     */
    public function GantiPasswordUser (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6',
                'confirm_password' => 'required|string|min:6',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            $credentials = request(['email', 'password']);
            if (! $token = auth()->attempt($credentials)) {                
                $rslt =  $this->ResultReturn(401, 'Incorrect Email & password', 'Unauthorized');
                return response()->json($rslt, 401);
            }elseif($request->new_password != $request->confirm_password){
                $rslt =  $this->ResultReturn(401, 'Incorrect New Password & Confirm password', 'Unauthorized');
                return response()->json($rslt, 401);
            }else{
                User::where('email', '=', $request->email)
                    ->update([
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

    
    public function EditProfile (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6',
                'confirm_password' => 'required|string|min:6',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            $credentials = request(['email', 'password']);
            if (! $token = auth()->attempt($credentials)) {                
                $rslt =  $this->ResultReturn(401, 'Incorrect Email & password', 'Unauthorized');
                return response()->json($rslt, 401);
            }elseif($request->new_password != $request->confirm_password){
                $rslt =  $this->ResultReturn(401, 'Incorrect New Password & Confirm password', 'Unauthorized');
                return response()->json($rslt, 401);
            }else{
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
     * @SWG\Post(
     *   path="/api/EditUser",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="EditUser",
     *   operationId="EditUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="EditUser",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
	 *				@SWG\Property(property="code", type="string", example="USR0000001"),
     *              @SWG\Property(property="name", type="string", example="nama"),
     *              @SWG\Property(property="alamat", type="string", example="nullable"),
     *              @SWG\Property(property="dusun", type="string", example="nullable"),
     *              @SWG\Property(property="desa", type="string", example="nullable"),
     *              @SWG\Property(property="kecamatan", type="string", example="nullable"),
     *              @SWG\Property(property="pekerjaan", type="string", example="nullable"),
     *              @SWG\Property(property="foto", type="string", example="nullable")
     *          ),
     *      )
     * )
     *
     */
    public function EditUser (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required',
                'name' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors(), $validator->errors());
                return response()->json($rslt, 400);
            }

            // 'code' => $code,
            // 'nama' => $request->name,
            // 'no_telp' => $this->ReplaceNull($request->no_telp, 'string'),
            // 'alamat' => $this->ReplaceNull($request->alamat, 'string'),
            // 'pekerjaan' => $this->ReplaceNull($request->pekerjaan, 'string'),
            // 'foto' => $this->ReplaceNull($request->foto, 'string'),
            // 'created_at' => Carbon::now(),

            UserProfile::where('code', '=', $request->code)
                    ->update([
                        'nama' => $request->name,
                        // 'no_telp' => $this->ReplaceNull($request->no_telp, 'string'),
                        'alamat' => $this->ReplaceNull($request->alamat, 'string'),
                        'dusun' => $this->ReplaceNull($request->dusun, 'string'),
                        'desa' => $this->ReplaceNull($request->desa, 'string'),
                        'kecamatan' => $this->ReplaceNull($request->kecamatan, 'string'),
                        'pekerjaan' => $this->ReplaceNull($request->pekerjaan, 'string'),
                        'foto' => $this->ReplaceNull($request->foto, 'string'),
                        'updated_at' => Carbon::now(),
                    ]);

            User::where('admin_user_code', '=', $request->code)
                    ->update([
                        'name' => $request->name,
                        'updated_at' => Carbon::now(),
                    ]);

                $rslt =  $this->ResultReturn(200, 'Success Update Profile', 'success');
                return response()->json($rslt, 200);
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
     *   summary="Get User",
     *   operationId="GetUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetUser(Request $request){
        try{
            // var_dump(count($GetLahanNotComplete));
            $GetUser = DB::table('user_profile')
            ->select('user_profile.code','user_profile.nama','user_profile.no_telp','user_profile.alamat',
            'user_profile.dusun','user_profile.desa','user_profile.kecamatan','user_profile.pekerjaan','user_profile.foto','users.email')
            ->join('users','users.admin_user_code','=', 'user_profile.code')
            ->where('user_profile.code','<>','USR00000001')
            ->get();
            if(count($GetUser)!=0){ 
                $count = DB::table('user_profile')
                ->join('users','users.admin_user_code','=', 'user_profile.code')
                ->where('user_profile.code','<>','USR00000001')
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

    function RandomPassword() {
        $maxLengthPass=8;
        $pass = array(); 
        
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char='!@#$*';
        $number='1234567890';
        
        $getNumberRandChar = rand(1,$maxLengthPass-1);
        $getNumberRandNumber = rand(1,$maxLengthPass-1);
        if($getNumberRandChar == $getNumberRandNumber){
            $getNumberRandChar = 2;
            $getNumberRandNumber = 5;
        }

        $alphaLength = strlen($alphabet) - 1;
        $charLength = strlen($char) - 1;
        $numberLength = strlen($number) - 1; 

        for ($i = 0; $i < $maxLengthPass; $i++) {
            $varPass='';
            if($getNumberRandChar == $i){
                $n = rand(0, $charLength);
                $varPass=$char[$n];
            }elseif($getNumberRandNumber == $i){
                $n = rand(0, $numberLength);
                $varPass=$number[$n];
            }else{
                $n = rand(0, $alphaLength); 
                $varPass=$alphabet[$n]; 
            }           
            $pass[] = $varPass;
        }
        return implode($pass); 
    }

    protected function RespondWithToken($token, $data)
    {
        return response()->json([
            'success'=>true,
            'data'=>$data,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteUser",
	 *   tags={"Users"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete User",
     *   operationId="DeleteUser",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete User",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
	 *				@SWG\Property(property="code", type="string", example="nama123"),
     *          ),
     *      )
     * )
     *
     */
    public function DeleteUser(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'code' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
    
            DB::table('user_profile')->where('code', $request->code)->delete();
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
}

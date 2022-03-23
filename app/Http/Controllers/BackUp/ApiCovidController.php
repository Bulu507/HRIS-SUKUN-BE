<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7;

class ApiCovidController extends Controller
{
    public function BaseUrl (){
        return 'https://api.magelangkab.go.id/corona/zonasi';
    }
    public function TokenAuth (){
        return '209eb3ff415476ff96b86b5d157d6874';
    }
    public function GetCovid($valUrl){
        $baseurl = $this->BaseUrl();
        $tokenauth = $this->TokenAuth();
        
            $url = $baseurl.$valUrl;
            
            $headers = array();
            $headers[] = 'service: covid';
            $headers[] = 'token:' .$tokenauth;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //Send the request
            $response = curl_exec($ch);

            curl_close($ch);
        
        $arrayResponse = json_decode($response, true);

        if($arrayResponse){
            if($arrayResponse['kabmagelang']['status']['code'] == 200){
                $data = $arrayResponse['kabmagelang']['result']['data'];
                $rslt =  $this->ResultReturn(200,  'success', $data);
                return response()->json($rslt, 200);
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404); 
            }
        }else{
            $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
            return response()->json($rslt, 404);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetCovidKabupaten",
     *   tags={"ApiCovid"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Covid Kabupaten",
     *   operationId="GetCovidKabupaten",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetCovidKabupaten(Request $request){
        return $this->GetCovid('/score_kabupaten');
    }
    /**
     * @SWG\Get(
     *   path="/api/GetCovidKecamatan",
     *   tags={"ApiCovid"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Covid Kecamatan",
     *   operationId="GetCovidKecamatan",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetCovidKecamatan(Request $request){
        return $this->GetCovid('/score_kecamatan');
    }
    /**
     * @SWG\Get(
     *   path="/api/GetCovidDesa",
     *   tags={"ApiCovid"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Covid Desa",
     *   operationId="GetCovidDesa",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function GetCovidDesa(Request $request){
        return $this->GetCovid('/score_desa');
    }
}

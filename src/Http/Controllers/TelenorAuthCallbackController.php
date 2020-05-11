<?php
namespace TelenorSMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Cache;

class TelenorAuthCallbackController extends Controller {

    public function callback(Request $request){
        if($request->has('code')){
            $client = new HttpClient([ 'base_uri' => config('telenorsms.base_url')]);
            $response = $client->post('oauth/v1/token', [
                'form_params' => [
                    'client_id' => config('telenorsms.sms.client_id'),
                    'client_secret' => config('telenorsms.sms.client_secret'),
                    'grant_type' => 'authorization_code',
                    'code' => $request->code
                ]
            ]);
            if($response->getStatusCode() == 200){
                $responseBody = json_decode($response->getBody());
                if($responseBody->status == 'approved'){
                    Cache::put('telenorsms_access_token', $responseBody->accessToken, $responseBody->expiresIn);
                }
            }
        }
    }

}
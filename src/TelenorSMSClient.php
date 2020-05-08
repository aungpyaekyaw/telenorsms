<?php
namespace TelenorSMS;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Cache;

class TelenorSMSClient
{
    protected $client;

    public function __construct(HttpClient $client = null){
        $this->client = $client;
    }

    private function client(){
        return $this->client ?: $this->client = new HttpClient([ 'base_uri' => config('telenorsms.base_url')]);
    }

    public function send($params){
        $token = Cache::get('telenorsms_access_token');
        if(empty($token)){
            throw new \Exception('Telenor SMS Token cannot be empty.');
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        return $this->client()->post('v3/mm/en/communicationMessage/send', [
            'headers' => $headers,
            'json' =>  $params
        ]);

    }

}
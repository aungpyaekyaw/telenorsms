<?php
namespace TelenorSMS\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client as HttpClient;

class AuthorizeTelenorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telenorsms:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request user authorization from telenor myanmar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $client = new HttpClient(['base_uri' => config('telenorsms.base_url')]);
        $client->request('GET', 'oauth/v1/userAuthorize', [
            'query'=>[
                'client_id' => config('telenorsms.sms.client_id'),
                'response_type' => 'code',
                'scope' => 'read'
            ]
        ]);
    }
}
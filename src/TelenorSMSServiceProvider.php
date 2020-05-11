<?php
namespace TelenorSMS;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Route;
use TelenorSMS\Console\Commands\AuthorizeTelenorCommand;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;

class TelenorSMSServiceProvider extends ServiceProvider
{

    public function boot(){

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->app->when(TelenorSMSChannel::class)
            ->needs(TelenorSMSClient::class)
            ->give(function() {
                return new TelenorSMSClient(new HttpClient([ 'base_uri' => config('telenorsms.base_url')]));
            });

        Route::middleware(['web'])
            ->group(function(){
                Route::get(config('telenorsms.sms.callback_url'), 'TelenorSMS\Http\Controllers\TelenorAuthCallbackController@callback')->name('telenorsms.callback');
            });
    }

    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/../config/telenorsms.php', 'telenorsms');

        $this->commands[] = 'command.telenorsms.auth';
        $this->app->singleton('command.telenorsms.auth', function ($app) {
            return new AuthorizeTelenorCommand();
        });
        $this->commands($this->commands);

        $this->app->singleton('telenorsms.console.kernel', function($app){
            $dispatcher = $app->make(\Illuminate\Contracts\Events\Dispatcher::class);
            return new \TelenorSMS\Console\Kernel($app, $dispatcher);
        });
        $this->app->make('telenorsms.console.kernel');

        $this->app->singleton(TelenorSMSClient::class, static function($app){
            return new TelenorSMSClient(new HttpClient([ 'base_uri' => config('telenorsms.base_url')]));
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('telenorsms', function ($app) {
                return new TelenorSMSChannel($app[TelenorSMSClient::class]);
            });
        });

    }

    public function bootForConsole(){

        $this->publishes([
            __DIR__.'/../config/telenorsms.php' => config_path('telenorsms.php'),
        ], 'telenorsms.config');
    }
}
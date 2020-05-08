<?php


namespace TelenorSMS;

use Illuminate\Notifications\Notification;


class TelenorSMSChannel
{
    /**
     * @var TelenorSMSClient
     */
    protected $client;

    public function __construct(TelenorSmsClient $client){
        $this->client = $client;
    }

    /**
     * @param $notificable
     * @param Notification $notification
     * @throws \Exception
     */
    public function send($notificable, Notification $notification){
        $message = $notification->toTelenorSms($notificable);
        if(is_string($message)){
            $message = TelenorMessage::create($message);
        }
        if($message->toNotGiven()){
            if(!$to = $notificable->routeNotificationFor('telenorsms')){
                throw new \Exception('Receiver cannot be null.');
            }
            $message->to($to);
        }

        $this->client->send($message->toArray());
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Channel;
use App\Models\Notification;
use Illuminate\Http\Request;


/**
 * @group Notifications
 *
 * APIs pour gérer les notifications
 */
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNotificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNotificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNotificationRequest  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }


    /**
     * 
     * Send notification
     * 
     * Permet d'envoyer une notification
     *
     */
    public function send(Request $request)
    {
        $channel = Channel::firstOrCreate(['name' => $request->to]);

        $notification = Notification::create([
            'title' => $request->title,
            'text' => $request->message,
            'channel_name' => $channel->name,
        ]);

        $notification->send();


        return httpSuccess('Notifications sent');
    }

    /**
     * 
     * Get user notifications
     * 
     * Retourne les notifications de l'utilisateur
     *
     */
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();

        return httpSuccess("Notifications", $user->notifications);
    }
}

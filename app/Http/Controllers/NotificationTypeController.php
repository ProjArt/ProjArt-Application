<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationTypeRequest;
use App\Http\Requests\UpdateNotificationTypeRequest;
use App\Models\NotificationType;

class NotificationTypeController extends Controller
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
     * @param  \App\Http\Requests\StoreNotificationTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNotificationTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotificationType  $notificationType
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationType $notificationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNotificationTypeRequest  $request
     * @param  \App\Models\NotificationType  $notificationType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNotificationTypeRequest $request, NotificationType $notificationType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotificationType  $notificationType
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationType $notificationType)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusEvent;
use App\Notifications\OrderStatusBrodcast;
use App\Notifications\OrderStatusDatabase;
use App\Notifications\OrderStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function mailNotification()
    {
        return view('mail');
    }

    public function smsNotification()
    {
        return view('sms');
    }

    public function slackNotification()
    {
        return view('slack');
    }

    public function databaseNotification()
    {
        $user=auth()->user();
        $notifications=$user->notifications;
        return view('database',compact('notifications'));
    }

    public function readNotification($notificationId,$type=0)
    {
        $user=auth()->user();
        if($type==1)
        {
            $user->unreadNotifications()->markAsRead();
            //other way for read notification
            // $user->unreadNotifications()->update(['read_at' => now()])
        }
        else
            $user->unreadNotifications()->find($notificationId)->markAsRead();

        return back();
    }
    public function deleteNotification($notificationId)
    {
        $user=auth()->user();
        $result=$user->readNotifications()->find($notificationId)->delete();
        return back();
    }
    public function brodcastNotification()
    {
        return view('brodcast');
    }

    public function changeStatus(Request $request)
    {
        if($request->status)
        {
            $user=auth()->user();
            if($request->type=='mail')
            {
                // $user->notify(new OrderStatusMail($request->status));
                Notification::send($user, new OrderStatusMail($request->status));
            }
            else if($request->type=='database')
            {
                $user->notify(new OrderStatusDatabase($request->status));
            }
            else if($request->type=='brodcast')
            {
                $user->notify(new OrderStatusBrodcast($request->status));
                event(new OrderStatusEvent($request->status,auth()->id()));
            }
            
        }
        return response()->json(["code"=>200,"message"=>"Order status changed"],200);
    }
}

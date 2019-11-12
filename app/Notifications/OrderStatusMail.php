<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusMail extends Notification implements ShouldQueue
{
    use Queueable;

    public $orderStatus;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($orderStatus)
    {
        $this->orderStatus=$orderStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*********first method*****************/
        // return (new MailMessage)
        //         ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        //         ->error() //  action button will be red instead of blue
        //         ->greeting('Hello!')
        //         ->line('One of your invoices has been paid!')
        //         ->action('View Invoice','https://www.google.com')
        //         ->line('Thank you for using our application!');
        
        /*********second method*****************/
        // return (new MailMessage)->view(
        //     'mail.order-email', ['orderStatus' => $this->orderStatus]
        // );
        
        
        /*********third method*****************/
        // it is not working
        // return (new Mailable($this->orderStatus))->to($notifiable->email);
        // 
        /*********fourth method*****************/

        $url = url('/');

        return (new MailMessage)
                ->subject('Order status')
                ->markdown('mail.order-status', ['url' => $url,"orderStatus"=>$this->orderStatus]);
    }

}

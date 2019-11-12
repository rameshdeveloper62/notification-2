Notification 

** Type of notification: **
	(1) mail
	(2) SMS
	(3) Nexmo
	(4) Slack
	(5) database
	(6) broadcast


** Creating Notifications **
	In Laravel, each notification is represented by a single class (typically stored in the  app/Notifications directory). Don't worry if you don't see this directory in your application, it will be created for you when you run the make:notification Artisan command:

	php artisan make:notification OrderStatus



** Notification method **
	via()
	toMail()
	toDatabase()
	toBroadcast()
	toArray()

	Each notification class contains a via method and a variable number of message building methods (such as toMail or toDatabase) that convert the notification to a message optimized for that particular channel


** Sending Notifications **

	Notifications may be sent in two ways: using the notify method of the Notifiable trait or using the Notification facade

	- Notifiable trait
```
	namespace App;

	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;

	class User extends Authenticatable
	{
	    use Notifiable;
	}

	```



	This trait is utilized by the default App\User model and contains one method that may be used to send notifications: notify. The notify method expects to receive a notification instance:

	```
	use App\Notifications\OrderStatus;

	$user->notify(new OrderStatus($order));

	```
	-Using The Notification Facade

	This is useful primarily when you need to send a notification to multiple notifiable entities such as a collection of users. To send notifications using the facade, pass all of the notifiable entities and the notification instance to the send method:

	```
	Notification::send($users, new OrderStatus($order));

	```

	The via method receives a $notifiable instance, which will be an instance of the class to which the notification is being sent. You may use $notifiable to determine which channels the notification should be delivered on:

	```
	public function via($notifiable)
	{
	    return $notifiable->prefers_sms ? ['nexmo'] : ['mail', 'database'];
	}
	```

	** Queueing Notifications **


		This driver stores queued jobs in the database. Before enabling this driver, you will need to create database tables to store your queued and failed jobs:

		**php artisan queue:table** for store job record
		**php artisan queue:failed-table** for store failed job record
		**php artisan migrate**

		Sending notifications can take time, especially if the channel needs an external API call to deliver the notification. To speed up your application's response time, let your notification be queued by adding the ShouldQueue interface and Queueable trait to your class. The interface and trait are already imported for all notifications generated using make:notification, so you may immediately add them to your notification class:

		```
			namespace App\Notifications;

			use Illuminate\Bus\Queueable;
			use Illuminate\Notifications\Notification;
			use Illuminate\Contracts\Queue\ShouldQueue;

			class InvoicePaid extends Notification implements ShouldQueue
			{
			    use Queueable;

			    // ...
			}

		```

		Once the ShouldQueue interface has been added to your notification, you may send the notification like normal. Laravel will detect the ShouldQueue interface on the class and automatically queue the delivery of the notification:

		```
		$user->notify(new InvoicePaid($invoice));

		```

		- delay notification

		```
		$when = now()->addMinutes(10);

		$user->notify((new InvoicePaid($invoice))->delay($when));

		```

	** You need to run php artisan queue:listner command **

** On-Demand Notifications without use Notifiable trait ** 

	Sometimes you may need to send a notification to someone who is not stored as a "user" of your application. Using the Notification::route method, you may specify ad-hoc notification routing information before sending the notification:

	```
		Notification::route('mail', 'taylor@example.com')
	    	->route('nexmo', '5555555555')
	    	->notify(new OrderStatusEmail($order));

    ```

** Notification method **

	** toMail() Notifications **

	- Formatting Mail Messages

	```
	public function toMail($notifiable)
	{
		$url = url('/invoice/'.$this->invoice->id);

		return (new OrderStatusEmail)
				->to($this->user->email)
				->from('test@example.com', 'Example')
				->error() //  action button will be red instead of blue
	            ->greeting('Hello!')
	            ->line('One of your invoices has been paid!')
	            ->action('View Invoice', $url)
	            ->line('Thank you for using our application!');
	}
	```

	- Other Notification Formatting Options

	```
	public function toMail($notifiable)
	{
	    return (new OrderStatusEmail)->view(
	        'emails.name', ['invoice' => $this->invoice]
	    );
	}
	```

	- Customizing The Recipient

	```
	namespace App;

	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;

	class User extends Authenticatable
	{
	    use Notifiable;

	    /**
	     * Route notifications for the mail channel.
	     *
	     * @param  \Illuminate\Notifications\Notification  $notification
	     * @return string
	     */
	    public function routeNotificationForMail($notification)
	    {
	        return $this->email_address;
	    }
	}

	```

	- Customizing The Templates

	php artisan vendor:publish --tag=laravel-notifications


	- Previewing Mail Notifications

		When designing a mail notification template, it is convenient to quickly preview the rendered mail message in your browser like a typical Blade template. For this reason, Laravel allows you to return any mail message generated by a mail notification directly from a route Closure or controller. When a MailMessage is returned, it will be rendered and displayed in the browser, allowing you to quickly preview its design without needing to send it to an actual email address:

		```
		Route::get('mail', function () {
		    $invoice = App\Invoice::find(1);

		    return (new App\Notifications\InvoicePaid($invoice))
		                ->toMail($invoice->user);
		});

		```
	** Database Notifications **
	
		The database notification channel stores the notification information in a database table. This table will contain information such as the notification type as well as custom JSON data that describes the notification.

		php artisan notifications:table

		php artisan migrate
		
		- Queue is not required in database channel.

		- Formatting Database Notifications

			If a notification supports being stored in a database table, you should define a toDatabase or  toArray method on the notification class. This method will receive a $notifiable entity and should return a plain PHP array. The returned array will be encoded as JSON and stored in the  data column of your notifications table. Let's take a look at an example toArray method:

			```
			public function toArray($notifiable)
			{
			    return [
			        'orderId' => $this->orderId,
			        'orderStatus' => $this->orderStatus,
			    ];
			}

			```

			- toDatabase Vs. toArray

				The toArray method is also used by the broadcast channel to determine which data to broadcast to your JavaScript client. If you would like to have two different array representations for the database and broadcast channels, you should define a toDatabase method instead of a toArray method.


			- Accessing The Notifications

				Once notifications are stored in the database, you need a convenient way to access them from your notifiable entities. The Illuminate\Notifications\Notifiable trait, which is included on Laravel's default App\User model, includes a notifications Eloquent relationship that returns the notifications for the entity. To fetch notifications, you may access this method like any other Eloquent relationship. By default, notifications will be sorted by the created_at timestamp:

				- All notification
				```
				$user = App\User::find(1);

				foreach ($user->notifications as $notification) {
				    echo $notification->type;
				}
				```
				- UnreadNotifications

				```
				$user = App\User::find(1);

				foreach ($user->unreadNotifications as $notification) {
				    echo $notification->type;
				}

				```

				- Marking Notifications As Read


				```
				$user = App\User::find(1);

				foreach ($user->unreadNotifications as $notification) {
				    $notification->markAsRead();
				}
				```

				```
				$user->unreadNotifications->markAsRead();
				
				```

				```
				$user->unreadNotifications()->update(['read_at' => now()]);

				```
				
				You may delete the notifications to remove them from the table entirely:

				```
				$user->notifications()->delete();

				```
** Broadcast Notifications **
	 

	 Before broadcasting notifications, you should configure and be familiar with Laravel's event broadcasting services. Event broadcasting provides a way to react to server-side fired Laravel events from your JavaScript client.

	 ** Formatting Broadcast Notifications **


		 The broadcast channel broadcasts notifications using Laravel's event broadcasting services, allowing your JavaScript client to catch notifications in realtime. If a notification supports broadcasting, you can define a toBroadcast method on the notification class. This method will receive a $notifiable entity and should return a BroadcastMessage instance. If the  toBroadcast method does not exist, the toArray method will be used to gather the data that should be broadcast. The returned data will be encoded as JSON and broadcast to your JavaScript client. Let's take a look at an example toBroadcast method:

		```
		use Illuminate\Notifications\Messages\BroadcastMessage;

		/**
		 * Get the broadcastable representation of the notification.
		 *
		 * @param  mixed  $notifiable
		 * @return BroadcastMessage
		 */
		public function toBroadcast($notifiable)
		{
		    return new BroadcastMessage([
		        'invoice_id' => $this->invoice->id,
		        'amount' => $this->invoice->amount,
		    ]);
		}

		```

	** Listening For Notifications **

		Notifications will broadcast on a private channel formatted using a {notifiable}.{id} convention. So, if you are sending a notification to a App\User instance with an ID of 1, the notification will be broadcast on the App.User.1 private channel. When using Laravel Echo, you may easily listen for notifications on a channel using the notification helper method:

		```
		Echo.private('App.User.' + userId)
    	.notification((notification) => {
        	console.log(notification.type);
    	});

    	```
	** You must create build in vuejs by npm run dev **
	** it is run once time npm install -g laravel-echo-server, it will be installed in global.** 
	** laravel-echo-server init for generate laravel-echo-server.json configure file**
	** You must create start echo server by laravel-echo-sver start **


    ** Customizing The Notification Channel **

    ```
    	namespace App;

		use Illuminate\Notifications\Notifiable;
		use Illuminate\Broadcasting\PrivateChannel;
		use Illuminate\Foundation\Auth\User as Authenticatable;

		class User extends Authenticatable
		{
		    use Notifiable;

		    /**
		     * The channels the user receives notification broadcasts on.
		     *
		     * @return string
		     */
		    public function receivesBroadcastNotificationsOn()
		    {
		        return 'users.'.$this->id;
		    }
		}

    ```
** Localizing Notifications **
	
	```
	$user->notify((new InvoicePaid($invoice))->locale('es'));

	```

	Localization of multiple notifiable entries may also be achieved via the Notification facade:


	```
	
	Notification::locale('es')->send($users, new InvoicePaid($invoice));

	```

	** User Preferred Locales **

	Sometimes, applications store each user's preferred locale. By implementing the  HasLocalePreference contract on your notifiable model, you may instruct Laravel to use this stored locale when sending a notification:


	```
		use Illuminate\Contracts\Translation\HasLocalePreference;

		class User extends Model implements HasLocalePreference
		{
		    /**
		     * Get the user's preferred locale.
		     *
		     * @return string
		     */
		    public function preferredLocale()
		    {
		        return $this->locale;
		    }
		}

	```

	Once you have implemented the interface, Laravel will automatically use the preferred locale when sending notifications and mailables to the model. Therefore, there is no need to call the  locale method when using this interface

	```
	$user->notify(new InvoicePaid($invoice));

	```

** Notification Events **
	

	When a notification is sent, the Illuminate\Notifications\Events\NotificationSent event is fired by the notification system. This contains the "notifiable" entity and the notification instance itself. You may register listeners for this event in your EventServiceProvider:

	```
		protected $listen = [
		    'Illuminate\Notifications\Events\NotificationSent' => [
		        'App\Listeners\LogNotification',
		    ],
		];

	```

	After registering listeners in your EventServiceProvider, use the event:generate Artisan command to quickly generate listener classes.

	Within an event listener, you may access the notifiable, notification, and channel properties on the event to learn more about the notification recipient or the notification itself:

	```
		public function handle(NotificationSent $event)
		{
			// $event->channel
			// $event->notifiable
			// $event->notification
			// $event->response
		}

	```

** Custom Channels **
	
	Laravel ships with a handful of notification channels, but you may want to write your own drivers to deliver notifications via other channels. Laravel makes it simple. To get started, define a class that contains a send method. The method should receive two arguments: a  $notifiable and a $notification:

	```
		namespace App\Channels;

		use Illuminate\Notifications\Notification;

		class VoiceChannel
		{
		    /**
		     * Send the given notification.
		     *
		     * @param  mixed  $notifiable
		     * @param  \Illuminate\Notifications\Notification  $notification
		     * @return void
		     */
		    public function send($notifiable, Notification $notification)
		    {
		        $message = $notification->toVoice($notifiable);

		        // Send notification to the $notifiable instance...
		    }
		}

	```
	Once your notification channel class has been defined, you may return the class name from the  via method of any of your notifications:

	```
		namespace App\Notifications;

		use Illuminate\Bus\Queueable;
		use App\Channels\VoiceChannel;
		use App\Channels\Messages\VoiceMessage;
		use Illuminate\Notifications\Notification;
		use Illuminate\Contracts\Queue\ShouldQueue;

		class InvoicePaid extends Notification
		{
		    use Queueable;

		    /**
		     * Get the notification channels.
		     *
		     * @param  mixed  $notifiable
		     * @return array|string
		     */
		    public function via($notifiable)
		    {
		        return [VoiceChannel::class];
		    }

		    /**
		     * Get the voice representation of the notification.
		     *
		     * @param  mixed  $notifiable
		     * @return VoiceMessage
		     */
		    public function toVoice($notifiable)
		    {
		        // ...
		    }
		}

	```
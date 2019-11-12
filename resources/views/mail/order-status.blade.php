@component('mail::message')
# Order status

@component('mail::button', ['url' => '','color' => 'green'])
Login
@endcomponent

@component('mail::panel')
Your order status is {{isset($orderStatus)?$orderStatus:'Shipped'}}
@endcomponent


@component('mail::table')
| Order Id      | item          | Quantity  | Total |
| ------------- |:-------------:| --------: |
| #010101       | Mobile        | 5         |   $5000|

@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

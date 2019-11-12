@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Brodcast Notification</div>
                <div class="card-body">
                    <div class="alert alert-success d-none" role="alert">
                            
                    </div>
                    <table class="table table-inverse">
                        <thead>
                            <tr>
                                <th>Order Id</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>#010101</th>
                                <th>Mobile</th>
                                <th>5</th>
                                <th>5000</th>
                                <th>
                                    <select name="status" id="status" data-type='brodcast'>
                                        <option value="pending">Pending</option>
                                        <option value="packed">Packed</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="dipached">Disptached</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="user-list"></div>
                    <div id="sayHi"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    <script>
    
    </script>
@endpush
@endsection

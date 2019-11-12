@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Slack Notification</div>
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
                                    <select name="status" id="status" data-type='dispatching job'>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

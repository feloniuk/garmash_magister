@extends('layouts.app')

@section('content')
    <div class="container">
    <!-- Page Header -->
    <div class="page-header row no-gutters py-4">
        <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
            <h3 class="page-title">Замовлення #{{$order->id}}</h3>
        </div>
    </div>
    @if($message = session('message'))
        <div class="alert alert-success" role="alert">
            {{ $message }}
        </div>
    @endif
    <!-- End Page Header -->
    <!-- Default Light Table -->
    <div class="row">
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom">
                    <p>Інформація про клієнта</p>
                </div>
                <div class="card-body p-0 pb-3 text-center">
                    <table class="table mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">Ім'я</th>
                            <th scope="col" class="border-0">Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->user as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name}}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom">
                    <p>Інформація про замовлення</p>
                </div>
                <div class="card-body p-0 pb-3 text-center">
                    <table class="table mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">Продукція</th>
                            <th scope="col" class="border-0">Кількість (шт)</th>
                            <th scope="col" class="border-0">Ціна (грн)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product[0]->id }}</td>
                                <td>{{ $product[0]->title }}</td>
                                <td>{{ $product[0]->amount }}</td>
                                <td>{{ $product[0]->price }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="p-2 d-flex justify-content-between" style="width: 800px;margin: 0 auto;font-size: 25px;">
                        <p>Загальна вартість:</p>
                        <div style="color: green;">{{$order->orderSum}} грн</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Default Light Table -->
    <div class="row">
        <div class="col">
            <form class="justify-content-between" action="{{route('orders.update', $order->id)}}" method="post">
                @method('put')
                @csrf
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Статус</label>
                    <select class="form-control" name="status" id="exampleFormControlSelect1">
                        <option @if($order->status == 'active') selected @endif value="active">Нове</option>
                        <option @if($order->status == 'apply') selected @endif value="apply">Прийняте</option>
                        <option @if($order->status == 'reject') selected @endif value="reject">Відхилено</option>
                    </select>
                    @if ($errors->has('status'))
                        <div style="color: #ff2432" role="alert">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Коментарій</label>
                    <textarea class="form-control" name="comment" id="exampleFormControlTextarea1" rows="3">{{$order->comment}}</textarea>
                    @if ($errors->has('comment'))
                        <div style="color: #ff2432" role="alert">
                            {{ $errors->first('comment') }}
                        </div>
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-success">Зберегти</button>
                    <a href="{{ route('pdf', $order->id) }}" class="btn btn-info" target="_blank">Зберегти в PDF</a>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

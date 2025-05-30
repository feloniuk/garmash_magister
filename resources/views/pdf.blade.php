<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Замовлення</title>
    <style>
        body { font-family: DejaVu Sans }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<div style="margin: 0 auto;display: block;width: 500px;">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header row no-gutters py-4">
            <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
                <h3 class="page-title">Накладна на Замовлення #{{$order->id}}</h3>
            </div>
        </div>
    <!-- End Page Header -->
        <!-- Default Light Table -->
        <div class="row">
            <div class="col">
                <div class="card card-small mb-4">
                    <div class="card-header border-bottom">
                        <h3>Інформація про клієнта</h3>
                    </div>
                    <div class="card-body p-0 pb-3 text-center">
                        <table class="table mb-0" style="width:100%;">
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
                        <h3>Інформація про замовлення</h3>
                    </div>
                    <div class="card-body p-0 pb-3 text-center">
                        <table class="table mb-0" style="width:100%;">
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
    </div>
</div>
</body>
</html>

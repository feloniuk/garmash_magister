@extends('layouts.app')

@section('content')
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-md-8 order-md-2 col-lg-9">
                    <div class="container-fluid">
                        <div class="row">
                            <h3>Замовлення</h3>
                            <table class="table mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="border-0">#</th>
                                    <th scope="col" class="border-0">Клієнт</th>
                                    <th scope="col" class="border-0">Загальна вартість</th>
                                    <th scope="col" class="border-0">Дата</th>
                                    <th scope="col" class="border-0">Статус</th>
                                    <th scope="col" class="border-0">Деталі</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user[0]->name }}</td>
                                        <td>{{ $order->orderSum }} грн</td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>
                                            <span style="color:{{ str_replace(['active', 'apply', 'reject'], ['green', 'blue', 'red'], $order->status) }}"
                                                 role="alert">
                                                {{ str_replace(['active', 'apply', 'reject'], ['Нове', 'Прийняте', 'Відхилено'], $order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.view', $order->id) }}"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function addProduct(productId)
            {
                $.ajax({
                    url: '{{ route('product.add') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {productId: productId},
                    dataType: 'json',
                    success: function(data){
                        if (data['error']) {
                            alert(data['error']);
                            return;
                        }
                        let btn = $('#add-button-' + data);
                        btn.prop('disabled', true);
                        btn.html('Вже додано');
                        alert('Продукт додано до замовлення');
                    }
                });
            }
        </script>
@endsection

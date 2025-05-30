@extends('layouts.app')

@section('content')
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-md-8 order-md-2 col-lg-9">
                    <div class="container-fluid">
                        <div class="row">
                            <h3>Продукція</h3>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="search">
                            </div>
                            <table class="table mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="border-0">#</th>
                                    <th scope="col" class="border-0">Назва</th>
                                    <th scope="col" class="border-0">Ціна</th>
                                    <th scope="col" class="border-0">Термін придатності</th>
                                    <th scope="col" class="border-0">Кількість</th>
                                    <th scope="col" class="border-0">Код</th>
                                </tr>
                                </thead>
                                <tbody id="table-body">
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->title }}</td>
                                        <td>{{ $product->price }} грн</td>
                                        <td>{{ $product->termin }} днів</td>
                                        <td>{{ $product->quantity }} шт</td>
                                        <td>{{ $product->code }}</td>
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

            $('#search').on('keyup', function () {
                let searchVal = $(this).val();
                $.ajax({
                    url: '{{ route('products.search') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {search: searchVal},
                    dataType: 'json',
                    success: function(data){
                        let html = '';
                        data.forEach(function (item) {
                            html += '<tr>'+
                                        '<td>' + item.id + '</td>'+
                                        '<td>' + item.title + '</td>'+
                                        '<td>' + item.price + ' грн</td>'+
                                        '<td>' + item.termin + ' днів</td>'+
                                        '<td>' + item.quantity + ' шт</td>'+
                                        '<td>' + item.code + '</td>'+
                                    '</tr>';
                        });
                       $('#table-body').html(html)
                    }
                });
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#search').focus();
            });
        </script>
@endsection

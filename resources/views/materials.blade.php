@extends('layouts.app')

@section('content')
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-md-8 order-md-2 col-lg-9">
                    <div class="container-fluid">
                        <div class="row">
                            <h3>Каталог сировини</h3>
                            @foreach($materials as $product)
                            <div class="col-6 col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">
                                            <a  class=" font-weight-bold text-dark text-uppercase small">{{$product->title}}</a>
                                        </h4>
{{--                                        <h5 class="card-price small">--}}
{{--                                            {{$product->description}}--}}
{{--                                        </h5>--}}
                                        <p>{{$product->price}}грн/1кг</p>
                                    </div>
                                    @if($product->ordered)
                                        <button type="button" id="add-button-{{$product->id}}" class="btn btn-success" disabled>Вже додано</button>
                                    @else
                                        <button type="button" id="add-button-{{$product->id}}" onclick="addProduct('{{$product->id}}');" class="btn btn-success">Додати до замовлення</button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function addProduct(productId)
            {
                $.ajax({
                    url: '{{ route('material.add') }}',
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
                        alert('Сировину додано до замовлення');
                    }
                });
            }
        </script>
@endsection

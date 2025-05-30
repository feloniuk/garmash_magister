@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200&display=swap');

        body {
            font-family: 'Manrope', sans-serif;
            background:#eee;
        }

        .size span {
            font-size: 11px;
        }

        .color span {
            font-size: 11px;
        }

        .product-deta {
            margin-right: 70px;
        }

        .gift-card:focus {
            box-shadow: none;
        }

        .pay-button {
            color: #fff;
        }

        .pay-button:hover {
            color: #fff;
        }

        .pay-button:focus {
            color: #fff;
            box-shadow: none;
        }

        .text-grey {
            color: #a39f9f;
        }

        .qty i {
            font-size: 11px;
        }
        .amount-button {
            font-size: 35px;
            cursor: pointer;
            margin-left: 15px;
        }
    </style>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-center row">
            @if($order)
                <div class="col-md-8">
                    <div class="p-2">
                        <h4>Замовлення</h4>
                    </div>
                    <div class="alert alert-success" role="alert" id="order-alert" style="display: none;">
                    </div>
                    @foreach($order as $product)
                        <div class="d-flex flex-row justify-content-between align-items-center p-2 bg-white mt-4 px-3 rounded">
                            <div class="d-flex flex-column align-items-center product-details">
                                <span class="font-weight-bold">{{ $product['name'] }} / 100г</span>
                            </div>
                            <div class="d-flex flex-row align-items-center qty">
                                <h5 class="text-grey mt-1 mr-1 ml-1" id="amount-product-{{$product['id']}}">{{ $product['amount'] }}</h5>
                            </div>
                            <div>
                                <span class="amount-button" onclick="changeAmount('{{ $product['id'] }}', '+')">+</span>
                                <span class="amount-button" onclick="changeAmount('{{ $product['id'] }}', '-')">-</span>
                            </div>
                            <div>
                                <h5 class="text-grey" id="sum-product-{{$product['id']}}">{{ round($product['price'] * $product['amount'], 2) }}грн</h5>
                            </div>
                            <div class="d-flex align-items-center"><i class="fa fa-trash mb-1 text-danger" style="cursor: pointer;" onclick="removeProduct('{{$product['id']}}')"></i></div>
                        </div>
                    @endforeach
                    <div class="p-2 d-flex justify-content-between">
                        <h4>Загальна вартість:</h4>
                        <div style="color: green; font-size: 30px;font-weight: bold;" id="full-price">{{$fullPrice}}грн</div>
                    </div>

                    <div class="d-flex flex-row align-items-center mt-3 p-2 bg-white rounded"><button class="btn btn-warning btn-block btn-lg ml-2 pay-button" onclick="makeOrder();" id="order-btn" type="button"
                        style="background-color: #70d970"
                        >Відправити замовлення</button></div>
                </div>
            @else
                <h3>Ваше замовлення порожнє :(</h3>
            @endif
        </div>
    </div>
@endsection
<script>
    function changeAmount(productId, operation)
    {
        $.ajax({
            url: '{{ route('material.change-amount') }}',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {productId: productId, operation: operation},
            dataType: 'json',
            success: function(data){
                if (data['error']) {
                    alert(data['error']);
                    return;
                }
                if (data['redirect']) {
                    window.location.href = "{{ route('material.cart') }}";
                }
                $('#sum-product-' + data.productId).html(data.sum + 'грн');
                $('#amount-product-' + data.productId).html(data.amount);
                $('#full-price').html(data.fullPrice + 'грн');
            }
        });
    }

    function removeProduct(productId)
    {
        $.ajax({
            url: '{{ route('material.remove') }}',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {productId: productId},
            dataType: 'json',
            success: function(data){
                window.location.href = "{{ route('material.cart') }}";
            }
        });
    }

    function makeOrder()
    {
        $.ajax({
            url: '{{ route('material.create-order') }}',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(data){
                if (data['success']) {
                    let alertBlock = $('#order-alert');
                    $('#order-btn').prop('disabled', true);
                    alertBlock.css('display', 'block')
                    alertBlock.html('Ваше замовлення успішно створене!');
                } else {
                    alert('some error')
                }
            }
        });
    }
</script>


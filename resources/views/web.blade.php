@extends('layouts.app')

@section('content')
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-md-8 order-md-2 col-lg-9">
                    <div class="container-fluid">
                        <div class="row mb-5">
                            <h3>Про нас</h3>
                            <div>
                                «ФІРМА ГАРМАШ» - це сучасне, стабільне, відоме на ринку м'ясопродуктів підприємство, лідер на ринку Одеси та Одеської обл. у виробництві ковбасних виробів, пельменів та напівфабрикатів.
                            </div>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-between align-items-center w-100 mb-3">
                                <h3>Каталог товарів</h3>
                                @auth
                                    @if(Auth::user()->role !== 'user')
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                                            <i class="fas fa-cog"></i> Управління продукцією
                                        </a>
                                    @endif
                                @endauth
                            </div>
                            @foreach($data as $product)
                            <div class="col-6 col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-img-top position-relative">
                                        <a href="{{ route('product.show', $product->id) }}">
                                            <img src="./images/products/{{$product->image}}" 
                                                 class="img-fluid mx-auto d-block" 
                                                 alt="{{$product->title}}"
                                                 style="height: 200px; object-fit: cover; width: 100%;">
                                        </a>
                                        @if($product->quantity <= 5)
                                            <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">
                                                Закінчується
                                            </span>
                                        @endif
                                    </div>
                                    <div class="card-body text-center d-flex flex-column">
                                        <h4 class="card-title">
                                            <a href="{{ route('product.show', $product->id) }}" 
                                               class="font-weight-bold text-dark text-uppercase small text-decoration-none">
                                                {{$product->title}}
                                            </a>
                                        </h4>
                                        <h5 class="card-price small text-muted flex-grow-1">
                                            {{$product->description}}
                                        </h5>
                                        
                                        @if($product->composition && count($product->composition) > 0)
                                            <div class="mb-2">
                                                <small class="text-muted">Основний склад:</small>
                                                <br>
                                                @php
                                                    $mainIngredients = collect($product->composition)
                                                        ->filter(function($percentage) { return $percentage > 0; })
                                                        ->sortDesc()
                                                        ->take(2);
                                                @endphp
                                                <small class="text-info">
                                                    @foreach($mainIngredients as $ingredient => $percentage)
                                                        {{ ucfirst($ingredient) }}: {{ $percentage }}%{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </small>
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="h5 text-success mb-0">{{$product->price}}грн/100г</span>
                                            @if($product->code)
                                                <small class="text-muted">Код: {{$product->code}}</small>
                                            @endif
                                        </div>

                                        <div class="mt-auto">
                                            @auth
                                                @if(Auth::user()->role == 'user')
                                                    @if($product->ordered)
                                                        <button type="button" id="add-button-{{$product->id}}" class="btn btn-success btn-block" disabled>
                                                            <i class="fas fa-check"></i> Вже додано
                                                        </button>
                                                    @else
                                                        <button type="button" id="add-button-{{$product->id}}" 
                                                                onclick="addProduct('{{$product->id}}');" 
                                                                class="btn btn-primary btn-block">
                                                            <i class="fas fa-cart-plus"></i> Додати до замовлення
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-block">
                                                        <i class="fas fa-eye"></i> Детальніше
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-block">
                                                    <i class="fas fa-eye"></i> Детальніше
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
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
                        btn.html('<i class="fas fa-check"></i> Вже додано');
                        btn.removeClass('btn-primary').addClass('btn-success');
                        alert('Продукт додано до замовлення');
                    }
                });
            }
        </script>
@endsection
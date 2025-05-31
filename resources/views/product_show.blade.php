@extends('layouts.app')

@section('content')
<div class="container pt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    @if($product->image)
                        <img src="{{ asset('images/products/' . $product->image) }}" 
                             alt="{{ $product->title }}" 
                             class="img-fluid rounded mb-3"
                             style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3" 
                             style="height: 300px;">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ $product->title }}</h3>
                        <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }} badge-lg">
                            {{ $product->is_active ? 'Активний' : 'Неактивний' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Код продукту:</strong>
                            <br><code>{{ $product->code ?? 'Не вказано' }}</code>
                        </div>
                        <div class="col-6">
                            <strong>Ціна:</strong>
                            <br><span class="h4 text-success">{{ $product->price }} грн</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Кількість на складі:</strong>
                            <br>
                            <span class="badge {{ $product->quantity <= 5 ? 'badge-danger' : 'badge-success' }} badge-lg">
                                {{ $product->quantity }} шт
                            </span>
                        </div>
                        <div class="col-6">
                            <strong>Термін зберігання:</strong>
                            <br>{{ $product->termin }} днів
                        </div>
                    </div>

                    @if($product->description)
                        <div class="mb-3">
                            <strong>Короткий опис:</strong>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>
                    @endif

                    @if($product->full_description)
                        <div class="mb-3">
                            <strong>Повний опис:</strong>
                            <p>{{ $product->full_description }}</p>
                        </div>
                    @endif

                    @auth
                        @if(Auth::user()->role == 'user')
                            <div class="mt-4">
                                @if($product->ordered ?? false)
                                    <button class="btn btn-success btn-lg btn-block" disabled>
                                        <i class="fas fa-check"></i> Вже додано до замовлення
                                    </button>
                                @else
                                    <button class="btn btn-primary btn-lg btn-block" 
                                            onclick="addProduct('{{ $product->id }}');" 
                                            id="add-button-{{ $product->id }}">
                                        <i class="fas fa-cart-plus"></i> Додати до замовлення
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Composition Card -->
    @if($product->composition && count($product->composition) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ul"></i> Склад продукту
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($product->composition as $ingredient => $percentage)
                                @if($percentage > 0)
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="progress-container">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-capitalize">{{ $ingredient }}</span>
                                                <strong>{{ $percentage }}%</strong>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Admin Actions -->
    @auth
        @if(Auth::user()->role !== 'user')
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Дії адміністратора</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Редагувати
                            </a>
                            
                            <form action="{{ route('admin.products.toggle-status', $product->id) }}" 
                                  method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="btn {{ $product->is_active ? 'btn-secondary' : 'btn-success' }}">
                                    <i class="fas fa-power-off"></i>
                                    {{ $product->is_active ? 'Деактивувати' : 'Активувати' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                  method="POST" style="display: inline;"
                                  onsubmit="return confirm('Ви впевнені що хочете видалити цей продукт?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Видалити
                                </button>
                            </form>

                            <a href="{{ route('admin.products.index') }}" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Повернутися до списку
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>

<script>
function addProduct(productId) {
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
            btn.html('<i class="fas fa-check"></i> Вже додано до замовлення');
            btn.removeClass('btn-primary').addClass('btn-success');
            alert('Продукт додано до замовлення');
        }
    });
}
</script>
@endsection
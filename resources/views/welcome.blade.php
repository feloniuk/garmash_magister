@extends('layouts.app')

@section('content')
<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image {
    height: 220px;
    object-fit: cover;
    width: 100%;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.composition-chart {
    height: 60px;
    margin: 10px 0;
}

.composition-bar {
    height: 8px;
    border-radius: 4px;
    background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 25%, #45B7D1 50%, #96CEB4 75%, #FFEAA7 100%);
    position: relative;
    overflow: hidden;
}

.composition-labels {
    font-size: 11px;
    margin-top: 5px;
}

.price-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 8px 15px;
    font-weight: bold;
    font-size: 16px;
}

.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.btn-view-product {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-view-product:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    text-decoration: none;
}

.btn-add-cart {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-add-cart:hover {
    background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.composition-tooltip {
    position: relative;
    cursor: help;
}

.composition-tooltip:hover .tooltip-content {
    visibility: visible;
    opacity: 1;
}

.tooltip-content {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    transition: opacity 0.3s;
}

.tooltip-content::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

.about-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-title-link {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title-link:hover {
    color: #667eea;
    text-decoration: none;
}
</style>

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 order-md-2 col-lg-9">
            <div class="container-fluid">
                <!-- About Section -->
                <div class="about-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-3">
                                <i class="fas fa-industry"></i> Про нас
                            </h3>
                            <p class="mb-0" style="font-size: 16px; line-height: 1.6;">
                                «ФІРМА ГАРМАШ» - це сучасне, стабільне, відоме на ринку м'ясопродуктів підприємство, 
                                лідер на ринку Одеси та Одеської обл. у виробництві ковбасних виробів, пельменів та напівфабрикатів.
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-award fa-3x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="mb-2">
                                    <i class="fas fa-shopping-basket"></i> Каталог товарів
                                </h3>
                                <p class="text-muted mb-0">Високоякісні м'ясні продукти власного виробництва</p>
                            </div>
                            @auth
                                @if(Auth::user()->role !== 'user')
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                                        <i class="fas fa-cog"></i> Управління продукцією
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    @forelse($data as $product)
                    <div class="col-6 col-md-6 col-lg-4 mb-4">
                        <div class="card product-card h-100">
                            <!-- Product Image -->
                            <div class="position-relative" style="overflow: hidden; border-radius: 15px 15px 0 0;">
                                <a href="{{ route('product.show', $product->id) }}">
                                    @if($product->image && file_exists(public_path('images/products/' . $product->image)))
                                        <img src="{{ asset('images/products/' . $product->image) }}" 
                                             class="product-image" 
                                             alt="{{$product->title}}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center product-image bg-light">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </a>
                                
                                <!-- Status badges -->
                                @if($product->quantity <= 5 && $product->quantity > 0)
                                    <span class="badge badge-warning status-badge">
                                        <i class="fas fa-exclamation-triangle"></i> Закінчується
                                    </span>
                                @elseif($product->quantity <= 0)
                                    <span class="badge badge-danger status-badge">
                                        <i class="fas fa-times"></i> Немає в наявності
                                    </span>
                                @elseif(!($product->is_active ?? true))
                                    <span class="badge badge-secondary status-badge">
                                        <i class="fas fa-pause"></i> Неактивний
                                    </span>
                                @endif

                                <!-- Price overlay -->
                                <div class="position-absolute" style="bottom: 10px; left: 10px;">
                                    <span class="price-badge">
                                        {{$product->price}} грн/100г
                                    </span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <!-- Product Title and Description -->
                                <div class="text-center mb-3">
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('product.show', $product->id) }}" 
                                           class="product-title-link font-weight-bold">
                                            {{$product->title}}
                                        </a>
                                    </h5>
                                    @if($product->description)
                                        <p class="text-muted small mb-2">
                                            {{$product->description}}
                                        </p>
                                    @endif
                                    @if($product->code)
                                        <small class="text-info">Код: <code>{{$product->code}}</code></small>
                                    @endif
                                </div>

                                <!-- Product Composition -->
                                @if($product->composition && is_array($product->composition) && count(array_filter($product->composition, function($v) { return $v > 0; })) > 0)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted font-weight-bold">
                                                <i class="fas fa-list-ul"></i> Склад продукту:
                                            </small>
                                        </div>
                                        
                                        <!-- Composition Visualization -->
                                        <div class="composition-chart">
                                            @php
                                                $filteredComposition = array_filter($product->composition, function($v) { return $v > 0; });
                                                $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8'];
                                                $total = array_sum($filteredComposition);
                                                $currentPosition = 0;
                                            @endphp
                                            
                                            @if($total > 0)
                                                <div class="position-relative">
                                                    <div class="composition-bar">
                                                        @foreach($filteredComposition as $ingredient => $percentage)
                                                            @php
                                                                $width = ($percentage / $total) * 100;
                                                                $color = $colors[array_search($ingredient, array_keys($filteredComposition)) % count($colors)];
                                                            @endphp
                                                            <div class="position-absolute h-100 composition-tooltip" 
                                                                 style="left: {{$currentPosition}}%; width: {{$width}}%; background-color: {{$color}};">
                                                                <div class="tooltip-content">
                                                                    {{ucfirst($ingredient)}}: {{$percentage}}%
                                                                </div>
                                                            </div>
                                                            @php $currentPosition += $width; @endphp
                                                        @endforeach
                                                    </div>
                                                    
                                                    <!-- Composition Labels -->
                                                    <div class="composition-labels">
                                                        @php $displayedIngredients = array_slice($filteredComposition, 0, 3, true); @endphp
                                                        @foreach($displayedIngredients as $ingredient => $percentage)
                                                            @php $color = $colors[array_search($ingredient, array_keys($filteredComposition)) % count($colors)]; @endphp
                                                            <span class="d-inline-block mr-2 mb-1">
                                                                <span class="d-inline-block rounded-circle mr-1" 
                                                                      style="width: 8px; height: 8px; background-color: {{$color}};"></span>
                                                                <small>{{ucfirst($ingredient)}} {{$percentage}}%</small>
                                                            </span>
                                                        @endforeach
                                                        @if(count($filteredComposition) > 3)
                                                            <small class="text-muted">+{{count($filteredComposition) - 3}} ще</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center text-muted">
                                                    <small>Склад не вказано</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Product Details -->
                                <div class="row text-center mb-3 small">
                                    <div class="col-6">
                                        <div class="border-right">
                                            <div class="text-muted">Термін зберігання</div>
                                            <strong>{{$product->termin}} днів</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted">На складі</div>
                                        <strong class="{{ $product->quantity <= 5 ? ($product->quantity <= 0 ? 'text-danger' : 'text-warning') : 'text-success' }}">
                                            {{$product->quantity}} кг
                                        </strong>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <a href="{{ route('product.show', $product->id) }}" 
                                               class="btn btn-view-product btn-block">
                                                <i class="fas fa-eye"></i> Детальніше
                                            </a>
                                        </div>
                                        @auth
                                            @if(Auth::user()->role == 'user' && ($product->is_active ?? true))
                                                <div class="col-12">
                                                    @if($product->ordered ?? false)
                                                        <button type="button" 
                                                                id="add-button-{{$product->id}}" 
                                                                class="btn btn-success btn-block" 
                                                                disabled>
                                                            <i class="fas fa-check"></i> Вже в замовленні
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                id="add-button-{{$product->id}}" 
                                                                onclick="addProduct('{{$product->id}}');" 
                                                                class="btn btn-add-cart btn-block"
                                                                {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                            <i class="fas fa-cart-plus"></i> 
                                                            {{ $product->quantity <= 0 ? 'Немає в наявності' : 'Додати до замовлення' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
                            <h4 class="text-muted">Продукти тимчасово відсутні</h4>
                            <p class="text-muted">Найближчим часом каталог буде поповнено новими товарами</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Additional Info Section -->
                @if($data->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle text-primary"></i> 
                                    Додаткова інформація
                                </h5>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <i class="fas fa-truck text-success"></i>
                                        <strong>Доставка</strong><br>
                                        <small class="text-muted">По Одесі та області</small>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <i class="fas fa-certificate text-warning"></i>
                                        <strong>Якість</strong><br>
                                        <small class="text-muted">Сертифіковані продукти</small>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <i class="fas fa-clock text-info"></i>
                                        <strong>Свіжість</strong><br>
                                        <small class="text-muted">Щоденне виробництво</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function addProduct(productId) {
    const button = document.getElementById('add-button-' + productId);
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Додавання...';
    button.disabled = true;
    
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
                // Show error
                button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Помилка';
                button.classList.remove('btn-add-cart');
                button.classList.add('btn-danger');
                
                // Show error message
                showNotification('error', data['error']);
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-add-cart');
                    button.disabled = false;
                }, 3000);
                return;
            }
            
            // Success
            button.innerHTML = '<i class="fas fa-check"></i> Вже в замовленні';
            button.classList.remove('btn-add-cart');
            button.classList.add('btn-success');
            button.disabled = true;
            
            // Show success message
            showNotification('success', 'Продукт успішно додано до замовлення!');
        },
        error: function() {
            // Network error
            button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Помилка мережі';
            button.classList.remove('btn-add-cart');
            button.classList.add('btn-danger');
            
            showNotification('error', 'Помилка мережі. Спробуйте пізніше.');
            
            // Reset button after 3 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-danger');
                button.classList.add('btn-add-cart');
                button.disabled = false;
            }, 3000);
        }
    });
}

function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// Initialize tooltips and effects
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects for composition charts
    const compositionBars = document.querySelectorAll('.composition-bar > div');
    compositionBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            this.style.opacity = '0.8';
            this.style.transform = 'scale(1.02)';
        });
        
        bar.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
            this.style.transform = 'scale(1)';
        });
    });
    
    // Add click tracking for product links
    const productLinks = document.querySelectorAll('.product-title-link, .btn-view-product');
    productLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('Product link clicked:', this.href);
        });
    });
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        {{ isset($product) ? 'Редагувати продукт' : 'Створити новий продукт' }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Назва продукту *</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" 
                                           value="{{ old('title', $product->title ?? '') }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Короткий опис</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" 
                                              rows="2">{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="full_description">Повний опис</label>
                                    <textarea class="form-control @error('full_description') is-invalid @enderror" 
                                              id="full_description" name="full_description" 
                                              rows="4">{{ old('full_description', $product->full_description ?? '') }}</textarea>
                                    @error('full_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Composition Section -->
                                <div class="form-group">
                                    <label>Склад продукту (%)</label>
                                    <div class="row" id="composition-container">
                                        @php
                                            $meatTypes = ['яловичина', 'свинина', 'курятина', 'індичатина', 'сало', 'спеції', 'інше'];
                                        @endphp
                                        @foreach($meatTypes as $meatType)
                                            <div class="col-md-6 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ ucfirst($meatType) }}</span>
                                                    </div>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           name="composition[{{ $meatType }}]" 
                                                           value="{{ old('composition.' . $meatType, (isset($product) && $product->composition) ? ($product->composition[$meatType] ?? 0) : 0) }}" 
                                                           min="0" max="100" step="0.1">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="form-text text-muted">
                                        Вкажіть відсоткове співвідношення складових продукту
                                    </small>
                                </div>
                            </div>

                            <!-- Product Details & Image -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="image">Фото продукту</label>
                                    @if(isset($product) && $product->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('images/products/' . $product->image) }}" 
                                                 alt="Current image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control-file @error('image') is-invalid @enderror" 
                                           id="image" name="image" 
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="code">Код продукту *</label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" 
                                           value="{{ old('code', $product->code ?? '') }}" 
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="price">Ціна (грн) *</label>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" 
                                           value="{{ old('price', $product->price ?? '') }}" 
                                           min="0" step="0.01" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="quantity">Кількість (шт) *</label>
                                    <input type="number" 
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" 
                                           value="{{ old('quantity', $product->quantity ?? '') }}" 
                                           min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="termin">Термін зберігання (днів) *</label>
                                    <input type="number" 
                                           class="form-control @error('termin') is-invalid @enderror" 
                                           id="termin" name="termin" 
                                           value="{{ old('termin', $product->termin ?? '') }}" 
                                           min="1" required>
                                    @error('termin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_active" name="is_active" 
                                           value="1" 
                                           {{ old('is_active', ($product->is_active ?? true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Активний продукт
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> 
                                {{ isset($product) ? 'Оновити продукт' : 'Створити продукт' }}
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Повернутися до списку
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate total percentage
    const compositionInputs = document.querySelectorAll('input[name^="composition"]');
    
    function updateTotal() {
        let total = 0;
        compositionInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        const totalElement = document.getElementById('total-percentage');
        if (totalElement) {
            totalElement.textContent = total.toFixed(1) + '%';
            totalElement.className = total > 100 ? 'text-danger' : 'text-success';
        }
    }
    
    compositionInputs.forEach(input => {
        input.addEventListener('input', updateTotal);
    });
    
    // Add total display
    const container = document.getElementById('composition-container');
    if (container) {
        const totalDiv = document.createElement('div');
        totalDiv.className = 'col-12 mt-2';
        totalDiv.innerHTML = '<strong>Загальний відсоток: <span id="total-percentage">0%</span></strong>';
        container.appendChild(totalDiv);
        updateTotal();
    }
});
</script>
@endsection
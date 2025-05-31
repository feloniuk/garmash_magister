@extends('layouts.app')

@section('content')
<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Управління продукцією</h3>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Додати новий продукт
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Список продуктів</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Фото</th>
                                    <th>Назва</th>
                                    <th>Код</th>
                                    <th>Ціна</th>
                                    <th>Кількість</th>
                                    <th>Термін</th>
                                    <th>Статус</th>
                                    <th>Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('images/products/' . $product->image) }}" 
                                                     alt="{{ $product->title }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        </td>
                                        <td><code>{{ $product->code ?? 'Не вказано' }}</code></td>
                                        <td><strong>{{ $product->price }} грн</strong></td>
                                        <td>
                                            <span class="badge {{ $product->quantity <= 5 ? 'badge-danger' : 'badge-success' }}">
                                                {{ $product->quantity }} шт
                                            </span>
                                        </td>
                                        <td>{{ $product->termin }} днів</td>
                                        <td>
                                            <form action="{{ route('admin.products.toggle-status', $product->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $product->is_active ? 'Активний' : 'Неактивний' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                                   class="btn btn-sm btn-info" title="Переглянути">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                                   class="btn btn-sm btn-warning" title="Редагувати">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                                      method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Ви впевнені що хочете видалити цей продукт?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Видалити">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted mb-0">Продукти не знайдено</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($products->hasPages())
                    <div class="card-footer">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
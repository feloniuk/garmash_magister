<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="http://garmash/images/logo.jpg" width="100px">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @if(Auth::user()->role == 'user')
                            <li class="nav-item">
                                <a class="nav-link @if(request()->route()->getName() == 'product.cart') active @endif" href="{{ route('product.cart') }}">Моє замовлення</a>
                            </li>
                            @endif
                            @if(Auth::user()->role != 'user')
                            <li class="nav-item">
                                <a class="nav-link @if(request()->route()->getName() == 'users') active @endif" href="{{ route('users') }}">Корисутвачі</a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link @if(request()->route()->getName() == 'home') active @endif" href="{{ route('home') }}">Домашня сторінка</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->role == 'user')
                                        <a class="dropdown-item" href="{{ route('profile') }}">
                                            Мій Профіль
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Вихід
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="row">
                @auth
                    @if(Auth::user()->role == 'manager')
                    <div class="col-2">
                        <div class="d-flex flex-column">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;" data-toggle="modal" data-target="#exampleModalLong">
                                Клієнти
                            </button>
                            <a href="{{route('orders')}}" type="button" class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;">
                                Замовлення
                            </a>
                            <a href="{{route('admin.products.index')}}" type="button" class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;">
                               Управління продукцією
                            </a>
                            <a href="{{route('products')}}" type="button" class="btn btn-secondary mb-2" style="width: 150px; margin-left: 20px;">
                               Пошук продукції
                            </a>
                            <a href="{{route('order.forecast')}}" type="button" class="btn btn-info mb-2" style="width: 150px; margin-left: 20px;">
                               Прогнозування
                            </a>
                            <a href="{{route('admin.analytics.index')}}" type="button" class="btn btn-warning mb-2" style="width: 150px; margin-left: 20px;">
                               Аналітика
                            </a>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Клієнти</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Ім'я</th>
                                                <th scope="col">ФОП</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Телефон</th>
                                                <th scope="col">Адреса</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($clients ?? [] as $user)
                                                <tr>
                                                    <th scope="row">{{ ++$loop->index }}</th>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->client_name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->tel }}</td>
                                                    <td>{{ $user->address }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @elseif(Auth::user()->role == 'sklad')
                        <div class="col-2">
                            <div class="d-flex flex-column">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;" data-toggle="modal" data-target="#exampleModalLong">
                                    Продукція
                                </button>
                                <a href="{{ route('materials') }}" style="width: 150px; margin-left: 20px;" class="btn btn-primary mb-2">
                                    Сировина
                                </a>
                                <a class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;" href="{{ route('material.cart') }}">
                                    Замовлення сировини
                                </a>
                                <a class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;" href="{{ route('web') }}">
                                    Відео
                                </a>
                                <a href="{{route('products')}}" type="button" class="btn btn-primary mb-2" style="width: 150px; margin-left: 20px;">
                                    Пошук продукції
                                </a>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade bd-example-modal-lg" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Продукція</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Назва</th>
                                                    <th scope="col">Опис</th>
                                                    <th scope="col">Ціна</th>
                                                    <th scope="col">Термін реалізації</th>
                                                    <th scope="col">Кількість</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data ?? [] as $product)
                                                    <tr>
                                                        <th scope="row">{{ ++$loop->index }}</th>
                                                        <td>{{ $product->title }}</td>
                                                        <td>{{ $product->description }}</td>
                                                        <td>{{ $product->price }}грн</td>
                                                        <td>{{ $product->termin }} днів</td>
                                                        <td>{{ $product->quantity }} кг</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                @endauth
                <div class="col">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/process_ajax.js') }}" type="text/javascript"></script>
</body>
</html>
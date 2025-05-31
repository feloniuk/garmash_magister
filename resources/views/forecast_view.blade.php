@extends('layouts.app')

@section('content')
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 order-md-2 col-lg-9">
                <div class="container">
                    <form id="prediction-form">
                        <div class="row">
                            <h3>Прогнозування для продукту: {{ $product->title }}</h3>
                        </div>

                        <input type="hidden" id="sales_data" value='@json($sales)'>

                        <div class=" mt-4">
                            <h3>Налаштування прогнозу</h3>
                            <div class="mt-5">
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="forecast-period">Період прогнозу (1-5):</label>
                                        <input type="number" id="forecast-period" name="forecast_period"
                                               class="form-control" min="1" max="5" step="1" value="1" required>
                                    </div>

                                    <div class="form-group col">
                                        <label for="param1">Порядок авторегресії (0-3):</label>
                                        <input type="number" id="param1" name="param1" class="form-control" min="0"
                                               max="3" step="1" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col">
                                        <label for="param2">Прорядок деференціювання (0-3):</label>
                                        <input type="number" id="param2" name="param2" class="form-control" min="0"
                                               max="3" step="1" required>
                                    </div>

                                    <div class="form-group col">
                                        <label for="param3">Порядок ковзаючого середнього (0-3):</label>
                                        <input type="number" id="param3" name="param3" class="form-control" min="0"
                                               max="3" step="1" required>
                                    </div>
                                </div>

                                @if($warning)
                                    <p style="color: red">{{ $warning }}</p>
                                @else
                                    <button type="submit" class="btn btn-primary">Прогнозувати</button>
                                @endif
                            </div>
                        </div>
                    </form>
                    <br>
                    <figure class="highcharts-figure">
                        <div id="forecast-chart-container"></div>
                    </figure>
                </div>
            </div>
        </div>
    </div>
@endsection

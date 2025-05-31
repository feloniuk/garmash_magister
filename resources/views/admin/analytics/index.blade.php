@extends('layouts.app')

@section('content')
<div class="container-fluid pt-5">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-chart-line"></i> Аналітика та кластеризація даних
            </h3>

            <!-- Filter Controls -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Налаштування аналізу</h5>
                </div>
                <div class="card-body">
                    <form id="analytics-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="criteria">Критерій кластеризації</label>
                                    <select class="form-control" id="criteria" name="criteria">
                                        <option value="products">За продуктами</option>
                                        <option value="prices">За ціновими категоріями</option>
                                        <option value="materials">За сировиною</option>
                                        <option value="regions">За регіонами</option>
                                        {{-- <option value="time">За часовими періодами</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">Дата від</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">Дата до</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Статус замовлень</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="apply">Прийняті</option>
                                        <option value="active">Нові</option>
                                        <option value="reject">Відхилені</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary" onclick="loadAnalytics()">
                                            <i class="fas fa-sync-alt"></i> Оновити аналіз
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="exportData()">
                                            <i class="fas fa-download"></i> Експорт
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loading" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Завантаження...</span>
                </div>
                <p class="mt-2">Аналіз даних...</p>
            </div>

            <!-- Analytics Results -->
            <div id="analytics-results" style="display: none;">
                <!-- Summary Cards -->
                <div class="row mb-4" id="summary-cards">
                    <!-- Cards will be populated by JavaScript -->
                </div>

                <!-- Main Visualization Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-cube"></i> 
                                    <span id="chart-title">Кластеризація даних</span>
                                </h5>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" id="btn-3d" onclick="show3DChart()">
                                        <i class="fas fa-cube"></i> 3D Вигляд
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-2d" onclick="show2DChart()">
                                        <i class="fas fa-chart-bar"></i> 2D Вигляд
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- 3D Chart Container -->
                                <div id="chart3d-container" style="height: 500px; position: relative;">
                                    <div id="chart3d" style="width: 100%; height: 100%;"></div>
                                    <!-- 3D Chart Controls -->
                                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 1000;">
                                        <div class="btn-group-vertical">
                                            <button class="btn btn-sm btn-light" onclick="resetCamera()" title="Скинути камеру">
                                                <i class="fas fa-home"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light" onclick="toggleRotation()" id="rotation-btn" title="Авто-обертання">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 2D Chart Container -->
                                <div id="chart2d-container" style="display: none; height: 500px;">
                                    <canvas id="clusterChart"></canvas>
                                </div>

                                <!-- Chart Legend -->
                                <div class="mt-3" id="chart-legend">
                                    <!-- Legend will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side-by-side charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-pie"></i> 
                                    Розподіл за категоріями
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="pieChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-area"></i> 
                                    Тренди продажів
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="trendChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Results Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-table"></i>
                                    Детальні результати кластеризації
                                </h5>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="toggleView('table')">
                                        <i class="fas fa-table"></i> Таблиця
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleView('cards')">
                                        <i class="fas fa-th"></i> Картки
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="results-table-view">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="results-table">
                                            <thead id="table-header">
                                                <!-- Table headers will be populated by JavaScript -->
                                            </thead>
                                            <tbody id="table-body">
                                                <!-- Table data will be populated by JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="results-cards-view" style="display: none;">
                                    <div class="row" id="results-cards">
                                        <!-- Cards will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Plotly.js for 3D charts -->
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

<script>
let clusterChart = null;
let pieChart = null;
let trendChart = null;
let currentData = null;
let rotationAnimation = null;
let isRotating = false;
let currentView = '3d'; // Track current view

// Load analytics on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range (last 30 days)
    setDefaultDateRange();
    loadAnalytics();
});

// Set default date range to last 30 days
function setDefaultDateRange() {
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    document.getElementById('date_to').value = today.toISOString().split('T')[0];
    document.getElementById('date_from').value = thirtyDaysAgo.toISOString().split('T')[0];
}

// Load analytics data
function loadAnalytics() {
    const formData = new FormData(document.getElementById('analytics-form'));
    const params = new URLSearchParams(formData);
    
    document.getElementById('loading').style.display = 'block';
    document.getElementById('analytics-results').style.display = 'none';

    fetch(`{{ route('admin.analytics.data') }}?${params}`)
        .then(response => response.json())
        .then(data => {
            currentData = data;
            displayResults(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Помилка при завантаженні даних аналітики');
        })
        .finally(() => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('analytics-results').style.display = 'block';
        });
}

// Display analytics results
function displayResults(data) {
    displaySummaryCards(data);
    if (currentView === '3d') {
        display3DChart(data);
    } else {
        display2DChart(data);
    }
    display2DCharts(data);
    displayDetailedResults(data);
}

// Display summary cards
function displaySummaryCards(data) {
    const container = document.getElementById('summary-cards');
    const criteria = document.getElementById('criteria').value;
    let cards = '';
    
    // Определяем тип данных и показываем соответствующие карточки
    if (criteria === 'materials') {
        // Для материалов показываем специфичные метрики
        const materialsCount = Object.keys(data.clusters).length;
        const totalOrdersProcessed = data.summary?.total_orders_processed || 0;
        
        // Найдем самый популярный материал
        let topMaterial = { name: 'N/A', percentage: 0 };
        Object.values(data.clusters).forEach(material => {
            if (material.average_percentage > topMaterial.percentage) {
                topMaterial = { name: material.name, percentage: material.average_percentage };
            }
        });

        // Найдем самый часто используемый материал
        let mostUsedMaterial = { name: 'N/A', usage: 0 };
        Object.values(data.clusters).forEach(material => {
            if (material.usage_percentage > mostUsedMaterial.usage) {
                mostUsedMaterial = { name: material.name, usage: material.usage_percentage };
            }
        });

        cards = `
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${materialsCount}</h4>
                                <p class="mb-0">Видів сировини</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-seedling fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalOrdersProcessed}</h4>
                                <p class="mb-0">Замовлень проаналізовано</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 style="font-size: 14px;">${topMaterial.name}</h6>
                                <h5>${topMaterial.percentage}%</h5>
                                <p class="mb-0">Найбільший % в продуктах</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-trophy fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 style="font-size: 14px;">${mostUsedMaterial.name}</h6>
                                <h5>${mostUsedMaterial.usage}%</h5>
                                <p class="mb-0">Найчастіше в замовленнях</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (criteria === 'prices') {
        // Для цен показываем ценовые метрики
        const totalOrders = Object.values(data.clusters).reduce((sum, cluster) => sum + cluster.count, 0);
        const avgOrderValue = Object.values(data.clusters).reduce((sum, cluster) => {
            const clusterAvg = cluster.orders.length > 0 
                ? cluster.orders.reduce((orderSum, order) => orderSum + order.total, 0) / cluster.orders.length 
                : 0;
            return sum + (clusterAvg * cluster.count);
        }, 0) / (totalOrders || 1);

        // Найдем самый популярный ценовой диапазон
        let topPriceRange = { range: 'N/A', count: 0 };
        Object.entries(data.clusters).forEach(([range, cluster]) => {
            if (cluster.count > topPriceRange.count) {
                topPriceRange = { range: range, count: cluster.count };
            }
        });

        cards = `
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalOrders}</h4>
                                <p class="mb-0">Всього замовлень</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${Math.round(avgOrderValue)} грн</h4>
                                <p class="mb-0">Середня вартість</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hryvnia-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5>${topPriceRange.range} грн</h5>
                                <p class="mb-0">Популярний діапазон</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${Object.keys(data.clusters).length}</h4>
                                <p class="mb-0">Цінових категорій</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tags fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (criteria === 'regions') {
        // Для регионов показываем региональные метрики
        const totalRegions = Object.keys(data.clusters).length;
        const totalRevenue = Object.values(data.clusters).reduce((sum, region) => sum + region.revenue, 0);
        const totalOrders = Object.values(data.clusters).reduce((sum, region) => sum + region.orders_count, 0);
        const totalCustomers = Object.values(data.clusters).reduce((sum, region) => sum + region.customers_count, 0);

        cards = `
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalRegions}</h4>
                                <p class="mb-0">Регіонів</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${Math.round(totalRevenue)} грн</h4>
                                <p class="mb-0">Загальна виручка</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hryvnia-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalOrders}</h4>
                                <p class="mb-0">Замовлень</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-bag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalCustomers}</h4>
                                <p class="mb-0">Клієнтів</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (criteria === 'time') {
        // Для времени показываем временные метрики
        const monthlyData = data.clusters.monthly || {};
        const weeklyData = data.clusters.weekly || {};
        const totalMonths = Object.keys(monthlyData).length;
        const totalRevenue = Object.values(monthlyData).reduce((sum, month) => sum + month.revenue, 0);
        const totalOrders = Object.values(monthlyData).reduce((sum, month) => sum + month.orders, 0);
        
        // Найдем самый активный месяц
        let topMonth = { month: 'N/A', orders: 0 };
        Object.entries(monthlyData).forEach(([month, data]) => {
            if (data.orders > topMonth.orders) {
                topMonth = { month: month, orders: data.orders };
            }
        });

        cards = `
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalMonths}</h4>
                                <p class="mb-0">Місяців в аналізі</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${Math.round(totalRevenue)} грн</h4>
                                <p class="mb-0">Загальна виручка</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hryvnia-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${totalOrders}</h4>
                                <p class="mb-0">Замовлень</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 style="font-size: 12px;">${topMonth.month}</h6>
                                <h5>${topMonth.orders}</h5>
                                <p class="mb-0">Найактивніший місяць</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-trophy fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (data.summary) {
        // Для продуктов показываем оригинальные метрики
        cards = `
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${data.summary.total_products || Object.keys(data.clusters).length}</h4>
                                <p class="mb-0">Всього категорій</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${data.summary.total_revenue ? Math.round(data.summary.total_revenue) + ' грн' : 'N/A'}</h4>
                                <p class="mb-0">Загальна виручка</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hryvnia-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${data.summary.avg_revenue ? Math.round(data.summary.avg_revenue) + ' грн' : 'N/A'}</h4>
                                <p class="mb-0">Середня виручка</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>${data.summary.avg_quantity ? Math.round(data.summary.avg_quantity) : 'N/A'}</h4>
                                <p class="mb-0">Середня кількість</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-boxes fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        // Fallback для случаев когда нет данных
        cards = `
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>Результати кластеризації завантажено</h5>
                        <p class="mb-0">Перегляньте детальну інформацію нижче</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = cards;
}

// Display 3D chart
function display3DChart(data) {
    const criteria = document.getElementById('criteria').value;
    
    // Update chart title
    const titleMap = {
        'products': '3D Кластеризація за продуктами',
        'prices': '3D Кластеризація за ціновими категоріями',
        'materials': '3D Кластеризація за сировиною',
        'regions': '3D Кластеризація за регіонами',
        // 'time': '3D Кластеризація за часовими періодами'
    };
    document.getElementById('chart-title').textContent = titleMap[criteria] || '3D Графік кластеризації';

    // Prepare 3D data
    const plotData = prepare3DData(data, criteria);
    
    // 3D Plot layout
    const layout = {
        title: {
            text: titleMap[criteria],
            font: { size: 16 }
        },
        scene: {
            xaxis: { title: getAxisLabel(criteria, 'x') },
            yaxis: { title: getAxisLabel(criteria, 'y') },
            zaxis: { title: getAxisLabel(criteria, 'z') },
            camera: {
                eye: { x: 1.5, y: 1.5, z: 1.5 }
            }
        },
        margin: { t: 50, b: 0, l: 0, r: 0 },
        showlegend: true,
        legend: {
            x: 0,
            y: 1,
            bgcolor: 'rgba(255,255,255,0.8)'
        }
    };

    const config = {
        displayModeBar: true,
        modeBarButtonsToRemove: ['pan2d', 'lasso2d'],
        responsive: true
    };

    Plotly.newPlot('chart3d', plotData, layout, config);
    
    // Create legend
    createChartLegend(plotData);
}

// Display 2D chart
function display2DChart(data) {
    const criteria = document.getElementById('criteria').value;
    
    // Update chart title
    const titleMap = {
        'products': '2D Кластеризація за продуктами',
        'prices': '2D Кластеризація за ціновими категоріями',
        'materials': '2D Кластеризація за сировиною',
        'regions': '2D Кластеризація за регіонами',
        // 'time': '2D Кластеризація за часовими періодами'
    };
    document.getElementById('chart-title').textContent = titleMap[criteria] || '2D Графік кластеризації';

    // Destroy existing chart
    if (clusterChart) {
        clusterChart.destroy();
    }

    // Create 2D chart
    const ctx = document.getElementById('clusterChart').getContext('2d');
    
    if (data.chart_data) {
        if (criteria === 'prices') {
            // Pie chart for price ranges
            clusterChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.chart_data.labels,
                    datasets: [{
                        data: data.chart_data.data,
                        backgroundColor: data.chart_data.backgroundColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else if (criteria === 'materials') {
            // Bar chart for materials
            clusterChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.chart_data.labels,
                    datasets: [{
                        label: 'Загальний відсоток використання',
                        data: data.chart_data.data,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else if (criteria === 'products' || criteria === 'regions') {
            // Multi-dataset chart
            clusterChart = new Chart(ctx, {
                type: 'bar',
                data: data.chart_data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            // Default scatter plot for other criteria
            const scatterData = prepare2DScatterData(data, criteria);
            clusterChart = new Chart(ctx, {
                type: 'scatter',
                data: scatterData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: {
                                display: true,
                                text: getAxisLabel(criteria, 'x')
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: getAxisLabel(criteria, 'y')
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Create simple legend for 2D
    const legendContainer = document.getElementById('chart-legend');
    legendContainer.innerHTML = `<p class="text-muted">2D відображення кластеризації за критерієм: <strong>${criteria}</strong></p>`;
}

// Prepare 2D scatter data
function prepare2DScatterData(data, criteria) {
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
    ];
    
    let datasets = [];
    
    if (criteria === 'products' && data.clusters) {
        Object.keys(data.clusters).forEach((clusterKey, clusterIndex) => {
            const products = data.clusters[clusterKey];
            
            if (products.length > 0) {
                const dataset = {
                    label: getClusterLabel(clusterKey),
                    data: products.map(p => ({
                        x: p.quantity || 0,
                        y: p.revenue || 0
                    })),
                    backgroundColor: colors[clusterIndex % colors.length],
                    borderColor: colors[clusterIndex % colors.length],
                    pointRadius: 5
                };
                datasets.push(dataset);
            }
        });
    }
    
    return { datasets };
}

// Prepare 3D data based on criteria (keeping original implementation)
function prepare3DData(data, criteria) {
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
    ];
    
    let plotData = [];
    
    if (criteria === 'products' && data.clusters) {
        // Product clustering: X=quantity, Y=revenue, Z=orders_count
        Object.keys(data.clusters).forEach((clusterKey, clusterIndex) => {
            const products = data.clusters[clusterKey];
            
            if (products.length > 0) {
                const trace = {
                    x: products.map(p => p.quantity || 0),
                    y: products.map(p => p.revenue || 0),
                    z: products.map(p => p.orders_count || 0),
                    mode: 'markers',
                    type: 'scatter3d',
                    marker: {
                        size: products.map(p => Math.min(Math.max((p.revenue || 0) / 100, 5), 20)),
                        color: colors[clusterIndex % colors.length],
                        opacity: 0.8,
                        line: {
                            color: 'rgba(0,0,0,0.3)',
                            width: 1
                        }
                    },
                    text: products.map(p => `${p.name}<br>Кількість: ${p.quantity}<br>Виручка: ${Math.round(p.revenue || 0)} грн<br>Замовлень: ${p.orders_count}`),
                    name: getClusterLabel(clusterKey)
                };
                plotData.push(trace);
            }
        });
    } else if (criteria === 'prices') {
        // Price clustering
        const priceRanges = Object.keys(data.clusters);
        const trace = {
            x: priceRanges.map((range, i) => i),
            y: Object.values(data.clusters).map(cluster => cluster.count),
            z: Object.values(data.clusters).map(cluster => {
                const avgValue = cluster.orders.length > 0 
                    ? cluster.orders.reduce((sum, order) => sum + order.total, 0) / cluster.orders.length 
                    : 0;
                return avgValue;
            }),
            mode: 'markers',
            type: 'scatter3d',
            marker: {
                size: Object.values(data.clusters).map(cluster => Math.min(Math.max(cluster.count * 2, 5), 25)),
                color: Object.values(data.clusters).map((cluster, i) => colors[i % colors.length]),
                opacity: 0.8
            },
            text: priceRanges.map((range, i) => {
                const cluster = data.clusters[range];
                const avgOrder = cluster.orders.length > 0 ? cluster.orders.reduce((sum, order) => sum + order.total, 0) / cluster.orders.length : 0;
                return `${range} грн<br>Замовлень: ${cluster.count}<br>Середня вартість: ${Math.round(avgOrder)} грн`;
            }),
            name: 'Цінові категорії'
        };
        plotData.push(trace);
    } else if (criteria === 'materials') {
        // Materials clustering
        const materials = Object.values(data.clusters);
        const trace = {
            x: materials.map((mat, i) => i),
            y: materials.map(mat => mat.total_percentage || 0),
            z: materials.map(mat => mat.product_count || 0),
            mode: 'markers',
            type: 'scatter3d',
            marker: {
                size: materials.map(mat => Math.min(Math.max((mat.total_percentage || 0) / 10, 5), 20)),
                color: materials.map((mat, i) => colors[i % colors.length]),
                opacity: 0.8
            },
            text: materials.map(mat => `${mat.name}<br>Загальний %: ${Math.round(mat.total_percentage || 0)}%<br>Продуктів: ${mat.product_count}<br>Замовлень: ${mat.order_count}`),
            name: 'Сировина'
        };
        plotData.push(trace);
    } else if (criteria === 'regions') {
        // Regional clustering
        const regions = Object.values(data.clusters);
        const trace = {
            x: regions.map((reg, i) => i),
            y: regions.map(reg => reg.orders_count || 0),
            z: regions.map(reg => reg.revenue || 0),
            mode: 'markers',
            type: 'scatter3d',
            marker: {
                size: regions.map(reg => Math.min(Math.max((reg.customers_count || 0) * 2, 5), 25)),
                color: regions.map((reg, i) => colors[i % colors.length]),
                opacity: 0.8
            },
            text: regions.map(reg => `${reg.name}<br>Замовлень: ${reg.orders_count}<br>Виручка: ${Math.round(reg.revenue || 0)} грн<br>Клієнтів: ${reg.customers_count}`),
            name: 'Регіони'
        };
        plotData.push(trace);
    }
    
    return plotData;
}

// Get axis labels for different criteria
function getAxisLabel(criteria, axis) {
    const labels = {
        'products': {
            'x': 'Кількість продажів',
            'y': 'Виручка (грн)',
            'z': 'Кількість замовлень'
        },
        'prices': {
            'x': 'Ціновий діапазон',
            'y': 'Кількість замовлень',
            'z': 'Середня вартість замовлення'
        },
        'materials': {
            'x': 'Тип сировини',
            'y': 'Загальний відсоток (%)',
            'z': 'Кількість продуктів'
        },
        'regions': {
            'x': 'Регіон',
            'y': 'Кількість замовлень',
            'z': 'Виручка (грн)'
        },
        'time': {
            'x': 'Часовий період',
            'y': 'Кількість замовлень',
            'z': 'Виручка (грн)'
        }
    };
    
    return labels[criteria] ? labels[criteria][axis] : axis.toUpperCase();
}

// Create chart legend
function createChartLegend(plotData) {
    const legendContainer = document.getElementById('chart-legend');
    let legendHTML = '<div class="row">';
    
    plotData.forEach((trace, index) => {
        legendHTML += `
            <div class="col-md-6 col-lg-4 mb-2">
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background-color: ${Array.isArray(trace.marker.color) ? trace.marker.color[0] : trace.marker.color}; border-radius: 50%; margin-right: 10px;"></div>
                    <span>${trace.name}</span>
                    <small class="text-muted ml-2">(${trace.x.length} точок)</small>
                </div>
            </div>
        `;
    });
    
    legendHTML += '</div>';
    legendContainer.innerHTML = legendHTML;
}

// Get cluster label in Ukrainian
function getClusterLabel(clusterKey) {
    const labelMap = {
        'high_revenue_high_volume': 'Висока виручка + Великий обсяг',
        'high_revenue_low_volume': 'Висока виручка + Малий обсяг',
        'low_revenue_high_volume': 'Низька виручка + Великий обсяг',
        'low_revenue_low_volume': 'Низька виручка + Малий обсяг'
    };
    return labelMap[clusterKey] || clusterKey;
}

// Display 2D charts (pie and trend)
function display2DCharts(data) {
    // Destroy existing charts
    if (pieChart) pieChart.destroy();
    if (trendChart) trendChart.destroy();

    // Create pie chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    createDistributionChart(pieCtx, data, document.getElementById('criteria').value);

    // Create trend chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    createTrendChart(trendCtx, data, document.getElementById('criteria').value);
}

// Create distribution pie chart
function createDistributionChart(ctx, data, criteria) {
    let pieData = [];
    let labels = [];
    
    if (criteria === 'products' && data.clusters) {
        Object.keys(data.clusters).forEach(clusterKey => {
            labels.push(getClusterLabel(clusterKey));
            pieData.push(data.clusters[clusterKey].length);
        });
    } else if (data.clusters) {
        const items = Object.values(data.clusters).slice(0, 5);
        items.forEach((item, index) => {
            labels.push(item.name || `Категорія ${index + 1}`);
            pieData.push(item.orders_count || item.count || item.product_count || 1);
        });
    }

    if (pieData.length > 0) {
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: pieData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

// Create trend chart
function createTrendChart(ctx, data, criteria) {
    const labels = ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер'];
    const trendData = labels.map(() => Math.floor(Math.random() * 100) + 50);
    
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Тренд продажів',
                data: trendData,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
}

// Chart view controls
function show3DChart() {
    currentView = '3d';
    document.getElementById('chart3d-container').style.display = 'block';
    document.getElementById('chart2d-container').style.display = 'none';
    
    // Update button states
    document.getElementById('btn-3d').classList.add('active');
    document.getElementById('btn-2d').classList.remove('active');
    
    // Redraw 3D chart
    if (currentData) {
        display3DChart(currentData);
    }
}

function show2DChart() {
    currentView = '2d';
    document.getElementById('chart3d-container').style.display = 'none';
    document.getElementById('chart2d-container').style.display = 'block';
    
    // Update button states
    document.getElementById('btn-3d').classList.remove('active');
    document.getElementById('btn-2d').classList.add('active');
    
    // Redraw 2D chart
    if (currentData) {
        display2DChart(currentData);
    }
}

// 3D Chart controls
function resetCamera() {
    const update = {
        'scene.camera.eye': { x: 1.5, y: 1.5, z: 1.5 }
    };
    Plotly.relayout('chart3d', update);
}

function toggleRotation() {
    const btn = document.getElementById('rotation-btn');
    
    if (isRotating) {
        clearInterval(rotationAnimation);
        btn.innerHTML = '<i class="fas fa-play"></i>';
        isRotating = false;
    } else {
        let angle = 0;
        rotationAnimation = setInterval(() => {
            angle += 2;
            const x = 1.5 * Math.cos(angle * Math.PI / 180);
            const y = 1.5 * Math.sin(angle * Math.PI / 180);
            
            Plotly.relayout('chart3d', {
                'scene.camera.eye': { x: x, y: y, z: 1.5 }
            });
        }, 100);
        
        btn.innerHTML = '<i class="fas fa-pause"></i>';
        isRotating = true;
    }
}

// Display detailed results
function displayDetailedResults(data) {
    const criteria = document.getElementById('criteria').value;
    
    document.getElementById('table-header').innerHTML = '';
    document.getElementById('table-body').innerHTML = '';
    document.getElementById('results-cards').innerHTML = '';

    if (criteria === 'products') {
        displayProductResults(data.clusters);
    } else if (criteria === 'prices') {
        displayPriceResults(data.clusters);
    } else if (criteria === 'materials') {
        displayMaterialResults(data.clusters);
    } else if (criteria === 'regions') {
        displayRegionResults(data.clusters);
    } else if (criteria === 'time') {
        displayTimeResults(data.clusters);
    }
}

// Display product clustering results
function displayProductResults(clusters) {
    document.getElementById('table-header').innerHTML = `
        <tr>
            <th>Кластер</th>
            <th>Продукт</th>
            <th>Кількість продажів</th>
            <th>Виручка (грн)</th>
            <th>Кількість замовлень</th>
            <th>Склад</th>
        </tr>
    `;

    let tableRows = '';
    let cards = '';

    Object.keys(clusters).forEach(clusterKey => {
        const clusterLabel = getClusterLabel(clusterKey);
        const products = clusters[clusterKey];

        products.forEach(product => {
            const compositionText = product.composition ? 
                Object.entries(product.composition)
                    .filter(([key, value]) => value > 0)
                    .map(([key, value]) => `${key}: ${value}%`)
                    .join(', ') : 'Не вказано';

            tableRows += `
                <tr>
                    <td><span class="badge badge-info">${clusterLabel}</span></td>
                    <td>${product.name}</td>
                    <td>${product.quantity}</td>
                    <td>${Math.round(product.revenue)}</td>
                    <td>${product.orders_count}</td>
                    <td><small>${compositionText}</small></td>
                </tr>
            `;

            cards += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <span class="badge badge-info">${clusterLabel}</span>
                            <h6 class="mb-0 mt-1">${product.name}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Кількість:</small>
                                    <div><strong>${product.quantity}</strong></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Виручка:</small>
                                    <div><strong>${Math.round(product.revenue)} грн</strong></div>
                                </div>
                            </div>
                            <hr>
                            <small class="text-muted">Склад:</small>
                            <div><small>${compositionText}</small></div>
                        </div>
                    </div>
                </div>
            `;
        });
    });

    document.getElementById('table-body').innerHTML = tableRows;
    document.getElementById('results-cards').innerHTML = cards;
}

// Other display functions (keeping minimal implementations for brevity)
function displayPriceResults(clusters) {
    document.getElementById('table-header').innerHTML = `<tr><th>Ціновий діапазон</th><th>Кількість замовлень</th></tr>`;
    let tableRows = '';
    Object.entries(clusters).forEach(([range, data]) => {
        tableRows += `<tr><td>${range}</td><td>${data.count}</td></tr>`;
    });
    document.getElementById('table-body').innerHTML = tableRows;
}

// Display material clustering results (ОБНОВЛЕННАЯ ФУНКЦИЯ)
function displayMaterialResults(clusters) {
    document.getElementById('table-header').innerHTML = `
        <tr>
            <th>Сировина</th>
            <th>Середній відсоток в продуктах</th>
            <th>Частота використання в замовленнях</th>
            <th>Кількість продуктів</th>
            <th>Кількість замовлень</th>
        </tr>
    `;

    let tableRows = '';
    let cards = '';

    Object.entries(clusters).forEach(([material, data]) => {
        tableRows += `
            <tr>
                <td><strong>${data.name}</strong></td>
                <td>${data.average_percentage}%</td>
                <td>${data.usage_percentage}%</td>
                <td>${data.product_count}</td>
                <td>${data.order_count}</td>
            </tr>
        `;

        cards += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">${data.name}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Середній %:</small>
                                <div><strong class="text-primary">${data.average_percentage}%</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Частота:</small>
                                <div><strong class="text-success">${data.usage_percentage}%</strong></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Продуктів:</small>
                                <div><strong>${data.product_count}</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Замовлень:</small>
                                <div><strong>${data.order_count}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    document.getElementById('table-body').innerHTML = tableRows;
    document.getElementById('results-cards').innerHTML = cards;
}

function displayRegionResults(clusters) {
    document.getElementById('table-header').innerHTML = `<tr><th>Регіон</th><th>Кількість замовлень</th></tr>`;
    let tableRows = '';
    Object.entries(clusters).forEach(([region, data]) => {
        tableRows += `<tr><td>${data.name}</td><td>${data.orders_count}</td></tr>`;
    });
    document.getElementById('table-body').innerHTML = tableRows;
}

function displayTimeResults(clusters) {
    document.getElementById('table-header').innerHTML = `<tr><th>Період</th><th>Замовлення</th></tr>`;
    document.getElementById('table-body').innerHTML = '<tr><td colspan="2">Дані часової кластеризації</td></tr>';
}

// Toggle between table and cards view
function toggleView(viewType) {
    const tableView = document.getElementById('results-table-view');
    const cardsView = document.getElementById('results-cards-view');
    const buttons = document.querySelectorAll('.btn-group[role="group"] button');

    if (viewType === 'table') {
        tableView.style.display = 'block';
        cardsView.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    } else {
        tableView.style.display = 'none';
        cardsView.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
    }
}

// Export data functionality
function exportData() {
    if (!currentData) {
        alert('Немає даних для експорту');
        return;
    }

    const criteria = document.getElementById('criteria').value;
    const dataStr = JSON.stringify(currentData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `analytics_${criteria}_${new Date().toISOString().split('T')[0]}.json`;
    link.click();
}
</script>
@endsection
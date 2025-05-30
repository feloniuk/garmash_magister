$('#prediction-form').on('submit', function(event) {
    event.preventDefault();

    const salesData = JSON.parse($('#sales_data').val());

    $.ajax({
        url: 'http://localhost:8001/api/forecast',
        method: 'POST',
        data: {
            dates: salesData.dates,
            sales: salesData.sales,
            forecast_period: $('#forecast-period').val(),
            param1: $('#param1').val(),
            param2: $('#param2').val(),
            param3: $('#param3').val(),
        },
        success: function(response) {
            const realData = response.dates.map((date, index) => {
                return [Date.parse(date), response.values[index]];
            });

            const predictedData = response.predicted_dates.map((date, index) => {
                return [Date.parse(date), response.predicted_values[index]];
            });

            if (realData.length > 0) {
                predictedData.unshift(realData[realData.length - 1]);
            }

            Highcharts.chart('forecast-chart-container', {
                title: {
                    text: 'Прогноз даних',
                    align: 'left'
                },
                subtitle: {
                    text: 'Пунктиром позначено прогнозовані дані',
                    align: 'left'
                },
                xAxis: {
                    type: 'datetime',
                    title: {
                        text: 'Дати'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Значення'
                    }
                },
                tooltip: {
                    xDateFormat: '%Y-%m-%d',
                    pointFormat: '{point.y}'
                },
                series: [{
                    name: 'Реальні дані',
                    data: realData,
                    zoneAxis: 'x',
                    lineWidth: 4,
                    marker: {
                        lineWidth: 2,
                        lineColor: '#4840d6',
                        fillColor: '#fff'
                    },
                }, {
                    name: 'Прогнозовані дані',
                    data: predictedData,
                    dashStyle: 'Dot',
                    color: '#fa4fed',
                    lineWidth: 4,
                }]
            });
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                console.log(xhr.responseJSON.errors);
                alert("Сталася помилка: " + xhr.responseJSON.errors.join(", "));
            } else {
                alert("Сталася невідома помилка.");
            }
        }
    });
});

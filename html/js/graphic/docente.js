$(function () {
   $('#container_encuesta').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Gráfica de Encuesta'
            },
            xAxis: {
                categories: $cources
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total de Preguntas'
                }
            },
            legend: {
                backgroundColor: '#FFFFFF',
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: 'John',
                data: [5, 3, 4, 7, 2]
            }, {
                name: 'Jane',
                data: [2, 2, 3, 2, 1]
            }, {
                name: 'Joe',
                data: [3, 4, 4, 2, 5]
            }]
    });
});
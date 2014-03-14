$(function () {
   $('#container_encuesta').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Gráfica de Encuesta Periodo'
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
                name: 'Siempre',
                data: $cant

            }, {
                name: 'Casi Siempre',
                data: $cant
            }, {
                name: 'A veces',
                data: $cant

            },{
                name:'Nunca',
                data: $cant
            }]
    });
});

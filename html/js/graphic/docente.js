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
                data: [1,4,2,3]

            }, {
                name: 'Casi Siempre',
                data: [7,4,2,3]
            }, {
                name: 'A veces',
                data: [1,6,2,3]

            },{
                name:'Nunca',
                data: [12,4,2,3]
            }]
    });
});

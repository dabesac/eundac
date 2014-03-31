$(function () {
        $('#container_encuesta').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ' '
            },
            xAxis: {
                categories: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11' ,'12'],
                title: {
                    text: 'Preguntas'
                },
                name: 'Siempre'
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Alumnos'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                        style: {
                            textShadow: '0 0 3px black, 0 0 3px black'
                        }
                    }
                }
            },
            series: [{
                name: 'Siempre',
                data: [5, -1, -1, 4, 2, 6, 3, 4, 7, 2, -1, -1]
            }, {
                name: 'Casi Siempre',
                data: [2, -1, -1, 5, 5, 3, 4, 4, 7, 2, -1, -1]
            }, {
                name: 'Aveces',
                data: [2, -1, -1, 2, 5, 3, 4, 4, 7, 2, -1, -1]
            }, {
                name: 'Nunca',
                data: [2, -1, -1, 3, 5, 3, 4, 4, 7, 2, -1, -1]
            }, {
                name: 'Si',
                data: [-1, 3, 4, -1, -1, -1, -1, -1, -1, -1, -1, 5]
            }, {
                name: 'No',
                data: [-1, 2, 2, -1, -1, -1, -1, -1, -1, -1, -1, 7]
            }, {
                name: 'Muy Bueno',
                data: [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 5, -1]
            }, {
                name: 'Bueno',
                data: [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 3, -1]
            }, {
                name: 'Regular',
                data: [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 6, -1]
            }, {
                name: 'Malo',
                data: [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 7, -1]
            }],
        });
    });
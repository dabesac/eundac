var cantidadCursos = $('#cantidadCursos').val();
alert(cantidadCursos);


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
            series: datosEncuesta
        });
    });
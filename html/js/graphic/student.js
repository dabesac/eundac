$(function () {
    /******************-----------grapphic-performance--------*********************/
        $('#container_performance').highcharts({
            chart: {
                type: 'area',
                spacingBottom: 30
            },
            title: {
                text: 'Avance Académico'
            },
            subtitle: {
                text: '* Cursos',
                floating: true,
                align: 'right',
                verticalAlign: 'bottom',
                y: 15
            },
            
            xAxis: {
                categories: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                title: {
                    text:'Semestres'
                }
            },
            yAxis: {
                title: {
                    text: 'Número de Cursos'
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ this.y;
                }
            },
            plotOptions: {
                area: {
                    fillOpacity: 0.7
                }
            },
            credits: {
                enabled: false
            },
            series: $data_courses
        });
        
        /**========================graphic assistemce=======================================**/
        $('#container_assistence').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Asistencia'
            },
            xAxis: {
                categories: $courses
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Asistencias'
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
            series: $dat_assist
        });
});
        
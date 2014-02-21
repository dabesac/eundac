$(function () {
    /******************-----------grapphic-performance--------*********************/
        $('#container_performance').highcharts({
            chart: {
                type: 'area',
                spacingBottom: 30
            },
            title: {
                text: 'Rendimiento Academico'
            },
            subtitle: {
                text: '* Cursos',
                floating: true,
                align: 'right',
                verticalAlign: 'bottom',
                y: 15
            },
            
            xAxis: {
                categories: ['Semestre 1', 'Semestre 2', 'Semestre 3', 'Semestre 4', 'Semestre 5', 'Semestre 6', 'Semestre 7', 'Semestre 8', 'Semestre 9', 'Semestre 10', 'Semestre 11', 'Semestre 12']
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
                    text: 'Total Asitencias'
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
        
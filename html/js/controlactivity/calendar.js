$(function() {
    $( "#calendar_datapiket" ).datepicker()
});
var next_date = new Date()
var next_day = 1
var day_previous = 1
var tr_today_day = $('<div class="tg-today" style="height:1008px;margin-bottom:-1008px;">&nbsp;</div>')
var tg_col0	= $('<div id="tgCol0" class="tg-col-eventwrapper" style="height:1008px;margin-bottom:-1008px;"><div class="tg-gutter">')
var tg_over_div = $('<div class="tg-hourmarker tg-nowmarker" id="tgnowmarker" style="top:539px;">')
var tg_over0 = $('<div id="tgOver0" class="tg-col-overlaywrapper">').append(tg_over_div)
var tg_over1 = $('<div id="tgOver0" class="tg-col-overlaywrapper">')
var tg_default_day = $('<td class="tg-col-today tg-weekend">').append(tr_today_day,tg_col0,tg_over0)			
var tg_day_other = $('<td class="tg-col-today tg-weekend">').append(tg_col0,tg_over1)
// var now = new Date();
// now.setDate(now.getDate()+28);
// alert(now);
date = new Date()
day_month_digit = date.getDay() /***dia de la semana **/
month_digit = date.getMonth() /***mes actual**/
year_digit = date.getFullYear() /** año actual**/
date_month = date.getDate()  /****dia del mes actual */

var calendar = {
	init:function(){
		 
		day_letters = change_day(date_month,day_month_digit,month_digit)
		data_calenadar()
		calendar.calendar_day(day_letters,day_month_digit,month_digit,year_digit)
		// console.log(data_calenadar)
		$("#nav-secundary-text").find('h4').append(
			$('<div class="btn-group">').append(
				$('<button type="button" class="btn btn-default" id="btn_default_day">Hoy</button>'),
				$('<button type="button" class="btn btn-default" id="btn_day_previous" ><span class="glyphicon glyphicon-chevron-left"></span></button>'),
				$('<button type="button" class="btn btn-default" id="btn_next_day" ><span class="glyphicon glyphicon-chevron-right"></span></button>')
			),
			$('<span style="font-size: 13px;">').text()
		)
		$("#weekViewAllDaywk .st-s").click(calendar.add_event_all_day)
		$("#btn_next_day").click(calendar.next_day)
		$("#btn_day_previous").click(calendar.previous_day)
		$("#btn_default_day").click(calendar.default_day)
		$("#btn_week").click(calendar.default_week)
	},
	default_week:function(){
		$("#contenedor_prueba").load("/controlactivity/index/save")
	},
	default_day:function(){
		
	},
	default_session:function(data){
		sss = data.period.class_start_date.split('-')
		date = new Date(sss)
		date_1 = new Date()
		if (date == date_1) {
			console.log(date)
		}
		// data.period.class_start_date
	},
	/****
	**** cambio de calenadrio por día 
	****/
	next_day:function(){
		// next_day ++
		day_add = date_month+1
		date_next = new Date(year_digit,month_digit,day_add)
		day_week = date_next.getDay()
		day_month = date_next.getDate()
		month=date_next.getMonth()
		year=date_next.getFullYear()

		day_month_digit = day_week /***dia de la semana **/
		month_digit = month /***mes actual**/
		year_digit = year /** año actual**/
		date_month = day_month  /****dia del mes actual */

		day_letters = change_day(day_month,day_week,month)
		calendar.calendar_day(day_letters)
	},
	/****
	***** regreso de día 
	*****/
	previous_day:function(){
		// day_previous ++
		decrease = date_month - 1
		pevious_date = new Date(year_digit,month_digit,decrease)
		day_week = pevious_date.getDay()
		day_month = pevious_date.getDate()
		month=pevious_date.getMonth()
		year=pevious_date.getFullYear()

		day_month_digit = day_week /***dia de la semana **/
		month_digit = month /***mes actual**/
		year_digit = year /** año actual**/
		date_month = day_month  /****dia del mes actual */

		day_letters = change_day(day_month,day_week,month)
		calendar.calendar_day(day_letters)
	},
	add_event_all_day:function(){
		position = $("#weekViewAllDaywk").position()
		console.log(position) 
		tr = $(this).parent()
		table = $(this).parent().length
		console.log(table)
		// $("#weekViewAllDayBgwk").height(height)
		$("#weekViewAllDaywk table.st-grid").append(
			$('<tr>').append(
				$('<td class="st-c">').append(
					$('<div class="st-c-pos">').append(
					$('<div class="ca-evp13 rb-n" style="border:1px solid #1587BD;color:#1d1d1d;background-color:#9FC6E7">').append(
						$('<div class="rb-ni">').append(
							$('<span class="evt-lk ca-elp13">').text("(sin titulo)")
						)
					)
					)
				)
			)
		)
	},
	calendar_day:function(day_letters,day,month,year){

		// var table = $('')
		$("#topcontainerwk").html('<table class="wk-weektop" cellpadding="0" cellspacing="0">')
		$("#topcontainerwk table").append(
			$('<tr class="wk-daynames" >').append(
				$('<td class="wk-tzlabel" style="width:60px" rowspan="3">').text('GMT-05'),
				$('<th title="'+day_letters+'" scope="col">').append(
					$('<div class="wk-dayname wk-today wk-today-last" >').append(
						$('<span class="ca-cdp22653 wk-daylink">').text(day_letters)
					)
				),
				$('<th class="wk-dummyth" rowspan="3" style="width: 17px;">&nbsp;</th>')
			),
			$('<tr>').append(
				$('<td class="wk-allday" colspan="1">').append(
					$('<div id="weekViewAllDaywk" class="wk-allday-pos">').append(
						$('<table id="weekViewAllDayBgwk" cellpadding="0" cellspacing="0" class="st-bg-all" style="height:13px;">').append(
							$('<tr><td class="st-bg-td-last">&nbsp;</td></tr>')
						),
						$('<table cellpadding="0" cellspacing="0" class="st-grid">').append(
							$('<tr><td class="st-c st-s">&nbsp;</td></tr>')
						)
					)
				)
			),
			$('<tr class="wk-webcontent"><td class="wk-webcontent-td">')
		)
		
		$("#scrolltimedeventswk div").html('<table id="tgTable" class="tg-timedevents" cellpadding="0" cellspacing="0" style="height:1010px">')
		$("#scrolltimedeventswk div	table").append(
			$('<tr height="1">').append(
				$('<td style="width:60px;"></td>'),
				$('<td colspan="1">').append(
						$('<div class="tg-spanningwrapper">').append(
							$('<div class="tg-hourmarkers">').append(
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">'),
								$('<div class="tg-markercell"><div class="tg-dualmarker">')
							)
						),
						$('<div class="tg-spanningwrapper tg-chipspanningwrapper" id="tgspanningwrapper">')
					)
			),
			$('<tr>').append(
				$('<td class="tg-times-pri">').append(
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('12am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('1am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('2am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('3am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('4am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('5am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('6am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('7am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('8am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('9am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('10am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('11am'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('12pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('1pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('2pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('3pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('4pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('5pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('6pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('7pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('8pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('9pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('1pm'),
					$('<div style="height:42px;"><div class="tg-time-pri" style="height:41px;">').text('11pm'),
					$('<div id="tgnowptr" class="tg-nowptr" style="left:0px;top:535px;">')
				),
				tg_default_day
			)
		)
	}

}
function data_calenadar(){
	$.ajax({
		url : '/controlactivity/index/datajson',
		type : 'GET',
		dataType : 'json',
		success : function(json) {
			if (json == null) {
				console.log("no registros");
			}else{
				calendar.default_session(json)
			}
		},
		error : function(jqXHR, status, error) {
			alert('Disculpe, existió un problema');
		}
	});
}
function change_day(date_month,day_digit,month_digit){
	month_digit = month_digit+1;
	day_letters = new Array(
				"domingo " +date_month+"/"+month_digit,
				"lunes " +date_month+"/"+month_digit,
				"martes " +date_month+"/"+month_digit,
				"miércoles " +date_month+"/"+month_digit,
				"jueves " +date_month+"/"+month_digit,
				"viernes " +date_month+"/"+month_digit,
				"sábado " +date_month+"/"+month_digit,
				"domingo " +date_month+"/"+month_digit) 
	return day_letters[day_digit]
}

$(document).ready(calendar.init);

/**
 * Controls: Table plugin
 * 
 * Depends on jWYSIWYG
 */
(function ($) {
	"use strict";

	if (undefined === $.wysiwyg) {
		throw "wysiwyg.table.js depends on $.wysiwyg";
	}

	if (!$.wysiwyg.controls) {
		$.wysiwyg.controls = {};
	}

	var insertTable = function (colCount, rowCount, semana,filler) {

		if (isNaN(rowCount) || isNaN(colCount) || rowCount === null || colCount === null) {
			return;
		}

		var i, j, k,html = ['<table style="width: 95%;">'];

		colCount = parseInt(colCount, 10);
		rowCount = parseInt(rowCount, 10);
		

		if (filler === null) {
			filler = "&nbsp;";
		}
		filler = "<td style='border: solid 1px #000;'  >" + filler + "</td>";

		if (colCount && rowCount) {
			for (i = rowCount; i > 0; i -= 1) {
			html.push("<tr>");
			for (j = colCount; j > 0; j -= 1) {
				html.push(filler);
			}
			html.push("</tr>");
			}	
		}
		else
		{
			if (colCount) { 
			// filler = "<td style='border: solid 1px red; width:20px;'  >" + filler + "</td>";

				for (j = colCount; j > 0; j -= 1) {
				html.push(filler);
				// alert('fas');
				}
			}
			if (rowCount) {
				for (i = rowCount; i > 0; i -= 1) {
				html.push("<tr>");
				html.push(filler);
				html.push("</tr>");
				}
			}
			if (semana) {
				// filler = "<td style='border: solid 1px #000;'  >" + filler + "</td>";
				html.push("<caption style='background-color:#DDDDDD; text-align: left;'>Unidad</caption>");
				// html.push("<tr ><th style='border: solid 1px #000'>semana</th><th style='border: solid 1px #000'>sesión</th><th style='border: solid 1px #000'>Contenidos</th><th style='border: solid 1px #000'>Estrategias</th></tr>");

				for (k = 4; k > 0; k -= 1) {
				html.push("<tbody>");
					html.push("<tr>");
					html.push("<td rowspan='2' >fffffff</td>");
					html.push(filler);
					html.push(filler);
					html.push(filler);
					html.push("<tr>");
				html.push("</tbody>");
				}
			};
		}	
		html.push("</table>");

		return this.insertHtml(html.join(""));
	};

	/*
	 * Wysiwyg namespace: public properties and methods
	 */
	$.wysiwyg.controls.table = function (Wysiwyg) {
		var adialog, dialog, colCount, rowCount, semana,formTableHtml, dialogReplacements, key, translation, regexp;

		dialogReplacements = {
			legend: "Inserta Tabla",
			cols  : "Ingresa Columnas",
			rows  : "Ingresa Filas",
			semana : "Tipo de Curso",
			submit: "Insertar",
			reset: "Cancelar"
		};

		formTableHtml = '<form class="wysiwyg" id="wysiwyg-tableInsert"><fieldset><legend>{legend}</legend>' +
			'<label>{cols}: <input type="text" name="colCount" value="" /></label><br/>' +
			'<label>{rows}: <input type="text" name="rowCount" value="" /></label><br/>' +
			'<input type="submit" class="button" value="{submit}"/> ' +
			'<input type="reset" value="{reset}"/></fieldset></form>';
		
		for (key in dialogReplacements) {
			if ($.wysiwyg.i18n) {
				translation = $.wysiwyg.i18n.t(dialogReplacements[key], "dialogs.table");

				if (translation === dialogReplacements[key]) { // if not translated search in dialogs 
					translation = $.wysiwyg.i18n.t(dialogReplacements[key], "dialogs");
				}

				dialogReplacements[key] = translation;
			}

			regexp = new RegExp("{" + key + "}", "g");
			formTableHtml = formTableHtml.replace(regexp, dialogReplacements[key]);
		}

		if (!Wysiwyg.insertTable) {
			Wysiwyg.insertTable = insertTable;
			// alert(Wysiwyg.insertTable);
		}

		adialog = new $.wysiwyg.dialog(Wysiwyg, {
			"title"   : dialogReplacements.legend,
			"content" : formTableHtml,
			"open"    : function (e, dialog) {
				dialog.find("form#wysiwyg-tableInsert").submit(function (e) {
					e.preventDefault();
					rowCount = dialog.find("input[name=rowCount]").val();
					colCount = dialog.find("input[name=colCount]").val();
					semana =dialog.find("select[name=semana]").val();

					Wysiwyg.insertTable(colCount, rowCount, semana,Wysiwyg.defaults.tableFiller);

					adialog.close();
					return false;
				});

				dialog.find("input:reset").click(function (e) {
					e.preventDefault();
					adialog.close();
					return false;
				});
			}
		});
		
		adialog.open();

		$(Wysiwyg.editorDoc).trigger("editorRefresh.wysiwyg");
	};

	// $.wysiwyg.insertTable = function (object, colCount, rowCount, filler) {
	// 	alert(object);
	// 	return object.each(function () {
	// 		var Wysiwyg = $(this).data("wysiwyg");

	// 		if (!Wysiwyg.insertTable) {
	// 			Wysiwyg.insertTable = insertTable;
	// 		}

	// 		if (!Wysiwyg) {
	// 			return this;
	// 		}

	// 		Wysiwyg.insertTable(colCount, rowCount, filler);
	// 		$(Wysiwyg.editorDoc).trigger("editorRefresh.wysiwyg");

	// 		return this;
	// 	});
	// };
})(jQuery);

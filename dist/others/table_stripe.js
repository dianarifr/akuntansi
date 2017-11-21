// JavaScript Document

$(document).ready(function() {

	var alternateRowColors = function($table){
		$('table.sortable tr:odd').removeClass().addClass('odd');
		$('table.sortable tr:even').removeClass().addClass('even');
	}
	
	$('table.sortable').each(function() {
		var $table = $(this);
		alternateRowColors($table);
		
		
	});
	
});



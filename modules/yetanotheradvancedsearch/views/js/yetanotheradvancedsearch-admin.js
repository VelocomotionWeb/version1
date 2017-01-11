/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

function findCorresponding(corr) {
	for (var i=0; i<yaasCorresponding.length; i++) {
		var yaasCorr = yaasCorresponding[i];
		if (yaasCorr['id'] == corr) {
			return yaasCorr['layout'];
		}
	}
	return 1;
}

function byte2bits(a) {
    var tmp = "";
    for(var i = 128; i >= 1; i /= 2) {
        tmp += a&i?'1':'0';
	}
    return tmp;
}

function updateYaasSubAddForm(editing) {
	var $parentMultiple = $("#allow_multiple_on").parent();
	var value = parseInt($('#id_layout').val());
	switch (value) {
		case 4: // SLIDER
			$parentMultiple.prev().hide();
			$parentMultiple.hide();
			break;
		
		case 2: // COMBO
			$parentMultiple.prev().show();
			$parentMultiple.show();
			if (!editing) $('#allow_multiple_on').prop('checked', false);
			break;
		
		case 1:	// LINK
			$parentMultiple.prev().show();
			$parentMultiple.show();
			if (!editing) $('#allow_multiple_on').prop('checked', true);
			break;
		default:
	}
}

function updateYaasAddForm(editing) {
	
	// hide all options
	$("#id_layout").find("option").css("display", "none").removeClass("possible");
	
	// get bits values
	var value = $('#id_criteria_field').val();
	var corr = findCorresponding(value);
	var bits = byte2bits(corr);
	
	// check bits values, with possible
	var possibles = [1,2,4];
	for (var i=0; i<possibles.length; i++) {
		var possible = possibles[i];
		var power = (Math.log(possible))/(Math.log(2)) ;
		if (bits[7-power] == '1') {
			$("#id_layout").find("option[value=" + possible +"]").css("display", "block").addClass("possible");
		}
	}
	
	if (!editing) $("#id_layout").find("option.possible:first").attr("selected", "selected");
	
	// update sub form
	updateYaasSubAddForm(editing);
}

$(document).ready(function() {
	
  $('#id_criteria_field').change(function() {
	  updateYaasAddForm(false);
  });
  $('#id_layout').change(function() {
	  updateYaasSubAddForm(false);
  });
  
  // init
  var editing = $("#editingYaas").val() == "true";
  updateYaasAddForm(editing);
  
});

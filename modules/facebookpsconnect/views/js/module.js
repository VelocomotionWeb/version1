/*
 * 2003-2015 Business Tech
 *
 *  @author    Business Tech SARL <http://www.businesstech.fr/en/contact-us>
 *  @copyright 2003-2015 Business Tech SARL
 */

// declare main object of module
var FpcModule = function(sName) {
	// set name
	this.name = sName;

	// set name
	this.oldVersion = false;

	// set translated js msgs
	this.msgs = {};

	// stock error array
	this.aError = [];

	// set url of admin img
	this.sImgUrl = '';

	// set url of module's web service
	this.sWebService = '';

	// set response
	this.response = null;

	// set this in obj context
	var oThis = this;

	/*
	 * show() method show effect and assign HTML in
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sId : container to show in
	 * @param string sHtml : HTML to display
	 * @return -
	 */
	this.show = function(sId, sHtml) {
		$("#" + sId).html(sHtml).css('style', 'none');
		$("#" + sId).show('fast');
	};

	/*
	 * hide() method hide effect and delete html
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sId : container to hide in
	 * @return -
	 */
	this.hide = function(sId) {
		$("#" + sId).hide('fast', function() {
				$("#" + sId).html('');
			}
		);
	};

	/*
	 * form() method check all fields of current form and execute : XHR or submit => used for update all admin config
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @see ajax
	 * @param string sForm : form
	 * @param string sURI : query params used for XHR
	 * @param string sRequestParam : param action and type in order to send with post mode
	 * @param string sToDisplay :
	 * @param string sToHide : force to hide specific ID
	 * @param bool bSubmit : used only for sending main form
	 * @param bool bFancyBox : used only for fancybox in xhr
	 * @param string sCallback :
	 * @param string sErrorType :
	 * @param string sLoadBar :
	 * @return string : HTML returned by smarty
	 */
	this.form = function(sForm, sURI, sRequestParam, sToDisplay, sToHide, bSubmit, bFancyBox, sCallback, sErrorType, sLoadBar) {
		// set loading bar
		if (sLoadBar) {
			$('#'+sLoadBar).show();
		}

		// set return validation
		var aError = [];

		// get all fields of form
		var fields = $("#" + sForm).serializeArray();

		// set counter
		var iCounter = 0;

		// set bIsError
		var bIsError = false;

		// check element form
		jQuery.each(fields, function(i, field) {
			bIsError = false;

			switch(field.name) {
				case 'id' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.id;
						bIsError = true;
					}
					break;
				case 'secret' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.secret;
						bIsError = true;
					}
					break;
				case 'callback' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.callback;
						bIsError = true;
					}
					break;
				case 'scope' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.scope;
						bIsError = true;
					}
					break;
				case 'developerKey' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.developerKey;
						bIsError = true;
					}
					break;
				case 'socialEmail' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.socialEmail;
						bIsError = true;
					}
					break;
				default:
					break;
			}

			if ($('#' + field.name) != undefined && bIsError == true) {
				if ($('input[name="' + field.name + '"]').length != 0) {
					$('input[name="' + field.name + '"]').css('border', '1px solid red');
				}
				if ($('textarea[name="' + field.name + '"]').length != 0) {
					$('textarea[name="' + field.name + '"]').css('border', '1px solid red');
				}
				++iCounter;
			}
		});

		// use case - no errors in form
		if (oThis.aError.length == 0) {
			// use case - Ajax request
			if (bSubmit == undefined || bSubmit == null || !bSubmit) {
				// format object of fields in string to execute Ajax request
				var sFormParams = $.param(fields);

				if (sCallback != null) {

				}

				if (sRequestParam != null && sRequestParam != '') {
					sFormParams = sRequestParam + '&' + sFormParams;
				}

				// execute Ajax request
				this.ajax(sURI, sFormParams, sToDisplay, sToHide, bFancyBox, null, sLoadBar);

				return true;
			}
			// use case - send form
			else {
				// hide loading bar
				if (sLoadBar) {
					$('#'+sLoadBar).hide();
				}
				document.forms[sForm].submit();
				return true;
			}
		}
		// display errors
		this.displayError(sErrorType);

		return false;
	};

	/*
	 * ajax() method execute XHR
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sURI : query params used for XHR
	 * @param string sParams :
	 * @param string sToShow :
	 * @param string sToHide :
	 * @param bool bFancyBox : used only for fancybox in xhr
	 * @param bool bFancyBoxActivity : used only for fancybox in xhr
	 * @param string sLoadBar : used only for loading
	 * @return string : HTML returned by smarty
	 */
this.ajax = function(sURI, sParams, sToShow, sToHide, bFancyBox, bFancyBoxActivity, sLoadBar) {
		if(bFancyBox && bFancyBoxActivity != false) {
			$.fancybox.showActivity();
		}

		sParams = 'sMode=xhr' + ((sParams == null || sParams == undefined) ? '' : '&' + sParams) ;

		// configure XHR
		$.ajax({
			type : 'POST',
			url : sURI,
			data : sParams,
			dataType : 'html',
			success: function(data) {
				// hide loading bar
				if (sLoadBar) {
					$('#'+sLoadBar).hide();
				}
				if(bFancyBox) {
					// update fancybox content
					$.fancybox(data);

					// use case - update only widget list
					if (sToShow == oThis.name + 'ConnectorList') {
						oThis.ajax('' + sURI, 'sAction=display&sType=connectors', '' + sToShow + '', '' + sToHide + '');
					}
					// use case - update widget list and hook list
					else if (sToShow == oThis.name + 'HookList') {
						sToShow = sToHide = oThis.name + 'ConnectorList';
						oThis.ajax('' + sURI + 'connectors', 'sAction=display&sType=connectors', '' + sToShow + '', '' + sToHide + '');
						sToShow = sToHide = oThis.name + 'HookList';
						oThis.ajax('' + sURI + 'hooks', 'sAction=display&sType=hooks', '' + sToShow + '', '' + sToHide + '');
					}
				}
				else if (sToShow != null && sToHide != null) {
					// same hide and show
					if (sToShow == sToHide) {
						oThis.hide('fast');
						$('#' + sToHide).hide('fast');
						$('#' + sToHide).empty();
						setTimeout('', 1000);
						oThis.show(sToShow, data);
					}
					else {
						oThis.hide(sToHide);
						setTimeout('', 1000);
						oThis.show(sToShow, data);
					}
				}
				else if (sToShow != null) {
					oThis.show(sToShow, data);
				}
				else if (sToHide != null) {
					oThis.hide(sToHide);
				}
				else {
					oThis.response = data;
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				$("#" + oThis.name + "FormError").addClass('alert error');
				oThis.show("#" + oThis.name + "FormError", '<h3>internal error</h3>');
			}
		});
	};

	/*
	 * displayError() method display errors
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sType : type of container
	 * @return bool
	 */
	this.displayError = function(sType) {
		if (oThis.aError.length != 0) {
			var sError = '<img class="pointer" src="' + oThis.sImgUrl + 'hook/cancel.png' + '" onclick="$(\'#' + oThis.name + sType + 'Error\').hide(\'' + oThis.name + sType + 'Error\');" />';
			for (var i = 0; i < oThis.aError.length;++i) {
				sError += '<span>' + oThis.aError[i] + '</span><br />';
			}
			$("#" + oThis.name + sType + "Error").html(sError).addClass('form-error');
			$("#" + oThis.name + sType + "Error").show();

			// flush errors
			oThis.aError = [];

			return false;
		}
	};

	/*
	 * changeSelect() method displays or hide related option form
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sId : type of container
	 * @param string sDestId
	 * @param string sDestId2
	 * @param string sType of second dest id
	 * @return
	 */
	this.changeSelect = function(sId, sDestId, sDestId2, sDestIdToHide) {
		$("#" + sId).bind($.browser.msie ? 'click' : 'change', function (event) {
			$("#" + sId + " input:checked").each(function () {
				switch ($(this).val()) {
					case 'true' :
						// display option features
						$("#" + sDestId).fadeIn('fast', function() {$("#" + sDestId).css('display', 'block')});
						break;
					default:
						// hide option features
						$("#" + sDestId).fadeOut('fast');

						// set to false
						if (sDestId2 && sDestIdToHide) {
							$("#" + sDestId2 + " input").each(function () {
									switch ($(this).val()) {
										case 'false' :
											$(this).attr('checked', 'checked');
											// hide option features
											$("#" + sDestIdToHide).fadeOut('fast');
											break;
										default:
											$(this).attr('checked', '');
											break;
									}
								}
							);
						}
						break;
				}
			});
		});
	};

	/*
	 * selectAll() method select / deselect all checkbox
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sId : type of container
	 * @param string sCible : all checkbox to process
	 * @return
	 */
	this.selectAll = function(sId, sCible){
		$("#" + sId).bind($.browser.msie ? 'click' : 'change', function (event){
			if (this.checked == true) {
				$(sCible).attr('checked', true);
			}
			else{
				$(sCible).attr('checked', false);
			}
		});
	};

	/*
	 * updateHook() method verify all childnodes of hook list and determine position to display widget in hook
	 * User: Business Tech (www.businesstech.fr)
	 * @param string sURI : query params used for XHR
	 * @param string sHookList :
	 * @param string sToShow :
	 * @param string sToHide :
	 * @param bool bFancyBox : used only for fancybox in xhr
	 * @return string : HTML returned by smarty
	 */
	this.updateHook = function(sURI, sHookList, sToShow, sToHide, bFancyBox) {
		var sList = '';

		if ($("#" + sHookList + " li").length != 0) {
			var aConnectorId = [];
			$("#" + sHookList + " li").each(function(i, elt) {
					aConnectorId[i] = elt.id;
				}
			);
			sList = aConnectorId.toString();
		}
		this.ajax(sURI, 'sConnectorList=' + sList, sToShow, sToHide, bFancyBox);
	};

	/*
	 * draggableConnector() method set draggable
	 * User: Business Tech (www.businesstech.fr)
	 * @param obj Current HTML elt
	 * @return -
	 */
	this.draggableConnector = function() {
		// declare draggable elts
		$("#" + oThis.name + "DraggableConnector li").draggable({
			revert : "invalid"
		});

		// declare droppable elt - only drop elt in this tag
		$("#" + oThis.name + "DroppableConnector ul").droppable({
			drop : function(event, ui) {
				// append draggable elt
				$("#" + oThis.name + "DroppableConnector ul").append(ui.draggable);
				// set css and deactivate drag
				$(ui.draggable).css({position:"relative", top:"0px", left:"0px", display : "block"})
					.draggable("disable")
					.css({opacity : 1})
					.addClass('fbpscsortli', 'ui-state-default');
				// append img and click evt only in no already added img
				var bFound = $(ui.draggable).find('img.' + oThis.name + 'Garbage');

				// use case - add img and click evt in no img case
				if (bFound.length == 0) {
					$(ui.draggable).append('<img class="' + oThis.name + 'Garbage" src="' + oThis.sAdminImgUrl + 'delete.gif" />').bind('click', function() {
							oThis.deleteConnector(this);
						}
					);
				}
			}
		});
	};

	/*
	 * sortableConnector() method set sortable Connector
	 * User: Business Tech (www.businesstech.fr)
	 * @param obj Current HTML elt
	 * @return -
	 */
	this.sortableConnector = function() {
		// set sortable elt
		$( "#" + oThis.name + "Sortable" ).sortable();
		$( "#" + oThis.name + "Sortable" ).disableSelection();
	};

	/*
	 * deleteConnector() method delete added Connector in hook form
	 * User: Business Tech (www.businesstech.fr)
	 * @param obj Current HTML elt
	 * @return -
	 */
	this.deleteConnector = function(oElt) {
		$(oElt).find('img.' + oThis.name + 'Garbage').remove();
		$("#" + oThis.name + "DraggableConnector ul").append($(oElt).removeClass('fbpscsortli').draggable('enable').draggable('option', 'revert' , 'invalid').addClass('fbpscdragli'));
	};

	/*
	 * collect() method save the customer action and behavior
	 * User: Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 * @param string sConnector : connector name : facebook | twitter | google | paypal
	 * @param int iCustId : customer id
	 * @param int iSocialId : customer social id
	 * @param string sAction : FB action : like or want button, etc ...
	 * @param string sType : PS object type: product or manufacturer or supplier ...
	 * @param int iObjId : PS object ID : product or manufacturer or supplier or category ...
	 */
	this.collect = function(sConnector, iCustId, iSocialId, sAction, sType, iObjId) {
		this.ajax(this.sWebService, 'sAction=clt&sType=scl&cn=' + sConnector + '&ci=' + iCustId + '&si=' + iSocialId + '&ca=' + sAction + '&ct=' + sType + '&oi=' + iObjId, null, null, null, null, null);
	};



};
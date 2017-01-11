var prepareTimeInterval;

function initMap() {
	setTimeout(function(){
		$('div.primary_block').append($('#booking_map').show());
		var booking = new google.maps.LatLng(bestkit_booking.map_latitude, bestkit_booking.map_longitude);
		var map = new google.maps.Map(document.getElementById('booking_map'), {
			center: booking,
			zoom: bestkit_booking.map_zoom,
			scrollwheel: false,
			scaleControl: false,
		});

		var coordInfoWindow = new google.maps.InfoWindow();
		coordInfoWindow.setContent(createInfoWindowContent(booking, map.getZoom()));
		coordInfoWindow.setPosition(booking);
		coordInfoWindow.open(map);
		
		map.addListener('zoom_changed', function() {
			coordInfoWindow.setContent(createInfoWindowContent(booking, map.getZoom()));
			coordInfoWindow.open(map);
		});
	}, 1000);
}

var TILE_SIZE = 256;

function createInfoWindowContent(latLng, zoom) {
	var scale = 1 << zoom;
	var worldCoordinate = project(latLng);

	var pixelCoordinate = new google.maps.Point(
	Math.floor(worldCoordinate.x * scale),
	Math.floor(worldCoordinate.y * scale));

	var tileCoordinate = new google.maps.Point(
	Math.floor(worldCoordinate.x * scale / TILE_SIZE),
	Math.floor(worldCoordinate.y * scale / TILE_SIZE));

	return [
		'<b>' + $('h1').text() + '</b>',
		bestkit_booking.address
	].join('<br>');
}

function project(latLng) {
	var siny = Math.sin(latLng.lat() * Math.PI / 180);
	siny = Math.min(Math.max(siny, -0.9999), 0.9999);
	
	return new google.maps.Point(
		TILE_SIZE * (0.5 + latLng.lng() / 360),
		TILE_SIZE * (0.5 - Math.log((1 + siny) / (1 - siny)) / (4 * Math.PI))
	);
}

(function($){
    $(document).ready(function(){
        var booking = $('#bestkit_booking');
        if (typeof(bestkit_booking) != 'undefined') {
            $('#add_to_cart').addClass('disallow_reservation');

			$('div.product_attributes').before(booking);

            $('.primary_block .col-xs-12').
                removeClass('col-md-5').
                removeClass('col-md-4').
                addClass('col-md-3');

            var booking = $('#bestkit_booking');
            $('.pb-center-column:first').after(booking);

            var format = 'mm/dd/yy';
            var dateFormat = function(date){
                return $.datepicker.formatDate(format, date);
            }

            var beforeAjax = function(){
                $.fancybox.showLoading();
                booking.css({'opacity': '0.3'});
            }

            var afterAjax = function(){
                $.fancybox.hideLoading();
                booking.css({'opacity': '1'});
            }

			var timeOffset = 0;

            var timesUpdating = function(date_from, date_to){
                beforeAjax();
                $.ajax({
                    url: bestkit_booking_settings.productAjaxController,
                    data: {'action': 'getTimeContainer', 'id_product': id_product, 'date_from': date_from, 'date_to': date_to},
                    type: 'POST',
                    success: function(html){
                        booking.find('.time_fromto_container').html(html);
                        booking.find('a.available-time').fancybox({autoDimensions: true});
                        $('#booking_from_hour').change();
                    },
                    complete: function(){
                        afterAjax();
                    },
                    error: function(error){
                        alert(error.responseText);
                    }
                });
            }

            var checkReservation = function(date_from, date_to){
                beforeAjax();
                $.ajax({
                    url: bestkit_booking_settings.productAjaxController,
                    data: {'action': 'checkReservation', 'id_product': id_product, 'date_from': date_from, 'date_to': date_to, 'qty': parseInt($('#quantity_wanted').val())},
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result){
                        if (result.status == "success") {
                            booking.find('.reservation_status')
                                .removeClass('alert-danger')
                                .addClass('alert-success')
                                .text(bestkit_booking_translator.allow_to_booking);

							$('#our_price_display').text(formatCurrency(result.price, currencyFormat, currencySign, currencyBlank))
                                	.attr('content', result.price);

                        	productPrice = result.price;
                        	productPriceTaxIncluded = result.price;

                            $('#add_to_cart').removeClass('disallow_reservation');
                        } else {
                            booking.find('.reservation_status')
                                .removeClass('alert-success')
                                .addClass('alert-danger')
                                .text(result.message);
                            $('#add_to_cart').addClass('disallow_reservation');
                        }

                        booking.find('.reservation_status').show();
                    },
                    complete: function(){
                        afterAjax();
                    },
                    error: function(error){
                        alert(error.responseText);
                    }
                });
            }
            
            var isExcludedDay = function(date){
				var time = date.getTime();

            	if (bestkit_booking.exclude_periods.length) {
	            	for (var i in bestkit_booking.exclude_periods) {
		            	if (time >= Date.parse(bestkit_booking.exclude_periods[i].from)
		            		&& time <= Date.parse(bestkit_booking.exclude_periods[i].to)
		            	) {
			            	return false;
		            	}
	            	}
            	}

            	if (bestkit_booking.exclude_recurrent_dates.length) {
	            	for (var i in bestkit_booking.exclude_recurrent_dates) {
		            	if (date.getDay() == bestkit_booking.exclude_recurrent_dates[i].day
		            		&& date.getMonth() == bestkit_booking.exclude_periods[i].month
		            	) {
			            	return false;
		            	}
	            	}
            	}

                if (bestkit_booking.exclude_dates.indexOf(dateFormat(date)) != -1
                    || bestkit_booking.booked_days.indexOf(dateFormat(date)) != -1
                    || bestkit_booking.exclude_weekdays.indexOf(date.getDay()) != -1
                    ) {
                    return false;
                }

                return true;
            }

			var datePrices = [];
			var lastTimeout;
			var addedPrices = [];

			var setDatePrice = function(date){
				if (bestkit_booking.billing_type != 'days') {
					return;
				}

				beforeAjax();
				if (typeof datePrices[date.getTime().toString()] == 'undefined') {
					datePrices[date.getTime().toString()] = false;
				}

				var timeoutId = setTimeout(function(){
					if (lastTimeout == timeoutId) {
						var needPrices = [];

						for (var time in datePrices) {
							if (datePrices[time] == false) {
								needPrices.push(time);
							}
						}

						if (needPrices.length > 0) {
			                $.ajax({
			                    url: bestkit_booking_settings.productAjaxController,
			                    data: {'action': 'getDatePrice', 'dates': needPrices, 'id_product': $('#product_page_product_id').val()},
			                    type: 'POST',
			                    dataType: 'json',
			                    success: function(json){
					                for (var time in json) {
										datePrices[time] = json[time];
									}

			                        fillDatePrices();
			                    },
			                    complete: function(){
			                        afterAjax();
			                    }
			                });
						} else {
							fillDatePrices();
						}


					}
				}, 500);

				lastTimeout = timeoutId;
			}

			var fillDatePrices = function()
			{
				for (var time in datePrices) {
					var need_date = new Date(parseInt(time));
					var elems = $('.ui-datepicker-calendar td[data-month="'+(need_date.getMonth())+'"][data-year="'+need_date.getFullYear()+'"] a');

					elems.each(function(index, value){
						if ($(this).text() == need_date.getDate()) {
							$(this).parent().attr('data-day', need_date.getDate());

							if (addedPrices.indexOf(time) == -1) {
								addedPrices.push(time);
								$('body').append('<style>.ui-datepicker-calendar td[data-month="'+(need_date.getMonth())+'"][data-year="'+need_date.getFullYear()+'"][data-day="'+need_date.getDate()+'"]:after {content: "'+formatCurrency(datePrices[time], currencyFormat, currencySign, currencyBlank)+'"}</style>');
							}
						}
					});
				}

				afterAjax();
			}

            //DATE RANGE!
            /*if (bestkit_booking.range_type == 'date_fromto') {
                $('#add_to_cart').removeClass('disallow_reservation');
            }*/

            if (bestkit_booking.range_type == 'date_fromto' || bestkit_booking.range_type == 'datetime_fromto') {
                var selectedDates = [];

                var isAllowedDay = function(date){
                	if (!isExcludedDay(date)) {
	                	return false;
                	}

                    if (selectedDates.length > 0) {
                        var first = false;
                        for (var i in selectedDates) {
                            if (!first) {
                                first = selectedDates[i];
                                if (date.getTime() < selectedDates[i]) {
                                    return false;
                                }
                            }
                        }
                    }

                    return true;
                }

                var isHighlightDate = function(date){
                    if (selectedDates.indexOf(date) != -1) {
                        return true;
                    }

                    if (selectedDates.length == 2) {
                        if (date > selectedDates[0] && date < selectedDates[1]) {
                            return true;
                        }
                    }

                    return false;
                }

                var selectFlag = false;
                var disableFlag = false;
                booking.find('#bestkit_booking_date1').datepicker({
                    dateFormat: format,
                    minDate: bestkit_booking.date_from, //0
                    //maxDate: bestkit_booking.max_day,
                    maxDate: bestkit_booking.date_to,
                    beforeShowDay: function(date){
                        if (isAllowedDay(date)) {
                            if (disableFlag != false && date.getTime() >= disableFlag) {
                                return [false, ''];
                            }

							if (bestkit_booking.range_type == 'date_fromto') {
								setDatePrice(date);
							}

                            return [true, (isHighlightDate(date.getTime()) ? "ui-state-selected-date" : "")];
                        }

                        if (selectFlag) {
                            if (selectedDates[0] < date.getTime()) {
                                disableFlag = date.getTime();
                            }
                        }

                        return [false, ''];
                    },
                    onSelect: function(dateStr, instance){
                    	var time = Date.parse(dateStr);

                    	if (selectedDates.length == 2 && selectedDates[0] == selectedDates[1] && selectedDates[0] == time) {
                    		selectedDates = [];
                    		booking.find('#bestkit_booking_date1').datepicker('refresh');
                    		booking.find('.time_fromto_container').html('');
                    		return;
                    	}

                        var index = selectedDates.indexOf(time);
                        if (index == -1) {
                            if (selectedDates.length == 2) {
                                selectedDates.pop();
                            }

                            selectedDates.push(time);
                        } else {
                            selectedDates.splice(index, 1);
                        }

                        if (selectedDates.length > 0) {
                            selectFlag = true;

							var date_from = dateFormat(new Date(selectedDates[0]));

                            if (selectedDates.length == 2 && bestkit_booking.range_type == 'date_fromto') {
								var date_to = dateFormat(new Date(selectedDates[1]));

                                booking.find('#booking_from').text(date_from);
                                booking.find('#booking_to').text(date_to);
                                booking.find('.selected-dates').show();

                            	var bookPrice = 0;
                            	for (var time in datePrices) {
                                	if (time >= selectedDates[0] && time < selectedDates[1]) {
	                                	bookPrice += datePrices[time];
                                	}
                            	}

                            	$('#our_price_display').text(formatCurrency(bookPrice, currencyFormat, currencySign, currencyBlank))
                            		.attr('content', bookPrice);
                            	
                            	productPrice = bookPrice;
                            	productPriceTaxIncluded = bookPrice;

                                $('#add_to_cart').removeClass('disallow_reservation');

                            } else if (bestkit_booking.range_type == 'datetime_fromto') {
								if (typeof selectedDates[1] == 'undefined') {
									selectedDates[1] = selectedDates[0];
								}
	
								var date_to = dateFormat(new Date(selectedDates[1]));
                            	timesUpdating(date_from, date_to);
                            } else {
                                booking.find('.selected-dates').hide();
                                if (bestkit_booking.range_type == 'datetime_fromto') {
                                    booking.find('.time_fromto_container').html(time_fromto_container);
                                } else {
                                    $('#add_to_cart').addClass('disallow_reservation');
                                }
                            }
                        } else {
                            selectFlag = false;
                            booking.find('.selected-dates').hide();
                        }

                        disableFlag = false;
                        booking.find('#bestkit_booking_date1').datepicker('refresh');
                    }
                });
            }


            //TIME RAGE OF DATE!!!
            if (bestkit_booking.range_type == 'time_fromto') {
                var time_fromto_date = 0;

                var isAllowedDay = function(date){
                	return isExcludedDay(date);
                }

                booking.find('#bestkit_booking_date1').datepicker({
                    dateFormat: format,
                    minDate: 0,
                    minDate: bestkit_booking.date_from, //0
                    //maxDate: bestkit_booking.max_day,
                    maxDate: bestkit_booking.date_to,
                    gotoCurrent: false,
                    beforeShowDay: function(date){
                        if (isAllowedDay(date)) {
                            return [true, ""];
                        }

                        return [false, ''];
                    },
                    onSelect: function(dateStr, instance){
                        var time = Date.parse(dateStr);
                        time_fromto_date = time;
                        booking.find('#booking_date').text(dateFormat(new Date(time)));
                        var date = $('#booking_date').text();
                        timesUpdating(date, '');
                    }
                });

                booking.find('.check_selected_params a').live('click', function(){
                    checkReservation(
                        [time_fromto_date, $('#booking_from_hour').val(), ($('#booking_from_minute').val() || 0)],
                        [time_fromto_date, $('#booking_to_hour').val(), ($('#booking_to_minute').val() || 0)]
                    );
                });
            }

            //DATE TIME!!!
            if (bestkit_booking.range_type == 'datetime_fromto') {
                var time_fromto_container = booking.find('.time_fromto_container').html();
                $('#timesPreview a.hour_details_view').live('click', function(){
                    $(this).parents('table:first').find('a.hour_details_view').removeClass('active');
                    //$('#timesPreview .hour_details_preview').hide();
                    $(this).addClass('active');
                    $(this).parents('table:first').next().html($(this).next().html()).show();
                });

                booking.find('.check_selected_params a').live('click', function(){
                    checkReservation(
                        [selectedDates[0], $('#booking_from_hour').val(), ($('#booking_from_minute').val() || 0)],
                        [selectedDates[1], $('#booking_to_hour').val(), ($('#booking_to_minute').val() || 0)]
                    );
                });
            }

            timeOffset = bestkit_booking.current_day - booking.find('#bestkit_booking_date1').datepicker('getDate').getTime();
            
            if (bestkit_booking.range_type == 'datetime_fromto' || bestkit_booking.range_type == 'time_fromto') {
                $('#booking_from_hour, #booking_from_minute, #booking_to_hour, #booking_to_minute').live('change', function(){
                	var interval = bestkit_booking.interval;
                	if (interval < 1) {
	                	interval = 1;
                	}

                	if (bestkit_booking.billing_type == 'hours') {
	                	interval *= 60;
                	}

                	var fromTimestamp = parseInt($('#booking_from_hour').val()) * 60;
                	fromTimestamp += parseInt($('#booking_from_minute').val());

					var minToTimestamp = fromTimestamp + interval;
					var minToHours = minToTimestamp / 60;
					var minToMinutes = parseInt(((minToHours - parseInt(minToHours)) * 60).toFixed(0));
					minToHours = parseInt(minToHours);

					if (bestkit_booking.range_type == 'time_fromto') {
						if ($(this).attr('id') == 'booking_from_hour') {
							$('#booking_to_hour').val(minToHours);
		            		$('#booking_to_hour option').each(function(){
		                		if (parseInt($(this).val()) < minToHours) {
			                		$(this).attr('disabled', true);
					            } else {
						            $(this).attr('disabled', false);
					            }
		            		});
		            	}
	
						if ($(this).attr('id') == 'booking_from_minute') {
							if ($('#booking_to_hour').val() == null) {
								$('#booking_to_minute').val(null);
							} else {
								$('#booking_to_minute').val(minToMinutes);
							}
		
		            		$('#booking_to_minute option').each(function(){
		                		if (parseInt($(this).val()) < minToMinutes || $('#booking_to_hour').val() == null) {
			                		$(this).attr('disabled', true);
					            } else {
						            $(this).attr('disabled', false);
					            }
		            		});
		            	}
		            	
		            	if ($(this).attr('id') == 'booking_to_hour') {
		            		if (parseInt($(this).val()) > minToHours) {
		            			$('#booking_to_minute').val(0);
				            	$('#booking_to_minute option').each(function(){
							    	$(this).attr('disabled', false);
			            		});
		            		} else {
			            		$('#booking_to_minute option').each(function(){
			            			$('#booking_to_minute').val(minToMinutes);
			                		if (parseInt($(this).val()) < minToMinutes || $('#booking_to_hour').val() == null) {
				                		$(this).attr('disabled', true);
						            } else {
							            $(this).attr('disabled', false);
						            }
			            		});
		            		}
		            		
		            		if ($(this).val() == $('#booking_to_hour option:last').val()) {
								$('#booking_to_minute option:gt(0)').attr('disabled', true);
							} else {
								$('#booking_to_minute option').attr('disabled', false);
							}
		            	}
		            }

					var maxFromHour = parseInt(interval / 60);
	            	var maxFromMinute = parseInt(((interval / 60 - maxFromHour) * 60).toFixed(0));
	            	$('#booking_from_hour option:gt(-'+(maxFromHour+1)+')').attr('disabled', true);

	            	if ($(this).attr('id') == 'booking_from_hour') {
		            	if ($(this).val() == $('#booking_from_hour option:enabled:last').val()) {
		            		$('#booking_from_minute').val(0);
			            	for (var min = 1; min < 60 - maxFromMinute; min++) {
				            	$('#booking_from_minute option[value="'+min+'"]').attr('disabled', true);
			            	}
		            	} else {
			            	$('#booking_from_minute option').attr('disabled', false);
		            	}
	            	}

					if ($('#booking_to_hour').val() == $('#booking_to_hour option:last').val()) {
						$('#booking_to_minute').val(0);
						$('#booking_to_minute option:gt(0)').attr('disabled', true);
					} else {
						if (bestkit_booking.range_type == 'datetime_fromto') {
							$('#booking_to_minute option').attr('disabled', false);
						}
					}
                });
            }

            prepareTimeInterval = function(){
	            $('#booking_from_hour').change();
            }
        }

		//Add to cart action:
		$(document).ajaxSend(function(event, jqxhr, settings ){ 
			if (typeof settings.data != 'undefined' && settings.data.indexOf('controller=cart&add=1') != -1) {
				var from = false;
				var to = false;

				if (bestkit_booking.range_type == 'date_fromto') {
					if (selectedDates.length == 2) {
						from = selectedDates[0];
						to = selectedDates[1];
					}
				}

				if (bestkit_booking.range_type == 'time_fromto') {
					from = booking.find('#bestkit_booking_date1').datepicker('getDate').getTime();
					to = from;

					from += ( parseInt($('#booking_from_hour').val()) * 60 + parseInt($('#booking_from_minute').val()) ) * 60 * 1000;
					to += ( parseInt($('#booking_to_hour').val()) * 60 + parseInt($('#booking_to_minute').val()) ) * 60 * 1000;
				}

				if (bestkit_booking.range_type == 'datetime_fromto') {
					if (selectedDates.length == 2) {
						from = selectedDates[0];
						to = selectedDates[1];

						from += ( parseInt($('#booking_from_hour').val()) * 60 + parseInt($('#booking_from_minute').val()) ) * 60 * 1000;
						to += ( parseInt($('#booking_to_hour').val()) * 60 + parseInt($('#booking_to_minute').val()) ) * 60 * 1000;
					}
				}

				if (!from || !to) {
					jqxhr.abort();
					$.fancybox.close();
					fancyMsgBox('Please choose From and To times!', 'Booking warning');
					return;
				}

				settings.data += '&bestkit_booking=1&from=' + from + '&to=' + to;
			}
		});
    });
})(jQuery);
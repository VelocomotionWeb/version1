/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function(){
	$("#quantity_wanted_p span").click(function(){
		//alert($(this).html());
		//updatequantity($('#quantity_wanted').val());
		var l_type_loc = '';
		$('[name=type_loc]:checked').each(function(){
			l_type_loc += $(this).attr('id') + ',';
		});
		$('#date_fin').val(addDaysToDate($('#date_depart').val(),$('#quantity_wanted').val()*1));
		link = $('#name_shop').html();
		$.ajax({
			url: '/module/marketplace/shopcollection?mp_shop_name='+link+'&quantite='+$('#quantity_wanted').val()+'&date_depart='+$('#date_depart').val()+'&date_fin='+$('#date_fin').val()+'&date_fin='+$('#date_fin').val()+'&l_type_loc='+l_type_loc+'&content_only=1',
			/*data: {
				id_product: id_product,
				is_all_product: is_all_product
			},*/
			success: function(result)
			{
				$("#wrapper_content_center").html(result);
			}
		});
		function addDaysToDate(old_date, delta_days)
		{
		   // Date plus plus quelques jours
		   var split_date = old_date.split('/');
		   // Les mois vont de 0 a 11 donc on enleve 1, cast avec *1
		   var new_date = new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1 + delta_days);
		   var new_day = new_date.getDate();
			   new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un zéro devant pour la forme
		   var new_month = new_date.getMonth() + 1;
			   new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un zéro devant pour la forme
		   var new_year = new_date.getYear();
			   new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
		   var new_date_text = new_day + '/' + new_month + '/' + new_year;
		   return new_date_text;
		}

	});
});

var last_id_store = 0;
// Retrocompatibility with 1.4
if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
	baseUri = baseDir;
function addcart(idProduct, n, quantity)
{
	//if()id_store
	setTimeout(function() {
		id = $("#quantity_"+idProduct+" #quantity option[value="+n+"]").attr('id');
		ajaxCart.add(id, 0, false, this, $('#quantity_wanted').val(), null)
	}, 1000 * n);

}
//JS Object : update the cart by ajax actions
var ajaxCart = {
	nb_total_products: 0,

	//override every button in the page in relation to the cart
	overrideButtonsInThePage : function(){
		//for every 'add' buttons...
		$('.ajax_add_to_cart_button').unbind('click').click(function(){

			var idProduct =  parseInt($(this).data('id-product'));
			var idProductAttribute =  $('#idCombination_'+idProduct+'').val();
			if ($(this).attr('disabled') != 'disabled') {
				$(this).attr('disabled',true);
				$(this).removeClass('checked');
				$(this).addClass('checking');
				nb = $("#quantity_"+idProduct+" #quantity").val();
				// enregistrement des produits du meme nom
				for (n=1; n<=nb; n++)
				{
					//alert(id);
					addcart(idProduct, n, nb);
					/*
					setTimeout(function() {
						id = $("#quantity_"+idProduct+" #quantity option[value="+n+"]").attr('id');
						ajaxCart.add(id, idProductAttribute, false, this, $('#quantity_wanted').val(), null)
					}, 1000 * quantity);
					*/
				}
				$("#loading").fadeIn("slow");
				setTimeout(function() {
					$("#loading").fadeOut("slow");
					parent.$.fancybox({
						openEffect: 'fade',
						closeEffect: 'fade',
						width: 800,
						height: 410,
						autoSize : true,
						maxWidth : '100%',
						/*closeBtn : false,
						helpers:  {
							overlay : {
								closeClick : false
							}
						},*/
						content: '<p>Location ajoutée au panier<p><a href="javascript:void(0)" onclick="$.fancybox.close();" class="btn btn-default">Continuer vos réservations</a><p><a href="/commande-rapide" target="_blank" class="btn btn-default">Accéder au panier</a></p>',
						afterClose: function () {
							$(".see_prod[id="+id_store+"]").click();
						}
					});
					//alert($('#message_confirm_delete').html());
					//setTimeout(function() {
						//$(".see_prod[id="+id_store+"]").click();
					//}, 1000 * nb);
				}, 1000 * nb);
			}
			return false;
		});
		//for product page 'add' button...
		$('#add_to_cart').unbind('click').click(function(){
			$(this).removeClass('checked');
			$(this).addClass('checking');
			ajaxCart.add( $('#product_page_product_id').val(), $('#idCombination').val(), true, null, $('#quantity_wanted').val(), null);
			return false;
		});

		//for 'delete' buttons in the cart block...
		$('#cart_block_list .ajax_cart_block_remove_link').unbind('click').click(function(){
			// Customized product management
			var customizationId = 0;
			var productId = 0;
			var productAttributeId = 0;
			var customizableProductDiv = $($(this).parent().parent()).find("div[id^=deleteCustomizableProduct_]");

			if (customizableProductDiv && $(customizableProductDiv).length)
			{
				$(customizableProductDiv).each(function(){
					var ids = $(this).attr('id').split('_');
					if (typeof(ids[1]) != 'undefined')
					{
						customizationId = parseInt(ids[1]);
						productId = parseInt(ids[2]);
						if (typeof(ids[3]) != 'undefined')
							productAttributeId = parseInt(ids[3]);
						return false;
					}
				});
			}

			// Common product management
			if (!customizationId)
			{
				//retrieve idProduct and idCombination from the displayed product in the block cart
				var firstCut = $(this).parent().parent().attr('id').replace('cart_block_product_', '');
				firstCut = firstCut.replace('deleteCustomizableProduct_', '');
				ids = firstCut.split('_');
				productId = parseInt(ids[0]);
				if (typeof(ids[1]) != 'undefined')
					productAttributeId = parseInt(ids[1]);
			}

			var idAddressDelivery = $(this).parent().parent().attr('id').match(/.*_\d+_\d+_(\d+)/)[1];

			// Removing product from the cart
			ajaxCart.remove(productId, productAttributeId, customizationId, idAddressDelivery);
			return false;
		});
	},

	// Fix display when using back and previous browsers buttons
	refresh : function(){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&ajax=true&token=' + static_token,
			success: function(jsonData)
			{
				ajaxCart.updateCart(jsonData);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	},

	// Update the cart information
	updateCartInformation : function (jsonData, addedFromProductPage)
	{
		ajaxCart.updateCart(jsonData);

		//reactive the button when adding has finished
		if (addedFromProductPage)
			$('#add_to_cart').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');
		/*else
			$('.ajax_add_to_cart_button').removeAttr('disabled');*/
	},
	// close fancybox
	updateFancyBox : function (){},
	// add a product in the cart via ajax
	add : function(idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist){
		if (addedFromProductPage && !checkCustomizations())
		{
			/*if (contentOnly)
			{
				var productUrl = window.document.location.href + '';
				var data = productUrl.replace('content_only=1', '');
				window.parent.document.location.href = data;
				return;
			}*/
			if (!!$.prototype.fancybox)
			    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + fieldRequired + '</p>'
			        }
			    ], {
			        padding: 0
			    });
			else
			    alert(fieldRequired);
            return;
		}
		emptyCustomizations();
		//disabled the button when adding to not double add if user double click
		if (addedFromProductPage)
		{
			$('#add_to_cart').attr('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
			$('.filled').removeClass('filled');
		}
		else
			$(callerElement).attr('disabled', true);

		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",

			/* AJOUTER PAR SRDEV - envoi la date de depart pour custom field  ' + ((quantity && quantity != null) ? quantity : '1') + '*/
			data: 'controller=cart&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + idProduct + '&date_depart=' + $('#date_depart').val() + '&token=' + static_token + ( (parseInt(idCombination) && idCombination != null) ? '&ipa=' + parseInt(idCombination): ''),

			success: function(jsonData,textStatus,jqXHR)

			{

				// add appliance to wishlist module

				if (wishlist && !jsonData.errors)

					WishlistAddProductCart(wishlist[0], idProduct, idCombination, wishlist[1]);

				if (!jsonData.hasError)

				{

					if (contentOnly)

						window.parent.ajaxCart.updateCartInformation(jsonData, addedFromProductPage);

					else

						ajaxCart.updateCartInformation(jsonData, addedFromProductPage);



					if (jsonData.crossSelling)

						$('.crossseling').html(jsonData.crossSelling);



					if (contentOnly)

						parent.$.fancybox.close();

				}

				else

				{

					if (contentOnly)

						window.parent.ajaxCart.updateCart(jsonData);

					else

						ajaxCart.updateCart(jsonData);

					if (addedFromProductPage)

						$('#add_to_cart button').removeProp('disabled').removeClass('disabled');

					else

						$(callerElement).removeProp('disabled');

				}

				$(callerElement).removeClass('checking');

				$(callerElement).addClass('checked');

				if (addedFromProductPage)

					$('#add_to_cart').removeClass('checking').addClass('checked');

				window.setTimeout( function() {$(callerElement).removeClass('checked');}, 3000 );

				ajaxCart.updateCartInformation(jsonData, addedFromProductPage);

			},

			error: function(XMLHttpRequest, textStatus, errorThrown)

			{

				/*alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);

				//reactive the button when adding has finished

				if (addedFromProductPage)

					$('#add_to_cart').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');

				else

					$(callerElement).removeAttr('disabled');*/

			}

		});

	},

	//remove a product from the cart via ajax

	remove : function(idProduct, idCombination, customizationId, idAddressDelivery){

		//send the ajax request to the server

		$.ajax({

			type: 'POST',

			headers: { "cache-control": "no-cache" },

			url: baseUri + '?rand=' + new Date().getTime(),

			async: true,

			cache: false,

			dataType : "json",

			data: 'controller=cart&delete=1&id_product=' + idProduct + '&ipa=' + ((idCombination != null && parseInt(idCombination)) ? idCombination : '') + ((customizationId && customizationId != null) ? '&id_customization=' + customizationId : '') + '&id_address_delivery=' + idAddressDelivery + '&token=' + static_token + '&ajax=true',

			success: function(jsonData)	{

				ajaxCart.updateCart(jsonData);

				if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')

					deleteProductFromSummary(idProduct+'_'+idCombination+'_'+customizationId+'_'+idAddressDelivery);

			},

			error: function() {alert('ERROR: unable to delete the product');}

		});

	},

	//hide the products displayed in the page but no more in the json data

	hideOldProducts : function(jsonData) {

		//delete an eventually removed product of the displayed cart (only if cart is not empty!)

		if ($('#cart_block_list li').length > 0)

		{

			var removedProductId = null;

			var removedProductData = null;

			var removedProductDomId = null;

			//look for a product to delete...

			$('#cart_block_list li').each(function(){

				//retrieve idProduct and idCombination from the displayed product in the block cart

				var domIdProduct = $(this).attr('id');

				var firstCut =  domIdProduct.replace('cart_block_product_', '');

				var ids = firstCut.split('_');



				//try to know if the current product is still in the new list

				var stayInTheCart = false;

				for (aProduct in jsonData.products)

				{

					//we've called the variable aProduct because IE6 bug if this variable is called product

					//if product has attributes

					if (jsonData.products[aProduct]['id'] == ids[0] && (!ids[1] || jsonData.products[aProduct]['idCombination'] == ids[1]))

					{

						stayInTheCart = true;

						// update the product customization display (when the product is still in the cart)

						ajaxCart.hideOldProductCustomizations(jsonData.products[aProduct], domIdProduct);

					}

				}

				//remove product if it's no more in the cart

				if (!stayInTheCart)

				{

					removedProductId = $(this).attr('id');

					if (removedProductId != null)

					{

						var firstCut =  removedProductId.replace('cart_block_product_', '');

						var ids = firstCut.split('_');



						$('#'+removedProductId).addClass('strike').fadeTo('slow', 0, function(){

							$(this).slideUp('slow', function(){

								$(this).remove();

								// If the cart is now empty, show the 'no product in the cart' message and close detail

								if($('#cart_block ul.products li').length == 0)

								{

									$('#cart_block_no_products:hidden').slideDown(450);

									$('#cart_block li').remove();

								}

							});

						});

						$('#cart_block_combination_of_' + ids[0] + (ids[1] ? '_'+ids[1] : '') + (ids[2] ? '_'+ids[2] : '')).fadeTo('fast', 0, function(){

							$(this).slideUp('fast', function(){

								$(this).remove();

							});

						});

					}

				}

			});

		}

	},

	hideOldProductCustomizations : function (product, domIdProduct)
	{

		var customizationList = $('#customization_' + product['id'] + '_' + product['idCombination']);

		if(customizationList.length > 0)

		{

			$(customizationList).find("li").each(function(){

				$(this).find("div").each(function() {

					var customizationDiv = $(this).attr('id');

					var tmp = customizationDiv.replace('deleteCustomizableProduct_', '');

					var ids = tmp.split('_');

					if ((parseInt(product.idCombination) == parseInt(ids[2])) && !ajaxCart.doesCustomizationStillExist(product, ids[0]))

						$('#' + customizationDiv).parent().addClass('strike').fadeTo('slow', 0, function(){

							$(this).slideUp('slow');

							$(this).remove();

						});

				});

			});

		}



		var removeLinks = $('#' + domIdProduct).find('.ajax_cart_block_remove_link');

		if (!product.hasCustomizedDatas && !removeLinks.length)

			$('#' + domIdProduct + ' span.remove_link').html('<a class="ajax_cart_block_remove_link" rel="nofollow" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + product['id'] + '&amp;ipa=' + product['idCombination'] + '&amp;token=' + static_token + '"> </a>');

		if (product.is_gift)

			$('#' + domIdProduct + ' span.remove_link').html('');

	},

	doesCustomizationStillExist : function (product, customizationId)
	{

		var exists = false;



		$(product.customizedDatas).each(function() {

			if (this.customizationId == customizationId)

			{

				exists = true;

				// This return does not mean that we found nothing but simply break the loop

				return false;

			}

		});

		return (exists);

	},
	//refresh display of vouchers (needed for vouchers in % of the total)

	refreshVouchers : function (jsonData) {
		if (typeof(jsonData.discounts) == 'undefined' || jsonData.discounts.length == 0)

			$('#vouchers').hide();

		else

		{

			$('#vouchers tbody').html('');

			for (i=0; i < jsonData.discounts.length; i++)

			{

				if (parseFloat(jsonData.discounts[i].price_float) > 0)

				{

					var delete_link = '';

					if (jsonData.discounts[i].code.length)

						delete_link = '<a class="delete_voucher" href="'+jsonData.discounts[i].link+'" title="'+delete_txt+'"><img src="'+img_dir+'icon/delete.gif" alt="'+delete_txt+'" class="icon" /></a>';

					$('#vouchers tbody').append($(

						'<tr class="bloc_cart_voucher" id="bloc_cart_voucher_'+jsonData.discounts[i].id+'">'

						+ '<td class="quantity">1x</td>'

						+ '<td class="name" title="'+jsonData.discounts[i].description+'">'+jsonData.discounts[i].name+'</td>'

						+ '<td class="price">-'+jsonData.discounts[i].price+'</td>'

						+ '<td class="delete">' + delete_link + '</td>'

						+ '</tr>'

					));

				}

			}

			$('#vouchers').show();

		}



	},
	// Update product quantity
	updateProductQuantity : function (product, quantity) {

		$('#cart_block_product_' + product.id + '_' + (product.idCombination ? product.idCombination : '0')+ '_' + (product.idAddressDelivery ? product.idAddressDelivery : '0') + ' .quantity').fadeTo('fast', 0, function() {

			$(this).text(quantity);

			$(this).fadeTo('fast', 1, function(){

				$(this).fadeTo('fast', 0, function(){

					$(this).fadeTo('fast', 1, function(){

						$(this).fadeTo('fast', 0, function(){

							$(this).fadeTo('fast', 1);

						});

					});

				});

			});

		});

	},





	//display the products witch are in json data but not already displayed

	displayNewProducts : function(jsonData) {



		//add every new products or update displaying of every updated products

		$(jsonData.products).each(function(){

			//fix ie6 bug (one more item 'undefined' in IE6)

			if (this.id != undefined)

			{

				//create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)



				if ($('#cart_block ul.products').length == 0)

				{

					$('#cart_block_no_products').before('<ul class="products"></ul>');

					$('#cart_block_no_products').hide();

				}

				//if product is not in the displayed cart, add a new product's line

				var domIdProduct = this.id + '_' + (this.idCombination ? this.idCombination : '0') + '_' + (this.idAddressDelivery ? this.idAddressDelivery : '0');

				var domIdProductAttribute = this.id + '_' + (this.idCombination ? this.idCombination : '0');

				if ($('#cart_block_product_'+ domIdProduct).length == 0)

				{

					var productId = parseInt(this.id);

					var productAttributeId = (this.hasAttributes ? parseInt(this.attributes) : 0);

					var content =  '<li class="hidden" id="cart_block_product_' + domIdProduct + '">';

					content += '<a class="preview-image" href="' + this.link + '" title="' + name + '"><img  src="' + this.image_cart + '" alt="' + this.name +'"></a>';

					content += '<div class="description">';

					var name = $('<span />').html(this.name).text();

					name = (name.length > 12 ? name.substring(0, 10) + '' : name);

					content += '<a href="' + this.link + '" title="' + this.name + '">' + name + '</a>';

					if (typeof(freeProductTranslation) != 'undefined')

						content += '<div class="price-quantity"><span class="price">' + (parseFloat(this.price_float) > 0 ? this.priceByLine : freeProductTranslation) + '</span>';

					content += '</div>';

					content += '</div>';



					if (typeof(this.is_gift) == 'undefined' || this.is_gift == 0)

						content += '<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;token=' + static_token + (this.hasAttributes ? '&amp;ipa=' + parseInt(this.idCombination) : '') + '"><span class="fa fa-times"></span></a></span>';

					else

						content += '<span class="remove_link"></span>';

					content += '</li>';



					$('#cart_block ul.products').append(content);

				}

				//else update the product's line

				else

				{

					var jsonProduct = this;

					if($.trim($('#cart_block_product_' + domIdProduct + ' .quantity').html()) != jsonProduct.quantity || $.trim($('#cart_block_product_' + domIdProduct + ' .price').html()) != jsonProduct.priceByLine)

					{

						// Usual product

						if (!this.is_gift)

							$('#cart_block_product_' + domIdProduct + ' .price').text(jsonProduct.priceByLine);

						else

							$('#cart_block_product_' + domIdProduct + ' .price').html(freeProductTranslation);

						ajaxCart.updateProductQuantity(jsonProduct, jsonProduct.quantity);



						// Customized product

						if (jsonProduct.hasCustomizedDatas)

						{

							customizationFormatedDatas = ajaxCart.displayNewCustomizedDatas(jsonProduct);

							if (!$('#customization_' + domIdProductAttribute).length)

							{

								if (jsonProduct.hasAttributes)

									$('#cart_block_combination_of_' + domIdProduct).append(customizationFormatedDatas);

								else

									$('#cart_block dl.products').append(customizationFormatedDatas);

							}

							else

							{

								$('#customization_' + domIdProductAttribute).html('');

								$('#customization_' + domIdProductAttribute).append(customizationFormatedDatas);

							}

						}

					}

				}

				$('#cart_block ul.products .hidden').slideDown(450).removeClass('hidden');



			var removeLinks = $('#cart_block_product_' + domIdProduct).find('a.ajax_cart_block_remove_link');

			if (this.hasCustomizedDatas && removeLinks.length)

				$(removeLinks).each(function() {

					$(this).remove();

				});

			}

		});

	},



	displayNewCustomizedDatas : function(product)

	{

		var content = '';

		var productId = parseInt(product.id);

		var productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);

		var hasAlreadyCustomizations = $('#customization_' + productId + '_' + productAttributeId).length;



		if (!hasAlreadyCustomizations)

		{

			if (!product.hasAttributes)

				content += '<dd id="cart_block_combination_of_' + productId + '" class="hidden">';

			if ($('#customization_' + productId + '_' + productAttributeId).val() == undefined)

				content += '<ul class="cart_block_customizations" id="customization_' + productId + '_' + productAttributeId + '">';

		}



		$(product.customizedDatas).each(function()

		{

			var done = 0;

			customizationId = parseInt(this.customizationId);

			productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);

			content += '<li name="customization"><div class="deleteCustomizableProduct" id="deleteCustomizableProduct_' + customizationId + '_' + productId + '_' + (productAttributeId ?  productAttributeId : '0') + '"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;ipa=' + productAttributeId + '&amp;id_customization=' + customizationId + '&amp;token=' + static_token + '"></a></div><span class="quantity-formated"><span class="quantity">' + parseInt(this.quantity) + '</span>x</span>';



			// Give to the customized product the first textfield value as name

			$(this.datas).each(function(){

				if (this['type'] == CUSTOMIZE_TEXTFIELD)

				{

					$(this.datas).each(function(){

						if (this['index'] == 0)

						{

							content += ' ' + this.truncatedValue.replace(/<br \/>/g, ' ');

							done = 1;

							return false;

						}

					})

				}

			});



			// If the customized product did not have any textfield, it will have the customizationId as name

			if (!done)

				content += customizationIdMessage + customizationId;

			if (!hasAlreadyCustomizations) content += '</li>';

			// Field cleaning

			if (customizationId)

			{

				$('#uploadable_files li div.customizationUploadBrowse img').remove();

				$('#text_fields input').attr('value', '');

			}

		});



		if (!hasAlreadyCustomizations)

		{

			content += '</ul>';

			if (!product.hasAttributes) content += '</dd>';

		}

		return (content);

	},





	//genarally update the display of the cart

	updateCart : function(jsonData) {

		//user errors display

		if (jsonData.hasError)

		{

			var errors = '';

			for (error in jsonData.errors)

				//IE6 bug fix

				if (error != 'indexOf')

					errors += $('<div />').html(jsonData.errors[error]).text() + "\n";

			alert(errors);

		}

		else

		{

			ajaxCart.updateCartEverywhere(jsonData);

			ajaxCart.hideOldProducts(jsonData);

			ajaxCart.displayNewProducts(jsonData);

			ajaxCart.refreshVouchers(jsonData);



			//update 'first' and 'last' item classes

			$('#cart_block .products li').removeClass('first_item').removeClass('last_item').removeClass('item');

			$('#cart_block .products li:first').addClass('first_item');

			$('#cart_block .products li:not(:first,:last)').addClass('item');

			$('#cart_block .products li:last').addClass('last_item');



			//reset the onlick events in relation to the cart block (it allow to bind the onclick event to the new 'delete' buttons added)

			ajaxCart.overrideButtonsInThePage();

		}

	},



	//update general cart informations everywhere in the page

	updateCartEverywhere : function(jsonData) {

		$('.ajax_cart_total').text($.trim(jsonData.productTotal));



		if (parseFloat(jsonData.shippingCostFloat) > 0 || jsonData.nbTotalProducts < 1)

			$('.ajax_cart_shipping_cost').text(jsonData.shippingCost);

		else if (typeof(freeShippingTranslation) != 'undefined')

				$('.ajax_cart_shipping_cost').html(freeShippingTranslation);

		$('.ajax_cart_tax_cost').text(jsonData.taxCost);

		$('.cart_block_wrapping_cost').text(jsonData.wrappingCost);

		$('.ajax_block_cart_total').text(jsonData.total);



		this.nb_total_products = jsonData.nbTotalProducts;



		if (parseInt(jsonData.nbTotalProducts) > 0)

		{

			$('.ajax_cart_no_product').hide();

			$('.ajax_cart_quantity').text(jsonData.nbTotalProducts);

			$('.ajax_cart_quantity').fadeIn('slow');

			$('.ajax_cart_total').fadeIn('slow');



			if (parseInt(jsonData.nbTotalProducts) > 1)

			{

				$('.ajax_cart_product_txt').each( function () {

					$(this).hide();

				});



				$('.ajax_cart_product_txt_s').each( function () {

					$(this).show();

				});

			}

			else

			{

				$('.ajax_cart_product_txt').each( function () {

					$(this).show();

				});



				$('.ajax_cart_product_txt_s').each( function () {

					$(this).hide();

				});

			}

		}

		else

		{

			$('.ajax_cart_quantity').text(jsonData.nbTotalProducts);

			$('.ajax_cart_product_txt_s, .ajax_cart_product_txt, .ajax_cart_total').each(function(){

				$(this).hide();

			});

			$('.ajax_cart_no_product').show('slow');

		}

	}

};



function HoverWatcher(selector){

	this.hovering = false;

	var self = this;



	this.isHoveringOver = function() {

		return self.hovering;

	}



	$(selector).hover(function() {

		self.hovering = true;

	}, function() {

		self.hovering = false;

	})

}



//when document is loaded...

$(document).ready(function(){

	ajaxCart.overrideButtonsInThePage();



	var cart_qty = 0;

	var current_timestamp = parseInt(new Date().getTime() / 1000);



	if (typeof $('.ajax_cart_quantity').html() == 'undefined' || (typeof generated_date != 'undefined' && generated_date != null && (parseInt(generated_date) + 30) < current_timestamp))

		ajaxCart.refresh();

	else

		cart_qty = parseInt($('.ajax_cart_quantity').html());



	/* roll over cart */

	var cart_block = new HoverWatcher('#header #cart_block');

	var shopping_cart = new HoverWatcher('#shopping_cart');



	$('.delete_voucher').live('click', function() {

		$.ajax({

			type: 'POST',

			headers: { "cache-control": "no-cache" },

			async: true,

			cache: false,

			url:$(this).attr('href') + '?rand=' + new Date().getTime(),

			error: function(XMLHttpRequest, textStatus, errorThrown) {

				alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);

			}

		});

		$(this).parent().parent().remove();

		if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')

		{

			if (typeof(updateAddressSelection) != 'undefined')

				updateAddressSelection();

			else

				location.reload();

		}

		return false;

	});



	$('#cart_navigation input').click(function(){

		$(this).attr('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');

		$(this).closest("form").get(0).submit();

	});

});
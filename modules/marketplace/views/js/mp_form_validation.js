$(document).ready(function(){
	//Add product and update product form validation
	$('#SubmitProduct,#SubmitCreate').on("click", function(){
		var product_name = $('#product_name').val().trim();
		var short_description = $('#short_description').val().length;
		var product_description = $('#product_description').val();
		var product_price = $('#product_price').val().trim();
		var product_quantity = $('#product_quantity').val().trim();
		var checkbox_length = $('.product_category:checked').length;
		var special_char = /^[^<>;=#{}]*$/;

		if(product_name == '')
		{
			alert(req_prod_name);
			$('#product_name').focus();
			return false;
		}
		else if(!isNaN(product_name) || !special_char.test(product_name))
		{
			alert(char_prod_name);
			$('#product_name').focus();
			return false;
		}
		else if(product_price == '')
		{
			alert(req_price);
			$('#product_price').focus();
			return false;
		}
		else if(isNaN(product_price))
		{
			alert(num_price);
			$('#product_price').focus();
			return false;
		}
		else if(product_quantity == '')
		{
			alert(req_qty);
			$('#product_quantity').focus();
			return false;
		}
		else if(isNaN(product_quantity))
		{
			alert(num_qty);
			$('#product_quantity').focus();
			return false;
		}
		else if(checkbox_length == 0)
		{
			alert(req_catg);
			$('#check').focus();
			return false;
		}
	});

	if ($('#SubmitProduct,#SubmitCreate,#update_profile').length) //only for add, update product and edit profile page
	{
		// for transalting choose file option in marketplace all pages
		$.uniform.defaults.fileButtonHtml = choosefile_fileButtonHtml;
		$.uniform.defaults.fileDefaultHtml = nofileselect_fileDefaultHtml;
	}

	if ($("#update_profile").length)
		id_seller = $("#id_seller").val();
	
	//Seller registration shop name validation(uniqueness)
	$("#shop_name1").on("blur", function(){
		return checkUniqueShopName($(this).val(), id_seller);
	});

	$("#shop_name1").on("focus", function(){
		$(".wk-msg-shopname").empty();
	});


	//Seller registration email validation(uniqueness)
	$("#business_email_id1").on("blur", function(){
		return checkUniqueSellerEmail($(this).val(), id_seller);
	});

	$("#business_email_id1").on("focus", function(){
		$(".wk-msg-selleremail").empty();
	});

	//Seller registration form validation
	$('#seller_save,#update_profile').on("click", function(e){

		var shop_name = $('#shop_name1').val().trim();
		var person_name = $('#person_name1').val().trim();
		var phone = $('#phone1').val().trim();
		var business_email = $('#business_email_id1').val().trim();
		var terms_and_conditions_checked = $('#terms_and_conditions_checkbox:checked').length;

		if(shop_name == '')
		{
			alert(req_shop_name);
			$('#shop_name1').focus();
			return false;
		}
		else if(!validate_shopname(shop_name))
		{
			alert(inv_shop_name);
			$('#shop_name1').focus();
			return false;
		}
		else if(checkUniqueShopName(shop_name, id_seller))
		{
			alert(shop_name_exist_msg);
			$('#shop_name1').focus();
			return false;
		}
		else if(person_name == '')
		{
			alert(req_seller_name);
			$('#person_name1').focus();
			return false;
		}
		else if(!validate_isName(person_name))
		{
			alert(inv_seller_name);
			$('#person_name1').focus();
			return false;
		}
		else if(phone == '')
		{
			alert(req_phone);
			$('#phone1').focus();
			return false;
		}
		else if(!validate_isPhoneNumber(phone))
		{
			alert(inv_phone);
			$('#phone1').focus();
			return false;
		}
		else if(business_email == '')
		{
			alert(req_email);
			$('#business_email_id1').focus();
			return false;
		}
		else if(!validate_isEmail(business_email))
		{
			alert(inv_email);
			$('#business_email_id1').focus();
			return false;
		}
		else if(checkUniqueSellerEmail(business_email, id_seller))
		{
			alert(seller_email_exist_msg);
			$('#business_email_id1').focus();
			return false;
		}
		else if (terms_and_condition_active == 1)
		{
			if (!terms_and_conditions_checked)
			{
				alert(agree_terms_and_conditions);
				$('#terms_and_conditions').focus();
				return false;
			}
		}
	});

	// payment details link
	$('#submit_payment_details').click(function(){
		var payment_mode = $('#payment_mode').val();
		if(payment_mode == "")
		{
			alert(req_payment_mode)
			$('#payment_mode').focus();
			return false;
		}
	});

	// Image: if image selected
	$(".wk-btn-other-img").on("click", function(e){
		e.preventDefault();
		var cover_img = $("#product_image").val();
		var images = $("#images2").val();
		if (!cover_img)
		{
			alert("Please validate previous image.");
			$('#product_image').focus();
			return false;
		}
		else
			showOtherImage();
	});


	// code only for page where category tree is using
	if ($('#tree1').length)
	{
		//for category tree
		$('#tree1').checkboxTree({
			initializeChecked: 'expanded',
			initializeUnchecked: 'collapsed'
		});
	}

	//Only for update seller profile page
	if ($("#update_profile").length)
	{
		$(document).on("click", ".wk_delete_seller_img", function(e){
			e.preventDefault();
			if (confirm("Are you sure?"))
				deleteSellerImages($(this), 'seller_img');
		});

		$(document).on("click", ".wk_delete_shop_img", function(e){
			e.preventDefault();
			if (confirm("Are you sure?"))
				deleteSellerImages($(this), 'shop_img');
		});
	}

	// Other image div remove event
	$(document).on("click", ".wk_more_img_remove", function(){
		$(this).parent(".wkChildDivClass").remove();
	});
});


var i = 2;
var id_seller;
var shop_name_exist = false;
var seller_email_exist = false;

//code for showing other image upload link
function showOtherImage()
{
    var newdiv = document.createElement('div');
    newdiv.setAttribute("id", "childDiv" + i);
    newdiv.setAttribute("class", "wkChildDivClass");
    newdiv.innerHTML = "<div class='col-md-8'><input type='file' id='images"+i+"' name='images[]'/></div><a class='wk_more_img_remove btn btn-default button button-small'><span>"+img_remove+"</span></a>";
    var ni = document.getElementById('wk_prod_other_images');
    ni.appendChild(newdiv);
    i++;
}

function checkUniqueShopName(shop_name, id_seller)
{
	if (shop_name != "")
	{
		$.ajax({
			url: validate_uniqueness_ajax_url,
			type: "POST",
			data: {
				shop_name: shop_name,
				id_seller: id_seller !== 'undefined' ? id_seller : false,
			},
			async: false,
			success:function(result){
				if (result == 1)
				{
					$(".wk-msg-shopname").html(shop_name_exist_msg);
					shop_name_exist = true;
				}
				else if (result == 2)
				{
					$(".wk-msg-shopname").html(shop_name_error_msg);
					$(".seller_shop_name").addClass('form-error').removeClass('form-ok');
					shop_name_exist = true;
				}
				else
				{
					$(".wk-msg-shopname").empty();
					shop_name_exist = false;
				}
			}
		});
	}
	return shop_name_exist;
}

function checkUniqueSellerEmail(seller_email, id_seller)
{
	if (seller_email != "")
	{
		$.ajax({
			url: validate_uniqueness_ajax_url,
			type: "POST",
			data: {
				seller_email: seller_email,
				id_seller: id_seller !== 'undefined' ? id_seller : false
			},
			async: false,
			success:function(result){
				if (result == 1)
				{
					$(".wk-msg-selleremail").html(seller_email_exist_msg);
					seller_email_exist = true;
				}
				else
				{
					$(".wk-msg-selleremail").empty();
					seller_email_exist = false;
				}
			}
		});
	}
	return seller_email_exist;
}

function deleteSellerImages(t, target)
{
	var id_seller = t.data("id_seller");

	if (target == 'seller_img')
		$(".wk_loader_seller_img_delete").show();
	else
		$(".wk_loader_shop_img_delete").show();

	$.ajax({
		url: editprofile_controller,
		type: 'POST',
		dataType: 'json',
		async: false,
		data: {
			id_seller: id_seller,
			delete_img: target
		},
		success: function(result){
			if (result.status == 'ok')
			{
				if (target == 'seller_img')
				{
					$(".wk_seller_img").attr("src", seller_default_img_path);
					$(".wk_loader_seller_img_delete").hide();
					$(".wk_delete_seller_img").parent().removeClass('wk_hover_img');
					$(".wk_delete_seller_img").remove();
				}
				else if (target == 'shop_img')
				{
					$(".wk_shop_img").attr("src", shop_default_img_path);
					$(".wk_loader_shop_img_delete").hide();
					$(".wk_delete_shop_img").parent().removeClass('wk_hover_img');
					$(".wk_delete_shop_img").remove();
				}
			}
		}
	});
}

function validate_shopname(name)
{
	if (!/^[^<>;=#{}]*$/i.test(name))
		return false;
	else
		return true;
}
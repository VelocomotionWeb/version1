<script type="text/javascript">
 
/* STATUS */
var status;
var token = $( "#status_token" ).html();
var nbOnglet = 0;
var interval = new Array();

$( function()
{
	document.title = 'DDLX live chat';

	$( window ).focus( function()
	{
		status = "online";
	} );

	$( window ).blur( function()
	{
		status = "away";
	} );

	$( window ).unload( function()
	{
		status = "offline";
	} );

	setInterval( "getMessages()", 2000 );

	titleInterval = setInterval( "updateChatNotAnswered()", 1000 );

	$( document ).on( "keypress", ".message", function( event )
	{
		if (event.which == 13 && !event.shiftKey)
		{
			event.preventDefault();
			postMessage( $( this ) );
		}
	} );

	$( document ).on( "click", ".onglet_client", function()
	{
		for (key in interval)
		{
			// console.log( key );
			// console.log( $( this ).attr( 'id' ) );

			if (key == $( this ).attr( 'id' ))
			{
				clearInterval( interval[key] );
				delete interval[key];

				// console.log( interval );

				$( this ).removeClass( "blink" );
			}
		}
		updateChatNotAnswered();
	} );

	$.ionSound( {
		sounds : [ "water_droplet" ],
		path : "{$modulepath}"  + "/sounds/",
		multiPlay : true,
		volume : "1.0"
	} );

	/*
	 * $("#message").keypress(function(event) { if (event.which == 13) {
	 * event.preventDefault(); postMessage(); } });
	 */
	getMessages();
} );

function dump( obj )
{
	var out = '';
	for ( var i in obj)
	{
		out += i + ": " + obj[i] + "\n";
	}
	return out;
}

/* MESSAGE RECEIVING */
function getMessages()
{
	// get all client connected activité < 5min, se fait par l'intermédiaire de
	// getmerchantstatus coté client.
	// On a sauvegardé en BD memory mysql les clients.
	if (token == undefined)
	{
		token = $( "#status_token" ).html();
	}

	$.getJSON( "{$modulepath}"  + "bo/merchantcontroller.php?token=" + token + '&status=' + status, function(
			data )
	{
		var answers;
		if (data != null)
		{
			//console.log( data );
			$.each( data, function( key, val )
			{
				if (key == "token")
				{
					token = val;
				}
				// Gestion des users/onglets
				if (key == "client")
				{
					// liste tous les onglets existants
					onglet = $( ".onglet_client" );

					// pour chaque onglet de la liste si le nom ne se trouve pas
					// dans le JSON alors on supprime l'onglet.
					// ds le json on récupère les noms
					$.each( onglet, function( cle, valeur )// nom ds onglet
					{
						var found = false;

						$.each( val, function( keyosef, value )// nom du JSON
						{
							//console.log( $( valeur ).attr("data") );
							//console.log( value );
							
							if ($( valeur ).attr("data") === value.id_session)
							{
								found = true;
							}
							else if ($( valeur ).attr("data") === value.id_customer)
							{
								found = true;
							}
						} );

						if (!found)
						{
							// enleve onglet et contenu chat !
							// onglet_client_1 => cont_onglet_client_1
							id = $( valeur ).attr( 'id' );
							
							nodeTodel = "cont_onglet_client_" + id.split( "_" ).pop();
							$( "#" + nodeTodel ).remove();
							nodeTodel = "info_onglet_client_" + id.split( "_" ).pop();
							$( "#" + nodeTodel ).remove();
							
							valeur.remove();
							nbOnglet--;
						}
					} );

					// pour chaque nom du JSON, s'il n'est pas dans la liste on
					// le rajoute o DOM
					$.each( val, function( key, value )
					{
						// liste tous les onglets existants
						onglet = $( ".onglet_client" );

						result = onglet.filter( function()
						{
							return ( ($( this ).attr("data") === value.id_session) || ($( this ).attr("data") === value.id_customer));
						} );

						if (result.get() == "")
						{
							// ajout onglet
							addOngletToChat( value );
						}
						else
						{
							// console.log(onglet.get());
						}
					} );
				}
				// gestion messages
				if (key == "messages")
				{
					$.each( val, function( keyosef, value )// value= array
					// message
					{

						$.each( value, function( cle, valeur )
						{
							// trouve le bon onglet:
							var divText;

							if (valeur.from_id_customer != undefined)
							{
								// celui dont l'id est ddlx_id_customer_text
								divText = $( "#ddlx_id_customer_" + valeur.from_id_customer );

							}
							if (valeur.from_id_session != undefined)
							{
								divText = $( "#ddlx_id_session_" + valeur.from_id_session );
							}

							answer = '<span class="triangle-right left in">' + unescapeHtml( valeur.message )
									+ '</span>';

							divText.append( answer );

							// ajoute notification clignotement et son
							$.ionSound.play( "water_droplet" );
							parent = $( divText ).parents( ".contenu_onglet_client" );

							idOfOngletContent = parent.attr( 'id' );
							idOfOnglet = idOfOngletContent.substring( 5, idOfOngletContent.length );

							blinkClientName( $( "#" + idOfOnglet ) );

							// scroll
							divText.scrollTop( divText.height() + 5000000 );
							// console.log(valeur);
						} );// each

					} );// each

				}// if (key == "messages")

			} );// $.each( data, function( key, val )

		}// data !=null

		if (nbOnglet > 0)
		{
			$( '#noclient' ).remove();
		}
		else
		{
			$( '.onglets_client' ).children().remove();
			$( '.onglets_client' ).html( "<div id=\"noclient\"> {l s='No clients connected' mod='ddlx_live_chat'} </div>" );
		}

	} );// getJSON

}

function addOngletToChat( client )
{
	var html_input, html_text, id_message, id_textarea;
	
	if (client.id_session != null)
	{
		html_input = '<input type="hidden" id="ddlx_id_session" value="' + client.id_session + '" >';
		html_text = '<div class="text" id="ddlx_id_session_' + client.id_session + '" >';
		id_message = 'ddlx_to_id_session_' + client.id_session;
		id_textarea = 'id_session_' + client.id_session;
	}
	else
	{
		html_input = '<input type="hidden" id="ddlx_id_customer" value="' + client.id_customer + '" >';
		html_text = '<div class="text" id="ddlx_id_customer_' + client.id_customer + '" >';
		id_message = 'ddlx_to_id_customer_' + client.id_customer;
		id_textarea = 'id_customer_' + client.id_customer ;
	}
	// onglet: onglets_client
	// content : contenu_onglets_client
	nbOnglet++;
	
	$.getJSON( "{$modulepath}"  + "bo/merchantcontroller.php?"
								+ 'id_session=' + client.id_session 
								+ '&id_customer=' + client.id_customer 
								+ '&ip_adress=' + client.ip_adress, function( data )
	{	
		if (data != null)
		{
			clientInfo = data["client"][0];
			addUserInfoToChat(clientInfo, html_input, html_text, id_message, id_textarea  );

			//messages
			addOldMessagesToChat(data["messages"], id_textarea);
		}
	});
	
}

function addUserInfoToChat(clientInfo, html_input, html_text, id_message, id_textarea )
{
	newID = Math.floor( (Math.random() * 10000000) + 1 );

	if( clientInfo.id_customer == null &&  clientInfo.id_session != null )
	{
		name = "{l s='Client' mod='ddlx_live_chat'}" + "_" + clientInfo.id;
		$( ".onglets_client" ).append(
			'<span class="onglet_client tab_client_inactif" id="onglet_client_' + newID + '" data="' + clientInfo.id_session + '">' + name + '</span>' );
	}
	else
	{		
		$( ".onglets_client" ).append(
			'<span class="onglet_client tab_client_inactif" id="onglet_client_' + newID + '" data="' + clientInfo.id_customer + '">' + clientInfo.name
					+ '</span>' );
	}
	
	$( ".contenu_onglets_client" )
			.append(
					'<div class="contenu_onglet_client" id="cont_onglet_client_'
							+ newID
							+ '" style="display: none;">	<table class="chat"><tbody><tr><!-- zone des messages --><td valign="top" id="text-td">'
							+ html_text
							+ '</div></td></tr></tbody></table>'
							+ '<table class="post_message"><tbody><tr><td><div><form action="" method=""><textarea class="message" id="'
							+ id_message + '" ></textarea><input type="hidden" id="chatID" value="' + '22222' + '">'
							+ html_input + '</form></div></td></tr></tbody></table></div>' );

	if  (clientInfo.comment == null || clientInfo.comment == "null")
	{
		clientInfo.comment = "";
	}
		
	$( ".systeme_onglets_client" ).append(
			'<div class="info_client" id="info_onglet_client_' + newID + '">'

			+ "{l s='Comment' mod='ddlx_live_chat'} :<br/>"
			
			+ '<div class="comment" style="width:100%"><textarea id="comment_' + id_textarea + '" style="width:97%;height:130px;max-height: 220px;max-width:97%;"> ' 
			+ clientInfo.comment 
			+ '</textarea><br/><button type="button" class="button-savecomment" data="' + id_textarea + '">' + " {l s='Save' mod='ddlx_live_chat'} " + '</button><div><br/>'
			
			+ '<div class="ip"> IP: ' + clientInfo.ip_adress + '<br/><button type="button" class="button-banip" data="' + clientInfo.ip_adress + '" >' + " {l s='Ban this IP !' mod='ddlx_live_chat'} " + '</button><br/><br/></div>'

			+ '<div class="note">' + '</div>'
			
			+'<div class="browser">' + clientInfo.browser + '</div>'
			
			+ '</div>' );

	
}

$(document.body).on("click", ".button-banip", function() {
	ip = $(this).attr("data");
	displayDialogBan(ip);
});

$(document.body).on("click", ".button-savecomment", function() {
	id_chatter = $(this).attr("data");
	comment = $(this).siblings("textarea");	
	saveComment(id_chatter, comment);
});

function saveComment(id_chatter, comment)
{
	
	$.ajax({
				type: "POST",
				url: "{$modulepath}"  + "bo/merchantcontroller.php",
				data: { token: token, id_chatter: id_chatter,comment: comment.val() }
           })
			.done(
				function( data ) 
				{
					if(data!=null)
					{
						if( data == "1" )
							alert("{l s='Comment saved' mod='ddlx_live_chat'}");
					}
				}
          	);
}

function addOldMessagesToChat( messages, id_textarea ){

	var divText, first=true;

	divText = $( "#ddlx_" + id_textarea );
	
	$.each( messages, function( keyosef, value )// value= array
	// message
	{		
		$.each( value, function( cle, valeur )
		{
			var answer;
			// message to employee
			if (valeur.from_id_employee == undefined)
			{
				answer = '<span class="triangle-right left in">' + unescapeHtml( valeur.message ) + '</span>';
			}
			//message from employee
			else if (valeur.from_id_employee != undefined)
			{
				answer = '<span class="triangle-right right">' + unescapeHtml( valeur.message ) + '</span>';
			}

			if(first)
			{
				divText.append(  "<span class=\"yesterdaychat\" >{l s='Last 24 hours' mod='ddlx_live_chat'}</span>" );
				first = false;
			}
			
			divText.append( answer );

		} );// each
		
	} );// each	

	divText.append( "<span class=\"todaychat\" >{l s='Now' mod='ddlx_live_chat'}</span>" );

	divText.scrollTop( divText.height() + 5000000 );
}

function displayDialogBan(ip){

	$('<div></div>').appendTo('body')
    .html("<div><h6>\"{l s='Are you sure you don\'t want to talk to this client anymore ?' mod='ddlx_live_chat'}\"</h6></div>")
    .dialog(
	{
		modal: true, title: 'Ban IP', zIndex: 10000, autoOpen: true,
		width: 'auto', resizable: false,
		buttons: 
		{
			Yes: function () 
			{
				$.ajax(
				{
					type: "POST",
					url: "{$modulepath}"  + "/bo/merchantcontroller.php",
					data: { token: token, ip: ip, action: 'ban' }
               })
				.done(
					function( data ) 
					{
						if (data != null && data != "")
						{
							data = $.parseJSON( data );

							if(data["result"] == true)
							{
								alert( "{l s='Ban succesfull' mod='ddlx_live_chat'}" );
							}
							else
							{
								alert( "{l s='Impossible to ban' mod='ddlx_live_chat'}" );
							}
							
							if (data["bannedips"] != null)
							{
								replaceBannedList(data["bannedips"]);
							}
						}
					}
              	);

				$(this).dialog("close");
			},
			
			No: function () 
			{
				$(this).dialog("close");
			}
		},
		
		close: function (event, ui)
		{
			$(this).remove();
		}
	});
}

/* MESSAGE SENDING */
function postMessage( SelectFromEvent )
{
	// console.log(SelectFromEvent);
	idOfTextareaSending = $( SelectFromEvent ).attr( 'id' );

	var id;
	var res;
	var is_customer;
	// si message to customer
	if (idOfTextareaSending.indexOf( 'ddlx_to_id_customer_' ) > -1)
	{
		is_customer = true;
		res = idOfTextareaSending.split( "_" )[4];
		id = '&id_customer=' + encodeURIComponent( res );
	}
	else if (idOfTextareaSending.indexOf( 'ddlx_to_id_session_' ) > -1)
	{
		is_customer = false;
		res = idOfTextareaSending.split( "_" )[4];
		id = '&id_session=' + encodeURIComponent( res );
	}

	var message = $( SelectFromEvent ).val().trim();
	var chatID = encodeURIComponent( $( "#chatID" ).val() );

	if (message.length > 0)
	{
		$.ajax( {
			type : "POST",
			url : "{$modulepath}"  + "/bo/merchantcontroller.php",
			data : 'message=' + message + '&chatID=' + chatID + id,
			success : function( msg )
			{
				// addDataToJStorage( 'out', res, message );

				if (msg != '')
				{
				}
				else
				{
					alert( "{l s='An error occured, message not sent.' mod='ddlx_live_chat'}" );
				}

				if (is_customer)
				{
					containerText = $( '#ddlx_id_customer_' + res );

					containerText.append( '<span class="triangle-right right" >' + message + '</span>' );

					containerText.scrollTop( containerText.height() + 5000000 );
				}
				else
				{
					containerText = $( '#ddlx_id_session_' + res );

					containerText.append( '<span class="triangle-right right" >' + message + '</span>' );

					containerText.scrollTop( containerText.height() + 5000000 );
				}

				$( SelectFromEvent ).val( '' );
			},
			error : function( msg )
			{
				// On alerte d'une erreur
				alert( "{l s='AJAX Error, impossible to sent the message.' mod='ddlx_live_chat'}" );
				$( '#message' ).val( $( '#message' ).val() + message );
			}
		} );
		$( "#message" ).val( '' );
	}
}

function blinkClientName( node )
{
	var classList = node.attr( 'class' ).split( /\s+/ );

	$.each( classList, function( index, item )
	{
		if (item == 'tab_client_inactif')
		{
			blink( node );
		}
	} );
}

function blink( divtoblink )
{
	interV = setInterval( function()
	{
		$( divtoblink ).toggleClass( "blink" );

	}, 1000 );

	interval[$( divtoblink ).attr( 'id' )] = interV;

	// Get the size of an object
	updateChatNotAnswered();

}

function updateChatNotAnswered()
{
	size = Object.size( interval );

	if (size == 0)
	{
		document.title = "DDLX Live chat";
	}
	else
	{
		if (document.title == "DDLX Live chat")
		{
			if (size == 1)
			{
				document.title = size + " {l s='chat waiting ' mod='ddlx_live_chat'}";
			}
			else
			{
				document.title = size + " {l s='chats waiting ' mod='ddlx_live_chat'}";
			}
		}
		else
		{
			document.title = "DDLX Live chat";
		}
	}
}

function unescapeHtml( safe )
{
	return safe.replace( /&amp;/g, '&' ).replace( /&lt;/g, '<' ).replace( /&gt;/g, '>' ).replace( /&quot;/g, '"' )
			.replace( /&#039;/g, "'" );
}

$.valHooks.textarea = {
	get : function( elem )
	{
		return elem.value.replace( /\r?\n/g, "<br/>" );
	}
};

Object.size = function( obj )
{
	var size = 0, key;
	for (key in obj)
	{
		if (obj.hasOwnProperty( key ))
			size++;
	}
	return size;
};


 </script>
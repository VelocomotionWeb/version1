<script type="text/javascript">


var token = encodeURIComponent( $( "#token" ).val() );

var window_focus;

$( window ).focus( function()
{
	window_focus = true;
	getMessagesAndStatus();
} ).blur( function()
{
	window_focus = false;
} );

var lastIndex;

// on init do:
$( function()
{
	displayHisto();

	setInterval( "getMessagesAndStatus()", 2000 );
	setInterval( "refreshHisto()", 100 );

	$( "#message" ).keypress( function( event )
	{
		if (event.which == 13 && !event.shiftKey)
		{
			event.preventDefault();
			postMessage();
		}
	} );

	$.cookie( "ddlx_live_chat", 'open', {
		path : '/'
	} );

	$( "#ddlx_live_chat_reduce" ).on( "click", function()
	{
		parent.reduce();
	} );

	$( "#ddlx_live_chat_close" ).on( "click", function()
	{
		$.cookie( "ddlx_live_chat", 'close', {
			path : '/'
		} );
		parent.close();
	} );

	$.ionSound( {
		sounds : [ "water_droplet" ],
		path : "../sounds/",
		multiPlay : true,
		volume : "1.0"
	} );

	window_focus = true;
	getMessagesAndStatus();
} );

function getMessagesAndStatus()
{
	var chatID = encodeURIComponent( $( "#chatID" ).val() );
	
	var id_client = $.cookie("ddlx_live_chat_id");

	if (id_client == undefined || id_client == null || id_client == "undefined" || id_client == "")
	{
		id_client = "";
	}
	
	if (token == undefined || token == null || token == "undefined" || token == "")
	{
		token = encodeURIComponent( $( "#token" ).html() );
	}

	$.ajax( {
		type : "POST",
		url : "{$modulepathclient}"  + "clientcontroller.php",
		data : 'chatID=' + chatID + '&token=' + token + '&id_client=' + id_client,

		success : function( data )
		{
			if (data != null && data != "")
			{
				data = $.parseJSON( data );

				if (data["messages"] != null)
				{
					$.each( data, function( key, val )
					{
						if (key == "messages")
						{
							$.each( val, function( keyosef, value )// value=
							// array
							{ // message
								$.each( value, function( cle, valeur )
								{
									char = '<span class="triangle-right left in" >' 
											+ unescapeHtml( valeur.message )
											+ '</span>';

									$( "#text" ).append( char );

									addDataToJStorage( 'in', valeur.message );
								} );

							} );

							$.ionSound.play( "water_droplet" );

						}
					} );
					// scroll
					scrollDown();
				}

				if (data["token"] != null)
				{
					token = data["token"];
				}
				if (data["status"] != null)
				{
					getStatus( data["status"] );
				}
				if (data["id_client"] != null)
				{
					$.cookie( "ddlx_live_chat_id", data["id_client"], {
						path : '/',
						expires: 365
					} );
				}
			}
		},
		error : function( msg )
		{
			$( "#annonce" ).html( '' );
			$( "#text" ).html( '' );
			$( location ).attr( 'href', "chat.php" );
			$( "#ddlx_live_chat_status" ).html( "" );
		}
	} );
}

function getStatus( msg )
{
	if (msg == 'online')
	{
		$( "#ddlx_live_chat_status" ).html(
				"<span class='ddlx_merchant_window_i ddlx_merchant_online' /> <strong>{l s='available'  mod='ddlx_live_chat'}</strong>" );
	}
	if (msg == 'away')
	{
		$( "#ddlx_live_chat_status" ).html(
				"<span class='ddlx_merchant_window_i ddlx_merchant_online' /> <strong>{l s='available'  mod='ddlx_live_chat'}</strong>" );
	}
	if (msg == '')
	{
		$( "#ddlx_live_chat_status" ).html(
				"<span class='ddlx_merchant_window_i ddlx_merchant_offline' /> <strong>{l s='unknown'  mod='ddlx_live_chat'}</strong>" );
	}
	if (msg == 'offline')
	{
		$( "#ddlx_live_chat_status" ).html(
				"<span class='ddlx_merchant_window_i ddlx_merchant_offline' /> <strong>{l s='unavailable'  mod='ddlx_live_chat'}</strong>" );
	}

}

function unescapeHtml( safe )
{
	return safe.replace( /&amp;/g, '&' ).replace( /&lt;/g, '<' ).replace( /&gt;/g, '>' ).replace( /&quot;/g, '"' )
			.replace( /&#039;/g, "'" );
}

function scrollDown()
{
	containerText = $( '#text' );
	containerText.scrollTop( containerText.height() + 5000000 );
}

$.valHooks.textarea = {
	get : function( elem )
	{
		return elem.value.replace( /\r?\n/g, "<br/>" );
	}
};

// send message to server
function postMessage()
{
	var message = $( "#message" ).val().trim();
	var chatID = encodeURIComponent( $( "#chatID" ).val() );

	if (token == undefined || token == null || token == "undefined" || token == "")
	{
		token = encodeURIComponent( $( "#token" ).html() );
	}

	if (message.length > 0)
	{
		$.ajax( {
			type : "POST",
			url : "{$modulepathclient}"  + "clientcontroller.php",
			data : 'message=' + message + '&chatID=' + chatID + '&token=' + token,
			success : function(data)
			{
				data = $.parseJSON( data );
				
				if( data["token"] !=null )
				{
					token = data["token"];
					
					addDataToJStorage( 'out', message );
					$( "#text" ).append( '<span class="triangle-right right" >' + message + '</span>' );
					$( "#message" ).focus();
					scrollDown();
				}
				else if ( data["banned"] !=null)
				{
					alert( "{l s='You are banned from this chat.'  mod='ddlx_live_chat'}" );
				}
				else
				{
					alert( "{l s='An error occured, message was not sent.'  mod='ddlx_live_chat'}" );
				}

			},
			error : function( data )
			{
				// On alerte d'une erreur
				alert( "{l s='An error occured, message was not sent.'  mod='ddlx_live_chat'}" );
				$( '#message' ).val( $( '#message' ).val() + message );
			}
		} );

		$( "#message" ).val( '' );
	}
}

// key = in ou out, value = texte
function addDataToJStorage( key, value )
{
	if ($.jStorage.storageAvailable())
	{
		tabindex = $.jStorage.index();

		if (tabindex.length >= 0)
		{
			$.jStorage.set( key + tabindex.length + 1, value );
			// pour ne pas afficher 2 fois le texte ds la fenetre qui recoit.
			lastIndex = key + tabindex.length + 1;
		}
	}
}

function displayHisto()
{
	if ($.jStorage.storageAvailable())
	{
		tabindex = $.jStorage.index();
		lastIndex = tabindex[tabindex.length - 1];

		$.each( tabindex,
				function( index, value )
				{
					if (value.indexOf( "in" ) > -1)
					{
						$( "#text" ).append(
								'<span class="triangle-right left in" >' + unescapeHtml( $.jStorage.get( value ) )
										+ '</span>' );
					}
					// sinon c'est sortant
					else
					{
						$( "#text" ).append(
								'<span class="triangle-right right" >' + unescapeHtml( $.jStorage.get( value ) )
										+ '</span>' );
					}
				} );
		scrollDown();
	}
}

function refreshHisto()
{
	if ($.jStorage.storageAvailable())
	{
		tabindex = $.jStorage.index();

		// si éléments ajoutés
		if (lastIndex != tabindex[tabindex.length - 1])
		{
			display = false;

			$.each( tabindex, function( index, value )
			{
				if (display)
				{
					if (value.indexOf( "in" ) > -1)
					{
						$( "#text" ).append(
								'<span class="triangle-right left in" >' + unescapeHtml( $.jStorage.get( value ) )
										+ '</span>' );
					}
					// sinon c'est sortant
					else
					{
						$( "#text" ).append(
								'<span class="triangle-right right" >' + unescapeHtml( $.jStorage.get( value ) )
										+ '</span>' );
					}
				}
				if (value == lastIndex)
				{
					display = true;
				}

			} );

			lastIndex = tabindex[tabindex.length - 1];

			scrollDown();
		}
	}
}

function getDate()
{
	var d = new Date();
	return d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes()
			+ ":" + d.getSeconds();
}
</script>
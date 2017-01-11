<!-- DDLX LIVE CHAT -->
<link rel="stylesheet" type="text/css"
	href="{$base_dir}modules/ddlx_live_chat/client/s.css">
<script type="text/javascript"
	src="{$base_dir}modules/ddlx_live_chat/js/jquery.cookie.js"></script>
<div class="ddlx_live_chat" id="ddlx_live_chat_bottom"></div>
<div id="ddlx_live_chat_w"></div>

<script>
	function reduce()
	{
	   $( "#ddlx_live_chat_w" ).toggleClass( "ddlx_live_chat_w_reduced" );
	}
	function close()
	{
	   $( "#ddlx_live_chat_w" ).empty();
	   $( "#ddlx_live_chat_w" ).hide();
	   $( "#ddlx_live_chat_w" ).removeClass("ddlx_live_chat_w_reduced");
	}

	if ( self == top ) 
	{
		//interroge le serveur qd focus, sinon non.	
		var window_focus;
	
		$(window).focus(function() {
		    window_focus = true;
		    getStatusAndMessages();
		})
		    .blur(function() {
		        window_focus = false;
		    });
		
		getStatusAndMessages();
		
		$(function()
		{
			popo = $("#ddlx_live_chat_w");
			popo.hide();
			
			setInterval("getStatusAndMessages()", 4000);
			
			var cookieValue = $.cookie("ddlx_live_chat");
			
			//si chat ouvert sur une fenêtre et dans cette fenetre pas d'iframe		
			if(cookieValue != undefined && cookieValue === 'open' && popo.children().length == 0)
			{
				openWin();
			}
			
		});
	}
	else
	{
		$(".ddlx_live_chat, #ddlx_live_chat_w").remove();
	}
		
	function getStatusAndMessages()
	{
		popo = $("#ddlx_live_chat_w");

		var id_client = $.cookie("ddlx_live_chat_id");
		if (id_client == undefined || id_client == null || id_client == "undefined" || id_client == "")
		{
			id_client = "";
		}
		
		if (popo.children().length == 0 )
		{
			
				$.ajax({
					type : "POST",
					url : baseDir  + "modules/ddlx_live_chat/client/clientcontroller.php",
					data : 'waitingchat=1' + '&id_client=' + id_client,
					
					success : function(data)
					{
						data = $.parseJSON( data );
						
						if (data["id_client"] != null)
						{
							$.cookie( "ddlx_live_chat_id", data["id_client"], {
								path : '/',
								expires: 365
							} );
						}
						//ouvrir fenêtre.
						if( data["message"] !=null && data["message"] == 1 )
						{
							openWin();	
						}
						
						msg="";
						
						if( data["status"] !=null)
						{
							msg = data["status"];
						}
	
						if ( msg == 'online' || msg == 'away' )
						{
							if (msg == 'online')
							{
								$("#ddlx_live_chat_bottom").html("<span class='ddlx_merchant_window ddlx_merchant_online' /> <strong> {l s='available'  mod='ddlx_live_chat'} </strong>");
							}
							if (msg == 'away')
							{
								$("#ddlx_live_chat_bottom").html("<span class='ddlx_merchant_window ddlx_merchant_away' /> <strong> {l s='available'  mod='ddlx_live_chat'} </strong>");
							}
							
							{literal}
							var button = '<button onclick="openWin()"'+'class="btn_ddlx">'
							+'Chat !</button>';
							//+'<script>function openWin(){'
							//+'if(! $("#ddlx_live_chat_open").length )'	
							//+' popo.load("modules/ddlx_live_chat/client/iframe.php", function() {popo.show();});'
							
							//+'var myWindow = window.open("modules/ddlx_live_chat/client/index.php","_blank","toolbar=0, scrollbars=1, resizable=0, top=500, left=500, width=600, height=600");'
							
							//+'}';
							 //button +="<"+"/script>";					
							{/literal}
							
							$("#ddlx_live_chat_bottom").append(button);
		
						}
						else
						{
							if (msg == '')
							{
								$("#ddlx_live_chat_bottom").html(" {l s='A problem occured, we are not able to run the chat. If it persists, please let us know by sending a mail in the contact.'  mod='ddlx_live_chat'} ");
							}
		
							if (msg == 'offline')
							{
								$("#ddlx_live_chat_bottom").html("<span class='ddlx_merchant_window ddlx_merchant_offline' /> <strong> {l s='offline'  mod='ddlx_live_chat'} </strong>");
							}
						}
					},
					error : function(msg)
					{
						$("#ddlx_live_chat_bottom").html(" {l s='A problem occured, we are not able to run the chat. If it persists, please let us know by sending a mail.'  mod='ddlx_live_chat'} ");
					}
				});
			
		}//if popo haschildren
	}
	
function openWin()
{
	if (!$( "#ddlx_live_chat_open" ).length)
	{
		popo = $( "#ddlx_live_chat_w" );
		popo.load( baseDir   +  "modules/ddlx_live_chat/client/iframe.php", function()
		{
			popo.show();
		} );

	}
}
	
</script>

<!--/ DDLX LIVE CHAT -->

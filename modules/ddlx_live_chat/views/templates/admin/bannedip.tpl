<table>
	<thead>
		<tr>
			<th>IP</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody id="bannedlist">

		{foreach from=$banned_ips item=ip}
		<tr>
			<td>{$ip['ip_adress']}</td>
			<td>{$ip['date']}</td>
			<td><button type="button" class="button-unbanip" data="{$ip['ip_adress']}">Unban</button></td>
		</tr>
		{/foreach}

	</tbody>
</table>

<script type="text/javascript">
$(document.body).on("click", ".button-unbanip", function() {
	ip = $(this).attr("data");
	Unban(ip);
});

function Unban(ip){
	$.ajax(
			{
				type: "POST",
				url: "{$modulepath}"  + "/bo/merchantcontroller.php",
				data: { token: token, ip: ip, action: 'unban' }
           })
			.done(
				function( data ) 
				{					
					if (data != null && data != "")
					{
						data = $.parseJSON( data );
						
						if (data["result"] != null)
						{
							if(data["result"] == true)
							{
								alert( '{l s="Unban succesfull" mod="ddlx_live_chat"}' );
							}
							else
							{
								alert( '{l s="Impossible to unban" mod="ddlx_live_chat"}' );
							}
						}
						if (data["bannedips"] != null)
						{
							replaceBannedList(data["bannedips"]);
						}
					}
				}
          	);
}

function replaceBannedList( bannedips )
{
	var toreplace ="";
	
	$( bannedips ).each(function( index, value ) 
	{
		toreplace += '<tr><td>' +  value.ip_adress + '</td><td>' + value.date + '</td><td><button type="button" class="button-unbanip" data="' + value.ip_adress + '">Unban</button></td></tr>';
	});
	
	toreplace = "<tbody id=\"bannedlist\">" + toreplace + "</tbody>";
	
	$("#bannedlist").replaceWith(toreplace);

}
</script>
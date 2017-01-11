<div class="systeme_onglets_client">
	<div class="onglets_client"></div>

	<div class="contenu_onglets_client"></div>
</div>

<script type="text/javascript">
	$(document).on(
	"click", ".tab_client_inactif", function()
	{
		 $(".onglet_client").removeClass( "tab_client_actif" ).addClass( "tab_client_inactif" );

         $(".contenu_onglet_client").hide();
		 $(".info_client").hide();

         $( "#cont_" + $(this).attr("id") ).show();
		 $( "#info_" + $(this).attr("id") ).show();

         $(this).removeClass( "tab_client_inactif" ).addClass( "tab_client_actif" );
	}
	);				

		
    $( document ).ready(function() {
    	$("#cont_" + "{$onglet_actif}" ).show();
        $("#" + "{$onglet_actif}" ).removeClass( "tab_client_inactif" ).addClass( "tab_client_actif" );

	});
	
</script>
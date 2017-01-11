<script>
var matched, browser;

$.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

matched = $.uaMatch( navigator.userAgent );
browser = {};

if ( matched.browser ) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
    browser.webkit = true;
} else if ( browser.webkit ) {
    browser.safari = true;
}

$.browser = browser;

</script>
<script type="text/javascript" src="/js/jquery/plugins/autocomplete/jquery.autocomplete.js"></script>
{if isset($ajaxsearch)}
{if $ajaxsearch}
<script type="text/javascript">
// <![CDATA[
$('document').ready(function() {

	var $input = $("#search_query_{$blocksearch_type}");
	var width_ac_results = 	$('#searchbox').width()-53;
	
	$input.autocomplete(
	'{if $search_ssl == 1}{$link->getPageLink('search', true)|addslashes}{else}{$link->getPageLink('search')|addslashes}{/if}',
	{
		minChars: 2,
		max: 10,
		width: (width_ac_results > 0 ? width_ac_results : 1000),
		selectFirst: false,
		scroll: false,
		dataType: "json",
		formatItem: function(data, i, max, value, term) {
			return value;
		},
		parse: function(data) {
			var mytab = [];
			for (var i = 0; i < data.length; i++)
			{
				color = data[i].couleur;
				//npc = data[i].npc;
				colors = '';
				if (color.match('JAUNE')) 	colors += '<span class="' + color + ' declinaison" style="color:transparent; background-color:yellow">' + color + '</span>';
				if (color.match('CYAN')) 	colors += '<span class="' + color + ' declinaison" style="color:transparent; background-color:cyan">' + color + '</span>';
				if (color.match('MAGENTA')) colors += '<span class="' + color + ' declinaison" style="color:transparent; background-color:magenta">' + color + '</span>';
				if (color.match('NOIRE')) 	colors += '<span class="' + color + ' declinaison" style="color:transparent; background-color:black">' + color + '</span>';
				mytab[mytab.length] = { data: data[i], value: data[i].pl_name  + colors};
			}
			return mytab;
		},
		extraParams: {
			ajaxSearch: 1,
			id_lang: {$cookie->id_lang}
		}
	})
	.result(function(event, data, formatted) {
		$input.val(data.pname);
		document.location.href = data.product_link;
	});
});
// ]]>
</script>
{/if}
{/if}

{if isset($instantsearch)}
{if $instantsearch}
<script type="text/javascript">
// <![CDATA[
function tryToCloseInstantSearch()
{
	var $oldCenterColumn = $('#old_center_column');
	if ($oldCenterColumn.length > 0)
	{
		$('#center_column').remove();
		$oldCenterColumn.attr('id', 'center_column').show();
		return false;
	}
}

instantSearchQueries = [];
function stopInstantSearchQueries()
{
	for(var i=0; i<instantSearchQueries.length; i++) {
		instantSearchQueries[i].abort();
	}
	instantSearchQueries = [];
}

$('document').ready(function() {

	var $input = $("#search_query_{$blocksearch_type}");

	$input.on('keyup', function() {
		if ($(this).val().length > 2)
		{
			stopInstantSearchQueries();
			instantSearchQuery = $.ajax({
				url: '{if $search_ssl == 1}{$link->getPageLink('search', true)|addslashes}{else}{$link->getPageLink('search')|addslashes}{/if}',
				data: {
					instantSearch: 1,
					id_lang: {$cookie->id_lang},
					q: $(this).val()
				},
				dataType: 'html',
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				async: true,
				cache: false,
				success: function(data){
					if($input.val().length > 0)
					{
						tryToCloseInstantSearch();
						$('#center_column').attr('id', 'old_center_column');
						$('#old_center_column').after('<div id="center_column" class="' + $('#old_center_column').attr('class') + '">'+data+'</div>').hide();
						// Button override
						ajaxCart.overrideButtonsInThePage();
						$("#instant_search_results a.close").on('click', function() {
							$input.val('');
							return tryToCloseInstantSearch();
						});
						return false;
					}
					else
						tryToCloseInstantSearch();
				}
			});
			instantSearchQueries.push(instantSearchQuery);
		}
		else
			tryToCloseInstantSearch();
	});
});
// ]]>
</script>
{/if}
{/if}
{*
 * @package Jms Drop Megamenu
 * @version 1.0
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}
<a href="#" id="off-canvas-menu-toggle"><span class="lnr lnr-menu"></span></a>
{$menu_html|escape:''}
<script type="text/javascript">
	jQuery(document).ready(function($) {
    	jQuery('.jms-megamenu').jmsMegaMenu({    			
    		event: '{$JMSMM_MOUSEEVENT}',
    		duration: {$JMSMM_DURATION}
    	});		
	});	
</script>
<style type="text/css">
.jms-megamenu .dropdown-menu {    
	transition:all {$JMSMM_DURATION}ms;
}
</style>
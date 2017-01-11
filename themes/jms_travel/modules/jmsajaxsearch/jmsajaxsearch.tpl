{*
 * @package Jms Ajax Search
 * @version 1.1
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}

<div id="jms_ajax_search" class="btn-group compact-hidden">
	<a href="#"  class="btn-xs search-toggle box-group" id="jms_search_btn">
		<span class="fa fa-search"></span>
	</a>
	<div class="search-box" id="dropdown_search">    
        <form method="get" action="{$link->getPageLink('search')|escape:'html'}" id="searchbox">
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input type="text" id="ajax_search" name="search_query" placeholder="{l s='Search everything...' mod='jmsajaxsearch'}" class="form-control" />		
			<a href="#"  class="icon_search">		
			</a>
		</form>
		<div id="search_result">
		</div>
	</div>	
	<div id="cover-background"> </div> 
</div>
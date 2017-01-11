<?php

/*
*/



if (!defined('_PS_VERSION_'))
	exit;

class Blocksearchhome extends Module
{
	public function __construct()
	{
		$this->name = 'blocksearchhome';
		$this->tab = 'search_filter_home';
		$this->version = '1.0.0';
		$this->author = 'SRDEV Informatique';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Recherche de produit par critere');
		$this->description = $this->l('Ajouter un bloc de recherche par critere.');
		$this->ps_versions_compliancy = array('min' => '1.4', 'max' => _PS_VERSION_);
		//$this->context->controller->addJqueryPlugin('ui.core.min.js');
		//$this->context->controller->addJqueryUI('ui.autocomplete.js' );		
	}

	public function install()
	{
		/*if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('header') || !$this->registerHook('home') || !$this->registerHook('displayMobileTopSiteMap') || !$this->registerHook('displaySearch'))*/
		
		if (!parent::install()  || !$this->registerHook('displayHome') )
			return false;
		// effacement du fichier de cache
		@unlink("/cache/class_index.php");
		return true;
	}

	public function hookdisplayMobileTopSiteMap($params)
	{
		$this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
		$params['hook_mobile'] = true;
		return $this->hookTop($params);
	}

	/*
	public function hookDisplayMobileHeader($params)
		{
			if (Configuration::get('PS_SEARCH_AJAX'))
				$this->context->controller->addJqueryPlugin('autocomplete');
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		}
	*/

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocksearch.css', 'all');

		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');

		if (Configuration::get('PS_INSTANT_SEARCH'))
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');

		if (Configuration::get('PS_SEARCH_AJAX') || Configuration::get('PS_INSTANT_SEARCH'))
		{
			Media::addJsDef(array('search_url' => $this->context->link->getPageLink('search', Tools::usingSecureMode())));
			$this->context->controller->addJS(($this->_path).'blocksearch.js');

		}

	}
	public function hookLeftColumn($params)
	{

		return $this->hookRightColumn($params);

	}
	public function hookRightColumn($params)
	{

		if (Tools::getValue('search_query') || !$this->isCached('blocksearch.tpl', $this->getCacheId()))

		{

			$this->calculHookCommon($params);

			$this->smarty->assign(array(

				'blocksearch_type' => 'block',

				'search_query' => (string)Tools::getValue('search_query')

				)

			);

		}

		Media::addJsDef(array('blocksearch_type' => 'block'));

		return $this->display(__FILE__, 'blocksearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());

	}
	public function hookTop($params)
	{
		$key = $this->getCacheId('blocksearch-top'.((!isset($params['hook_mobile']) || !$params['hook_mobile']) ? '' : '-hook_mobile'));

		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-top.tpl', $key))

		{

			$this->calculHookCommon($params);

			$this->smarty->assign(array(

				'blocksearch_type' => 'top',

				'search_query' => (string)Tools::getValue('search_query')

				)

			);

		}

		Media::addJsDef(array('blocksearch_type' => 'top'));

		return $this->display(__FILE__, 'blocksearch-top.tpl', Tools::getValue('search_query') ? null : $key);

	}
	public function hookHome($params)
	{
		$this->context->controller->addJqueryPlugin('ui.core.min.js');
		$this->context->controller->addJqueryUI('ui.autocomplete.js' );		
		$this->context->controller->addCSS(($this->_path).'blocksearch.css', 'all');
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');

		if (Configuration::get('PS_INSTANT_SEARCH'))
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');

		if (Configuration::get('PS_SEARCH_AJAX') || Configuration::get('PS_INSTANT_SEARCH'))
		{
			Media::addJsDef(array('search_url' => $this->context->link->getPageLink('search', Tools::usingSecureMode())));
			$this->context->controller->addJS(($this->_path).'blocksearch.js');
		}
	}
	public function hookDisplayHome($params)
	{
		//$this->context->controller->addCSS(($this->_path).'blocksearchhome.css', 'all');
		$this->context->controller->addJqueryUI('ui.datepicker');
		$this->context->controller->addJqueryPlugin('fancybox');

		$key = $this->getCacheId('blocksearch-home'.((!isset($params['hook_mobile']) || !$params['hook_mobile']) ? '' : '-hook_mobile'));
		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-home.tpl', $key))
		{
			$this->calculHookCommon($params);
			// recherche des attributs pour type loc			
			$sql = "SELECT cl.* FROM ps_category_lang cl 
					INNER JOIN ps_category c ON cl.id_category = c.id_category WHERE c.id_parent = 23 and cl.id_lang = 1 
					ORDER BY position ";					
			$type_locs = Db::getInstance()->ExecuteS($sql);
			$this->context->smarty->assign('nbtype_locs', count($type_locs));
			
			$date_depart = Tools::getValue('date_depart');
			$date_fin = Tools::getValue('date_fin');
			if ( $date_depart == "") $date_depart = $this->dateadd('d', 1, date("d/m/Y"));
			//echo "date=".$date_depart."=". $this->datediff("d",  date("Y-m-d"), $this->dateus($date_depart));
			if ( $this->datediff("d",  date("Y-m-d"), $this->dateus($date_depart))>0 ) 
			{ 
				$date_depart = $this->dateadd('d', 1, date("d/m/Y")); 
				$date_fin = $this->dateadd('d', 1, date("d/m/Y"));
			}
			if ( $date_fin == "") $date_fin = $date_depart;

			//var_dump($type_locs);			
			$this->smarty->assign(array(
				'blocksearch_type' => 'home',
				'type_locs' => $type_locs,
				'search_query' => (string)Tools::getValue('search_query'),
				'date_depart' => $date_depart,
				'date_fin' => $date_fin
				)
			);

		}
		Media::addJsDef(array('blocksearch_type' => 'top'));
		return $this->display(__FILE__, 'blocksearch-home.tpl', Tools::getValue('search_query') ? null : $key);
	}

	public function hookDisplayNav($params)
	{
		return $this->hookTop($params);
	}
	public function hookDisplaySearch($params)
    {
        $this->context->controller->addCSS(($this->_path).'blocksearchhome.css', 'all');

		//$this->context->controller->addJqueryUI('ui.autocomplete.js' );		
		$this->context->controller->addJqueryPlugin('autocomplete');
		
		$key = $this->getCacheId('blocksearch-home'.((!isset($params['hook_mobile']) || !$params['hook_mobile']) ? '' : '-hook_mobile'));
		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-home.tpl', $key))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'blocksearch_type' => 'home',
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		Media::addJsDef(array('blocksearch_type' => 'top'));
		return $this->display(__FILE__, 'blocksearch-home.tpl', Tools::getValue('search_query') ? null : $key);
    }
	public function hookSearchhome($params)
    {

		$this->context->controller->addCSS(($this->_path).'modules/blocksearchhome.css', 'all');
		$this->addCSS(array(
            _MODULE_DIR_.'blocksearchhome/blocksearchhome1.css',
        ));


		//$this->context->controller->addJqueryUI('core');
	
		//$this->context->controller->addJqueryUI('autocomplete' );

		$this->context->controller->addJqueryPlugin('core');

		$this->context->controller->addJqueryPlugin('autocomplete');

		

		$key = $this->getCacheId('blocksearch-home'.((!isset($params['hook_mobile']) || !$params['hook_mobile']) ? '' : '-hook_mobile'));

		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-home.tpl', $key))

		{

			$this->calculHookCommon($params);

			$this->smarty->assign(array(
				'city' => '',

				'blocksearch_type' => 'home',

				'search_query' => (string)Tools::getValue('search_query')

				)

			);

		}

		Media::addJsDef(array('blocksearch_type' => 'top'));

		return $this->display(__FILE__, 'blocksearch-home.tpl', Tools::getValue('search_query') ? null : $key);

    }
	private function calculHookCommon($params)
	{

		$this->smarty->assign(array(

			'ENT_QUOTES' =>		ENT_QUOTES,

			'search_ssl' =>		Tools::usingSecureMode(),

			'ajaxsearch' =>		Configuration::get('PS_SEARCH_AJAX'),

			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),

			'self' =>			dirname(__FILE__),

		));



		return true;

	}
	private function getAttributTypeloc()
	{
		return 'OK';
	}
	
	 // function dateadd 
	public function DateAdd($interval, $number, $date) {
		if (@ereg("-", $date)) $date = $this->datefr($date, "NON"); // on met au format francais pour le traitement si contient -
		list($day, $month, $year)=split("/", $date); // recuperation des elements de la date
		switch ($interval) {
		
			case 'y':   // add year
				$year+=$number;
				break;
	
			case 'm':    // add month
				$month+=$number;
				break;
	
			case 'd':    // add days
				$day+=$number;
				break;
	
			case 'w':    // add week
				$day+=($number*7);
				break;
	
			case 'ww':    // add week
				$day+=($number*7);
				break;
				
			case 'h':    // add hour
				$hours+=$number;
				break;
	
			case 'n':    // add minutes
				$minutes+=$number;
				break;
	
			case 's':    // add seconds
				$seconds+=$number; 
				break;            
	
		}
		$dateplus = @date("d/m/Y", mktime(0, 0, 0, $month, $day,  $year ));// ajoute le délai à la date 
		return $dateplus;
	}
	public function datefr($date_modif, $heurea="") // Entre=2000-12-30 sort=30/12/2000
	{
		//echo  $date_modif.$heurea;
		$date_modif_retour = $date_modif;
		//echo  "retour=".$date_modif_retour;
		if ($date_modif_retour== "") return "";
		// si un tiret alors c'est pas un format americain donc pas de formatage
		//if (@ereg("-", $date_modif_retour)) return $date_modif_retour;
		if (@ereg("-", $date_modif)) $date_modif_retour = substr($date_modif,8,2)."/".substr($date_modif,5,2)."/".substr($date_modif,0,4);
		if ($heurea=="") if (@ereg(":", $date_modif)) $date_modif_retour = $date_modif_retour." ".substr($date_modif,11,2).":".substr($date_modif,14,2);
		//if (! is_date( $date_modif ) ) $date_modif = "";
		if (@ereg("//", $date_modif_retour)) $date_modif_retour = "";
		//if ($date_modif_retour= "") echo "date_modif_retour=$date_modif_retour";
		return substr($date_modif_retour,0,16);
	}
	function datediff( $str_interval, $dt_menor, $dt_maior, $relative=false)
	{
       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
      
       switch( $str_interval){
           case "y":
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h":
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i":
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s":
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
    }
	function dateus($date_modif) // Entre=30/12/2000 sort=2000-12-30
	{
	
		$date_modif_retour = $date_modif;
		if (@ereg("/", $date_modif))  
		{
			$date_modif_retour =  substr($date_modif,6,4)."-".substr($date_modif,3,2)."-".substr($date_modif,0,2);
			if ($heurea=="") if (@ereg(":", $date_modif)) $date_modif_retour .= " ".substr($date_modif,11,2).":".substr($date_modif,14,2);
		}
		//echo substr($date_modif,6,4)."/".substr($date_modif,3,2)."/".substr($date_modif,0,2);
		return $date_modif_retour;
	}

}




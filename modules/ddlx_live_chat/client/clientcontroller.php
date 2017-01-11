<?php
session_start();
header('Content-Type: application/json');
require_once ( '../../../config/config.inc.php' );
require_once ( '../../../init.php' );
require_once ( '../ddlx_live_chat.php' );
require_once ( 'browser.php' );

$now = new DateTime("now");
$dt = $now->format("Y-m-d H:i:s");

global $context;
$context = Context::getContext();

// chatID évite du spam vers ce script sans avoir de session valide.
if ( checksecurity() && isset($_POST ["message"]) )
{
	saveClientConnectedInSession();
	saveClientMessage();
}
// request coming from "client-chat.js"
else if ( checksecurity() )
{
	$id_client = saveClientConnectedInSession();
	getMerchantMessages($id_client);
}
// request coming from "ddlx_live_chat.tpl" (post sans param)
else if ( ! isset($_POST ["chatID"]) && ! isset($_POST ["token"]) )
{
	$id_client = saveClientConnectedInSession();
	getMessagesAndStatus($id_client);
}
else
{
	echo json_encode('error');
}

function checksecurity()
{
	return isset($_POST ["token"]) && $_POST ["token"] == $_SESSION ["token"] && isset($_POST ["chatID"]) && ( ! empty($context->customer->id) || session_id() );
}

function saveClientMessage()
{
	global $dt, $context;
	$jsonarray = Array ();
	
	$bannedip = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '`
											WHERE ip_adress LIKE "' . $_SERVER ['REMOTE_ADDR'] . '"
											AND banned = true
											AND id_shop = ' . $context->shop->id);
	
	if ( ! empty($bannedip) )
	{
		$jsonarray ["banned"] = "banned";
		
		$json = json_encode($jsonarray);
		
		echo $json;
		
		die();
	}
	
	$merchant_id = Db::getInstance()->getValue('SELECT id_employee FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_merchant_status . '
												ORDER BY date');
	
	if ( ! empty($context->customer->id) )
	{
		Db::getInstance()->insert(ddlx_live_chat::$table_message, array (
				'date' => $dt,
				'from_id_customer' => $context->customer->id,
				'to_id_employee' => $merchant_id,
				'message' => htmlspecialchars($_POST ["message"], ENT_QUOTES, "UTF-8"),
				'id_shop' => $context->shop->id 
		));
	}
	else if ( session_id() )
	{
		Db::getInstance()->insert(ddlx_live_chat::$table_message, array (
				'date' => $dt,
				'from_id_session' => session_id(),
				'to_id_employee' => $merchant_id,
				'message' => htmlspecialchars($_POST ["message"], ENT_QUOTES, "UTF-8"),
				'id_shop' => $context->shop->id 
		));
	}
	else
	{
		die("error");
	}
	
	Db::getInstance()->insert(ddlx_live_chat::$table_message_status, array (
			'date' => $dt,
			'id_message' => Db::getInstance()->Insert_ID() 
	));
	

	// $_SESSION ["token"] = rand(10000, 9999999);
	$jsonarray ["token"] = $_SESSION ["token"];
	
	$json = json_encode($jsonarray);
	
	echo $json;
}

function getMessagesAndStatus( $id_client = null )
{
	$mess = getWaitingMessages();
	$status = getMerchantStatus();
	
	$jsonarray = Array ();
	$jsonarray ["status"] = $status;
	$jsonarray ["message"] = $mess;
	// data ["id_client"]
	$jsonarray ["id_client"] = $id_client;
	
	$json = json_encode($jsonarray);
	
	echo $json;
}

function getMerchantMessages( $id_client = null )
{
	global $dt, $context;
	
	// réponse
	$answer;
	$mess_read;
	
	if ( ! empty($context->customer->id) )
	{
		$mess_read = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND message.`to_id_employee` IS NULL
			AND message.`to_id_customer` = ' . $context->customer->id . '
			AND status.`read_by_id_customer` IS NULL
			AND status.`read_by_id_session` IS NULL
			AND message.`id_shop` = ' . $context->shop->id);
	}
	else if ( session_id() )
	{
		$mess_read = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND message.`to_id_employee` IS NULL
			AND message.`to_id_session` LIKE "' . session_id() . '"
			AND status.`read_by_id_customer` IS NULL
			AND status.`read_by_id_session` IS NULL
			AND message.`id_shop` = ' . $context->shop->id);
	}
	
	// si messages déjà envoyés :
	if ( count($mess_read) )
	{
		$i = 1;
		
		foreach ( $mess_read as $m )
		{
			$answer [$i] = Array ();
			// var_dump($mess_read);
			$arrayModele = Array ();
			
			// sauvegarde la lecture du message
			if ( ! empty($context->customer->id) && $m ["to_id_customer"] == $context->customer->id )
			{
				$arrayModele ["message"] = htmlreplace($m ["message"]);
				
				Db::getInstance()->update(ddlx_live_chat::$table_message_status, array (
						'date' => $dt,
						'read_by_id_customer' => $context->customer->id 
				), 'id_message = ' . $m ["id_message"] . ' 
					AND read_by_id_customer IS null', 0, 0, 0);
			}
			else if ( session_id() && ( $m ["to_id_session"] === session_id() ) )
			{
				$arrayModele ["message"] = htmlreplace($m ["message"]);
				// var_dump(session_id());
				// var_dump($m ["to_id_session"]);
				
				Db::getInstance()->update(ddlx_live_chat::$table_message_status, array (
						'date' => $dt,
						'read_by_id_session' => session_id() 
				), 'id_message = ' . $m ["id_message"] . ' 
					AND  read_by_id_session IS null', 0, 0, 0);
			}
			
			array_push($answer [$i], $arrayModele);
			$i ++;
		}
	}
	
	$jsonarray = Array ();
	
	// var_dump($answer);
	
	if ( ! empty($answer) )
	{
		if ( ! empty($answer [1]) )
		{
			if ( ! empty($answer [1] [0]) )
			{
				$jsonarray ["messages"] = $answer;
			}
		}
	}
	
	$status = getMerchantStatus();
	$jsonarray ["status"] = $status;
	
	// $_SESSION ["token"] = rand(10000, 9999999);
	$jsonarray ["token"] = $_SESSION ["token"];
	
	$jsonarray ["id_client"] = $id_client;
	
	$json = json_encode($jsonarray);
	
	echo $json;
}


// request coming from "ddlx_live_chat.tpl" !!
function getWaitingMessages()
{
	global $context;
	
	if ( isset($_POST ["waitingchat"]) )
	{
		$mess_read;
		
		if ( ! empty($context->customer->id) )
		{
			$mess_read = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND message.`to_id_employee` IS NULL
			AND message.`to_id_customer` = ' . $context->customer->id . '
			AND status.`read_by_id_customer` IS NULL
			AND status.`read_by_id_session` IS NULL
			AND message.`id_shop` = ' . $context->shop->id);
		}
		else if ( session_id() )
		{
			$mess_read = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND message.`to_id_employee` IS NULL
			AND message.`to_id_session` LIKE "' . session_id() . '"
			AND status.`read_by_id_customer` IS NULL
			AND status.`read_by_id_session` IS NULL
			AND message.`id_shop` = ' . $context->shop->id);
		}
		

		if ( count($mess_read) )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

function getMerchantStatus()
{
	global $dt;
	global $now;
	/**
	 * Ensemble de résultats, il faut regarder si parmi tous les résultats,
	 */
	$result = Db::getInstance()->getRow('SELECT status, date FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_merchant_status);
	
	$newformat = date('Y-m-d H:i:s', strtotime($result ['date']));
	$lastUpdateDate = new DateTime($newformat);
	
	$interval = $now->diff($lastUpdateDate);
	
	if ( $interval->y > 0 || $interval->m > 0 || $interval->d > 0 || $interval->h > 0 || $interval->i > 0 || $interval->s > 15 )
	{
		return 'offline';
	}
	else
	{
		return $result ['status'];
	}
}

// SAVE CLIENT INFOS
function saveClientConnectedInSession()
{
	global $dt, $context;
	$result;
	

	if ( ! empty($context->customer->id) )
	{
		$result = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client_connected . ' 
											WHERE id_customer = ' . $context->customer->id . '
											AND id_shop = ' . $context->shop->id);
	}
	else if ( session_id() )
	{
		// cookie thing
		
		$result = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client_connected . ' 
											WHERE id_session LIKE \'' . session_id() . '\'
											AND id_shop = ' . $context->shop->id);
	}
	
	if ( $result != null && ! empty($result) )
	{
		
		return updateUserInfo();
	}
	// si aucun client en session
	else
	{
		return saveUserInfo();
	}
}

function saveUserInfo()
{
	global $dt, $context;
	
	$id_client;
	
	$browser = new Browser();
	$browser->getBrowser();
	$browserinfo = 'Browser :' . $browser->getBrowser() . ' | Version :' . $browser->getVersion() . ' | Plateforme :' . $browser->getPlatform();
	
	$ipdetails = $browser->getAdresseGeo($_SERVER ['REMOTE_ADDR']);
	
	if ( $ipdetails != null && isset($ipdetails->country) )
	
	{
		$browserinfo .= ' | Country : ' . $ipdetails->country . ' | Region : ' . $ipdetails->region . ' | City :' . $ipdetails->city;
	}
	
	$browserinfo .= $browser->isMobile() ? ' | mobile device' : '';
	
	// var_dump($browserinfo);
	$infoUser = Array ();
	$infoUser ['browser'] = $browserinfo;
	$infoUser ["ip"] = $_SERVER ['REMOTE_ADDR'];
	
	if ( ! empty($context->customer->id) )
	{
		$infoUser ["id_customer"] = $context->customer->id;
		$infoUser ["name"] = $context->customer->firstname;
		
		Db::getInstance()->insert(ddlx_live_chat::$table_client_connected, array (
				'id_customer' => $infoUser ["id_customer"],
				'ip_adress' => $infoUser ["ip"],
				'name' => $infoUser ["name"],
				'date' => $dt,
				'id_shop' => $context->shop->id 
		));
		
		// check if already exists in BD
		$result = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
											WHERE id_customer = ' . $context->customer->id . '
											AND id_shop = ' . $context->shop->id);
		
		if ( $result == null || empty($result) )
		{
			Db::getInstance()->insert(ddlx_live_chat::$table_client, array (
					'id_customer' => $infoUser ["id_customer"],
					'name' => $infoUser ["name"],
					'ip_adress' => $infoUser ["ip"],
					'date' => $dt,
					'browser' => $infoUser ['browser'],
					'id_shop' => $context->shop->id 
			));
			
			$id_client = Db::getInstance()->Insert_ID();
		}
	}
	else if ( session_id() )
	{
		$infoUser ["id_session"] = session_id();
		$infoUser ["name"] = session_id();
		
		Db::getInstance()->insert(ddlx_live_chat::$table_client_connected, array (
				'id_session' => $infoUser ["id_session"],
				'ip_adress' => $infoUser ["ip"],
				'name' => $infoUser ["name"],
				'date' => $dt,
				'id_shop' => $context->shop->id 
		));
		
		// check if already exists in BD
		$result = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
											WHERE id_session LIKE \'' . $infoUser ["id_session"] . '\'
											AND id_shop = ' . $context->shop->id);
		
		if ( $result == null || empty($result) )
		{
			Db::getInstance()->insert(ddlx_live_chat::$table_client, array (
					'id_session' => $infoUser ["id_session"],
					'name' => $infoUser ["name"],
					'ip_adress' => $infoUser ["ip"],
					'date' => $dt,
					'browser' => $infoUser ['browser'],
					'id_shop' => $context->shop->id 
			));
			
			$id_client = Db::getInstance()->Insert_ID();
		}
	}
	return $id_client;
}

function updateUserInfo()
{
	global $dt, $context;
	$id_client;
	
	$browser = new Browser();
	$browser->getBrowser();
	$browserinfo = 'Browser :' . $browser->getBrowser() . ' | Version :' . $browser->getVersion() . ' | Plateforme :' . $browser->getPlatform();
	
	$browserinfo .= $browser->isMobile() ? ' | mobile device' : '';
	
	$infoUser = Array ();
	$infoUser ['browser'] = $browserinfo;
	$infoUser ["ip"] = $_SERVER ['REMOTE_ADDR'];
	
	if ( ! empty($context->customer->id) )
	{
		$infoUser ["id_customer"] = $context->customer->id;
		$infoUser ["name"] = $context->customer->firstname;
		$infoUser ["id_session"] = null;
		
		Db::getInstance()->update(ddlx_live_chat::$table_client_connected, array (
				'id_customer' => $infoUser ["id_customer"],
				'ip_adress' => $infoUser ["ip"],
				'date' => $dt 
		), 'id_customer = ' . $infoUser ["id_customer"] . '
			AND id_shop = ' . $context->shop->id, 0, 0, 0);
		
		$id_client = Db::getInstance()->getRow("SELECT id 
												FROM " . _DB_PREFIX_ . ddlx_live_chat::$table_client . "
												WHERE id_customer = " . $infoUser ["id_customer"] . "
												AND id_shop = " . $context->shop->id);
	}
	else if ( session_id() )
	{
		$infoUser ["id_customer"] = null;
		$infoUser ["id_session"] = session_id();
		$infoUser ["name"] = session_id();
		Db::getInstance()->update(ddlx_live_chat::$table_client_connected, array (
				'id_session' => $infoUser ["id_session"],
				'ip_adress' => $infoUser ["ip"],
				'date' => $dt 
		), 'id_session LIKE \'' . $infoUser ["id_session"] . '\'
			AND id_shop = ' . $context->shop->id, 0, 0, 0);
		
		$id_client = Db::getInstance()->getRow("SELECT id
												FROM " . _DB_PREFIX_ . ddlx_live_chat::$table_client . "
												WHERE id_session LIKE '" . $infoUser ["id_session"] . "'
												AND id_shop = " . $context->shop->id);
	}
	
	return $id_client ['id'];
}

function htmlreplace( $text )
{
	$rexProtocol = '(https?://)?';
	$rexDomain = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
	$rexPort = '(:[0-9]{1,5})?';
	$rexPath = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
	$rexQuery = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	$rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	
	$validTlds = array_fill_keys(explode(" ", ".aero .asia .biz .cat .com .coop .edu .gov .info .int .jobs .mil .mobi .museum .name .net .org .pro .tel .travel .ac .ad .ae .af .ag .ai .al .am .an .ao .aq .ar .as .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .io .iq .ir .is .it .je .jm .jo .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mk .ml .mm .mn .mo .mp .mq .mr .ms .mt .mu .mv .mw .mx .my .mz .na .nc .ne .nf .ng .ni .nl .no .np .nr .nu .nz .om .pa .pe .pf .pg .ph .pk .pl .pm .pn .pr .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .ye .yt .yu .za .zm .zw .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--80akhbyknj4f .xn--9t4b11yi5a .xn--deba0ad .xn--g6w251d .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--jxalpdlp .xn--kgbechtv .xn--zckzah .arpa"), true);
	
	$results = '';
	$position = 0;
	
	while ( preg_match("{\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))}", $text, $match, PREG_OFFSET_CAPTURE, $position) )
	{
		list ( $url, $urlPosition ) = $match [0];
		
		// Print the text leading up to the URL.
		$results .= ( htmlspecialchars(substr($text, $position, $urlPosition - $position)) );
		
		$domain = $match [2] [0];
		$port = $match [3] [0];
		$path = $match [4] [0];
		
		// Check if the TLD is valid - or that $domain is an IP address.
		$tld = strtolower(strrchr($domain, '.'));
		if ( preg_match('{\.[0-9]{1,3}}', $tld) || isset($validTlds [$tld]) )
		{
			// Prepend http:// if no protocol specified
			$completeUrl = $match [1] [0] ? $url : "http://$url";
			
			// Print the hyperlink.
			$results .= sprintf('<a href="%s" target="_blank">%s</a>', htmlspecialchars($completeUrl), htmlspecialchars("$domain$port$path"));
		}
		else
		{
			// Not a valid URL.
			$results .= ( htmlspecialchars($url) );
		}
		
		// Continue text parsing from after the URL.
		$position = $urlPosition + strlen($url);
	}
	$results .= htmlspecialchars(substr($text, $position));
	// Print the remainder of the text.
	return $results;
}
?>
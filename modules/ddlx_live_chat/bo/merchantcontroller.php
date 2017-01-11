<?php
session_start();
header('Content-Type: application/json');
require_once ( '../../../config/config.inc.php' );
require_once ( '../ddlx_live_chat.php' );


global $cookie;
global $context;
$context = Context::getContext();
$cookie = new Cookie('psAdmin');

$id_employee = (int) $cookie->id_employee;

$d = new DateTime("now");
$dt = $d->format("Y-m-d H:i:s");

// var_dump($_SESSION);
// var_dump($context->shop->id);
// die();


if ( isset($_POST ['token']) && $_POST ["token"] == $_SESSION ["ddlxtokenstatus"] && $id_employee != null && isset($_POST ['id_chatter']) && $_POST ["id_chatter"] != "" && isset($_POST ['comment']) && $_POST ["comment"] != "" )
{
	saveComment();
}
// Handle ban
else if ( isset($_POST ['token']) && $_POST ["token"] == $_SESSION ["ddlxtokenstatus"] && $id_employee != null && isset($_POST ['ip']) && $_POST ["ip"] != "" )
{
	if ( isset($_POST ['action']) && $_POST ["action"] == 'ban' )
	{
		saveBan();
	}
	else if ( isset($_POST ['action']) && $_POST ["action"] == 'unban' )
	{
		unban();
	}
}
else if ( isset($_POST ["chatID"]) && isset($_POST ["message"]) && $id_employee != null && ( isset($_POST ["id_session"]) || isset($_POST ["id_customer"]) ) )
{
	saveMessage();
}
else if ( isset($_GET ['token']) && $_GET ["token"] == $_SESSION ["ddlxtokenstatus"] && $id_employee != null && isset($_GET ['status']) )
{
	$_SESSION ['merchant'] = "merchant";
	updateStatusAndGetClientMessages();
}
else if ( isset($_SESSION ['merchant']) && $id_employee != null && isset($_GET ['id_session']) && isset($_GET ['id_customer']) && isset($_GET ['ip_adress']) )
{
	getClientInfos($_GET ['id_session'], $_GET ['id_customer'], $_GET ['ip_adress']);
}
else
{
	echo 'unauthorized';
}

function saveComment()
{
	global $context;
	
	// check if id_customer_1 or id_session.
	$pos = strpos($_POST ["id_chatter"], "id_customer_");
	
	if ( $pos !== false )
	{
		$id_customer = substr($_POST ["id_chatter"], $pos + 12);
		
		$res = Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
										SET comment = "' . $_POST ['comment'] . '"
										WHERE id_customer = ' . $id_customer . ' AND id_shop = ' . $context->shop->id . ';');
	}
	else
	{
		$id_session = substr($_POST ["id_chatter"], $pos + 11);
		
		$res = Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
										SET comment = "' . $_POST ['comment'] . '"
										WHERE id_session LIKE "' . $id_session . '" AND id_shop = ' . $context->shop->id . ';');
	}
	
	if ( $res )
		echo "1";
}

function unban()
{
	global $context;
	
	$res = Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . ' 
										SET banned = false 
										WHERE ip_adress LIKE "' . $_POST ["ip"] . '" AND id_shop = ' . $context->shop->id . ';');
	
	$jsonarray = Array ();
	$jsonarray ["result"] = $res;
	$jsonarray ["bannedips"] = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
															WHERE banned = true 
															AND id_shop = ' . $context->shop->id . ';');
	
	echo $json = json_encode($jsonarray);
}

function saveBan()
{
	global $context;
	
	$res = Db::getInstance()->update(ddlx_live_chat::$table_client, array (
			'banned' => true 
	), ' id_shop = ' . $context->shop->id . ' AND ip_adress LIKE "' . $_POST ['ip'] . '"', 0, 0);
	
	$jsonarray = Array ();
	$jsonarray ["result"] = $res;
	$jsonarray ["bannedips"] = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
															WHERE banned = true 
															AND id_shop = ' . $context->shop->id . ';');
	
	echo $json = json_encode($jsonarray);
}

function updateStatusAndGetClientMessages()
{
	global $id_employee, $dt, $context;
	
	Db::getInstance()->update(ddlx_live_chat::$table_merchant_status, array (
			'status' => $_GET ["status"],
			'id_shop' => $context->shop->id,
			'date' => $dt 
	), 'id_employee = ' . $id_employee, 0, 0, 0);
	
	$s = rand(10000, 9999999);
	$_SESSION ["ddlxtokenstatus"] = $s;
	
	$client_connected = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client_connected . '
													 WHERE date > ( NOW() - INTERVAL 3 MINUTE )
													 AND id_shop = ' . $context->shop->id);
	
	$jsonarray = Array ();
	$jsonarray ["client"] = $client_connected;
	$jsonarray ["messages"] = getClientMessages();
	$jsonarray ["token"] = $s;
	
	$json = json_encode($jsonarray);
	
	echo $json;
}

function getClientInfos( $id_session, $id_customer, $ip_adress )
{
	global $id_employee, $dt, $context;
	
	$jsonarray = Array ();
	
	if ( $id_session != null && $id_session != "" && $id_session != "null" )
	{
		$request = 'SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
													 WHERE id_shop = ' . $context->shop->id . '
													AND id_session LIKE "' . $id_session . '"
													AND ip_adress LIKE "' . $ip_adress . '" LIMIT 1';
		
		$client_connected = Db::getInstance()->executeS($request);
		$jsonarray ["client"] = $client_connected;
		$jsonarray ["messages"] = getOldClientMessages(false, $id_session);
	}
	else if ( $id_customer != null && $id_customer != "" && $id_customer != "null" )
	{
		$client_connected = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . ddlx_live_chat::$table_client . '
													 WHERE id_shop = ' . $context->shop->id . '
													AND id_customer = ' . $id_customer . '
													AND ip_adress LIKE "' . $ip_adress . '" LIMIT 1');
		$jsonarray ["client"] = $client_connected;
		$jsonarray ["messages"] = getOldClientMessages(true, $id_customer);
	}
	
	$json = json_encode($jsonarray);
	
	echo $json;
}

function getOldClientMessages( $is_customer, $id )
{
	global $id_employee, $dt, $context;
	
	// réponse
	$answer = Array ();
	
	if ( $is_customer )
	{
		$request = '
			SELECT * FROM
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND (message.`to_id_employee` = ' . $id_employee . ' 
				OR message.`from_id_employee` = ' . $id_employee . ')
			AND (message.`to_id_customer` = ' . $id . '
				OR message.`from_id_customer` = ' . $id . ')
			AND (message.`to_id_session` IS NULL
				OR message.`from_id_session` IS NULL)
			AND message.`date` > ( NOW() - INTERVAL 240 MINUTE )
			AND message.`id_shop` = ' . $context->shop->id . '
			AND NOT (status.`read_by_id_employee` IS NULL AND status.`read_by_id_customer` IS NULL AND status.`read_by_id_session` IS NULL)
			ORDER BY message.`date` ASC';
	}
	else
	{
		$request = '
			SELECT * FROM
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status,
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message`
			AND (message.`to_id_employee` = ' . $id_employee . '
				OR message.`from_id_employee` = ' . $id_employee . ')
			AND (message.`to_id_customer` IS NULL
				OR message.`from_id_customer` IS NULL)
			AND (message.`to_id_session` LIKE "' . $id . '"
				OR message.`from_id_session` LIKE "' . $id . '")
			AND message.`date` > ( NOW() - INTERVAL 240 MINUTE )
			AND message.`id_shop` = ' . $context->shop->id . '
			AND NOT (status.`read_by_id_employee` IS NULL AND status.`read_by_id_customer` IS NULL AND status.`read_by_id_session` IS NULL)
			ORDER BY message.`date` ASC';
	}
	
	$mess_read = Db::getInstance()->executeS($request);
	

	if ( count($mess_read) )
	{
		$i = 1;
		
		foreach ( $mess_read as $m )
		{
			$arrayModele = Array ();
			
			if ( $m ["from_id_employee"] == null )
			{
				if ( $m ["from_id_customer"] != null )
				{
					$arrayModele ["from_id_customer"] = $m ["from_id_customer"];
				}
				else if ( $m ["from_id_session"] != null )
				{
					$arrayModele ["from_id_session"] = $m ["from_id_session"];
				}
			}
			else if ( $m ["from_id_employee"] != null )
			{
				$arrayModele ["from_id_employee"] = $m ["from_id_employee"];
				
				if ( $m ["to_id_customer"] != null )
				{
					$arrayModele ["to_id_customer"] = $m ["to_id_customer"];
				}
				else if ( $m ["to_id_session"] != null )
				{
					$arrayModele ["to_id_session"] = $m ["to_id_session"];
				}
			}
			// $arrayModele ["message"] = $m ["message"];
			// echo var_dump($m ["message"]);
			
			$arrayModele ["message"] = htmlreplace($m ["message"]);
			
			// echo var_dump($arrayModele ["message"]);
			
			$answer [$i] = Array ();
			
			array_push($answer [$i], $arrayModele);
			
			$i ++;
		}
	}
	

	return $answer;
}

function getClientMessages()
{
	global $id_employee, $dt, $context;
	
	// réponse
	$answer = Array ();
	
	$mess_read = Db::getInstance()->executeS('
			SELECT * FROM 
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message_status . '` status, 
			`' . _DB_PREFIX_ . ddlx_live_chat::$table_message . '` message
			WHERE status.`id_message` = message.`id_message` 
			AND message.`to_id_employee` IS NOT NULL 
			AND status.`read_by_id_employee` IS NULL
			AND message.`id_shop` = ' . $context->shop->id);
	

	if ( count($mess_read) )
	{
		$i = 1;
		
		foreach ( $mess_read as $m )
		{
			$arrayModele = Array ();
			
			if ( $m ["from_id_customer"] != null )
			{
				$arrayModele ["from_id_customer"] = $m ["from_id_customer"];
			}
			else if ( $m ["from_id_session"] != null )
			{
				$arrayModele ["from_id_session"] = $m ["from_id_session"];
			}
			
			// $arrayModele ["message"] = $m ["message"];
			// echo var_dump($m ["message"]);
			
			$arrayModele ["message"] = htmlreplace($m ["message"]);
			
			// echo var_dump($arrayModele ["message"]);
			
			$answer [$i] = Array ();
			
			array_push($answer [$i], $arrayModele);
			
			// sauvegarde la lecture du message
			if ( $id_employee != null )
			{
				Db::getInstance()->update(ddlx_live_chat::$table_message_status, array (
						'date' => $dt,
						'read_by_id_employee' => $id_employee 
				), 'id_message = ' . $m ["id_message"] . ' 
					AND read_by_id_employee IS null', 0, 0, 0);
			}
			
			$i ++;
		}
	}
	

	return $answer;
}

function saveMessage()
{
	global $cookie, $id_employee, $context, $dt;
	
	if ( isset($_POST ["id_customer"]) )
	{
		Db::getInstance()->insert(ddlx_live_chat::$table_message, array (
				'date' => $dt,
				'from_id_employee' => $id_employee,
				'to_id_customer' => $_POST ["id_customer"],
				'message' => htmlspecialchars($_POST ["message"], ENT_QUOTES, "UTF-8"),
				'id_shop' => $context->shop->id 
		));
		
		Db::getInstance()->insert(ddlx_live_chat::$table_message_status, array (
				'date' => $dt,
				'id_message' => Db::getInstance()->Insert_ID() 
		));
	}
	else if ( isset($_POST ["id_session"]) )
	{
		Db::getInstance()->insert(ddlx_live_chat::$table_message, array (
				'date' => $dt,
				'from_id_employee' => $id_employee,
				'to_id_session' => $_POST ["id_session"],
				'message' => htmlspecialchars($_POST ["message"], ENT_QUOTES, "UTF-8"),
				'id_shop' => $context->shop->id 
		));
		
		Db::getInstance()->insert(ddlx_live_chat::$table_message_status, array (
				'date' => $dt,
				'id_message' => Db::getInstance()->Insert_ID() 
		));
	}
	else
	{
		die("error");
	}
	// retour pour ajax
	echo "ok";
}

// $text = "a1 www.poney.com 222222222 www.poney.com 3333333 www.poney.com hihi4";
// echo htmlreplace($text);
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
<?php
session_start();
require_once ( '../../../config/config.inc.php' );
require_once ( '../../../init.php' );
require_once ( '../ddlx_live_chat.php' );

$ddlx = new ddlx_live_chat();

$context = Context::getContext();
$context->smarty->assign(array (
		'modulepathclient' => $ddlx->path 
));

// gestion ID de session pour savoir qui est en train de chatter
if ( ! isset($_SESSION ['chatID']) )
{
	$_SESSION ['chatID'] = rand(1000, 1000000);
}
if ( ! isset($_SESSION ['token']) )
{
	$_SESSION ['token'] = rand(1000, 1000000);
}

function urllink( $content = '' )
{
	$content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
	// Si on capte un lien tel que www.test.com, il faut rajouter le http://
	if ( preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content) )
	{
		$content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
		// preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
	}
	$content = stripslashes($content);
	return $content;
}

?>
<html>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="s.css">


<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.cookie.js"></script>

<script src="../js/ion.sound.js"></script>
<script src="../js/jstorage.min.js"></script>

<body>
	<div id="token" style="display: none;"><?php echo $_SESSION ['token']; ?></div>
	<div id="container">
		<!-- Statut //////////////////////////////////////////////////////// -->
		<table class="status">
			<tr>
				<td><span id="ddlx_live_chat_status">&nbsp;</span></td>
				<td style="float: right; width: 44px;"><span
					id="ddlx_live_chat_reduce">&nbsp;</span> <span
					id="ddlx_live_chat_close">&nbsp;</span></td>
			</tr>
		</table>
		<table class="chat">
			<tr>
				<!-- zone des messages -->
				<td valign="top" id="text-td">
					<!--	<div id="annonce"></div> -->
					<div id="text"></div>
				</td>
			</tr>
		</table>
		<!-- Zone de texte -->
		<a name="post"></a>
		<table class="post_message">
			<tr>
				<td>
					<form action="" method="">
						<textarea id="message"></textarea>
						<input type="hidden" id="chatID"
							value="<?php echo $_SESSION ['chatID']; ?>" />
					</form>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>

<?php
echo $ddlx->display("ddlx_live_chat.php", 'views/templates/front/client-chat-js.tpl');
?>
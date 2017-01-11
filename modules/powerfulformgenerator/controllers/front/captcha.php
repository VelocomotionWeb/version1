<?php
/**
 * Powerful Form Generator
 *
 * This modules aims to provide for your customer any kind of form you want.
 *
 * If you find errors, bugs or if you want to share some improvments,
 * feel free to contact at contact@prestaddons.net ! :)
 * Si vous trouvez des erreurs, des bugs ou si vous souhaitez
 * tout simplement partager un conseil ou une amélioration,
 * n'hésitez pas à me contacter à contact@prestaddons.net
 *
 * @package   modules
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) April 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @since     2014-04-15
 * @version   2.6.2
 * @license   Nicodème Cyril
 */

include (dirname(__FILE__).'/../../../../config/config.inc.php');
include (dirname(__FILE__).'/../../../../init.php');

/* Let's generate a totally random string using md5 */
$md5_hash = md5(rand(0, 999));

/* We don't need a 32 character long string so we trim it down to 5 */
$security_code = Tools::substr($md5_hash, 15, 5);

/* Set the session to store the security code */
Context::getContext()->cookie->pfg_captcha_string = $security_code;

/* Set the image width and height */
$width = 100;
$height = 20;

/* Create the image resource */
$image = ImageCreate($width, $height);
if (!is_resource($image)) {
    die ('An error occured.');
}

/* We are making three colors, white, black and gray */
$white = ImageColorAllocate($image, 255, 255, 255);
$black = ImageColorAllocate($image, 0, 0, 0);
$grey = ImageColorAllocate($image, 204, 204, 204);

/* Make the background black */
ImageFill($image, 0, 0, $black);

/* Add randomly generated string in white to the image */
ImageString($image, 5, 30, 3, $security_code, $white);

/* Throw in some lines to make it a little bit harder for any bots to break */
ImageRectangle($image, 0, 0, $width - 1, $height - 1, $grey);
imageline($image, 0, $height / 2, $width, $height / 2, $grey);
imageline($image, $width / 2, 0, $width / 2, $height, $grey);

/* Tell the browser what kind of file is come in */
header('Content-Type: image/jpeg');

/* Output the newly created image in jpeg format */
ImageJpeg($image);

/* Free up resources */
ImageDestroy($image);

exit();

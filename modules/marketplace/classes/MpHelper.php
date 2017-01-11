<?php
class MpHelper extends ObjectModel
{
	public static function randomImageName()
	{
		$length = 6;
		$characters= "0123456789abcdefghijklmnopqrstuvwxyz";
		$rand = '';
		
		for ($i = 0; $i < $length; $i++)
			$rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
			
		return $rand;
	}

	/**
	 * [uploadMpImages uploar image for marketplace]
	 * @param  [string]  $dir    [path to upload]
	 * @param  boolean $width  [image width]
	 * @param  boolean $height [image height]
	 * @return [type]          [error/success image id]
	 */
	public static function uploadMpImages($image, $dir_abs_path, $width = false, $height = false)
	{
		if (!$image)
			return false;

		if ($image['error'])
			return $image['error'];

		if (!$width)
			$width = 200;

		if (!$height)
			$height = 200;

		if (!ImageManager::isCorrectImageFileExt($image['name']))
			return 2;

		return ImageManager::resize($image['tmp_name'], $dir_abs_path, $width, $height);
	}
}
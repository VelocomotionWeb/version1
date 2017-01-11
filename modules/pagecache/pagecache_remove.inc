<?php
/**
 * This is a workaround for http://forge.prestashop.com/browse/PSCSX-3241
 */
function jmRemoveOverride($module, $classname)
{
	if (Tools::version_compare(_PS_VERSION_,'1.6','>=')) {
		$path = PrestaShopAutoload::getInstance()->getClassPath($classname.'Core');

		if (!PrestaShopAutoload::getInstance()->getClassPath($classname))
			return true;

		// Check if override file is writable
		$override_path = _PS_ROOT_DIR_.'/'.PrestaShopAutoload::getInstance()->getClassPath($classname);
		if (!is_writable($override_path))
			return false;
	}
	else {
		$path = Autoload::getInstance()->getClassPath($classname.'Core');
		
		if (!Autoload::getInstance()->getClassPath($classname))
			return true;
		
		// Check if override file is writable
		$override_path = _PS_ROOT_DIR_.'/'.Autoload::getInstance()->getClassPath($classname);
		if (!is_writable($override_path))
			return false;
	}

	// Get a uniq id for the class, because you can override a class (or remove the override) twice in the same session and we need to avoid redeclaration
	do $uniq = uniqid();
	while (class_exists($classname.'OverrideOriginal_remove', false));
		
	// Make a reflection of the override class and the module override class
	$override_file = file($override_path);
	eval(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+'.$classname.'\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'), array(' ', 'class '.$classname.'OverrideOriginal_remove'.$uniq), implode('', $override_file)));
	$override_class = new ReflectionClass($classname.'OverrideOriginal_remove'.$uniq);

	$module_file = file($module->getLocalPath().'override/'.$path);
	eval(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+'.$classname.'(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array(' ', 'class '.$classname.'Override_remove'.$uniq), implode('', $module_file)));
	$module_class = new ReflectionClass($classname.'Override_remove'.$uniq);

	// Remove methods from override file
	$override_file = file($override_path);
	foreach ($module_class->getMethods() as $method)
	{
		if (!$override_class->hasMethod($method->getName()))
			continue;

		$method = $override_class->getMethod($method->getName());
		$length = $method->getEndLine() - $method->getStartLine() + 1;
		
		$module_method = $module_class->getMethod($method->getName());

		$override_file_orig = $override_file;

		$orig_content = preg_replace("/\s/", '', implode('', array_splice($override_file, $method->getStartLine() - 1, $length, array_pad(array(), $length, '#--remove--#'))));
		$module_content = preg_replace("/\s/", '', implode('', array_splice($module_file, $module_method->getStartLine() - 1, $length, array_pad(array(), $length, '#--remove--#'))));

		if (md5($module_content) != md5($orig_content)) {
			if (strstr($orig_content, 'pc_groups') !== FALSE 
				|| strstr($orig_content, 'pc_is_logged') !== FALSE 
				|| strstr($orig_content, '->dispatch_') !== FALSE 
				|| strstr($orig_content, '_isPageCacheActive') !== FALSE 
				|| strstr($orig_content, 'self::exec_') !== FALSE 
				|| strstr($orig_content, 'PageCache') !== FALSE 
			) {
				// OK it's mine, I remove it
			}
			else {
				$override_file = $override_file_orig;
			}
		}
	}

	// Remove properties from override file
	foreach ($module_class->getProperties() as $property)
	{
		if (!$override_class->hasProperty($property->getName()))
			continue;

		// Remplacer la ligne de declaration par "remove"
		foreach ($override_file as &$line_content)
			if (preg_match('/(public|private|protected|const)\s+(static\s+)?(\$)?'.$property->getName().'/i', $line_content))
			{
				$line_content = '#--remove--#';
				break;
			}
	}

	// Rewrite nice code
	$code = '';
	foreach ($override_file as $line)
	{
		if ($line == '#--remove--#')
			continue;

		$code .= $line;
	}
	file_put_contents($override_path, $code);
	
	return true;
}

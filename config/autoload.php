<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Wishlist
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\GiveAwayHook' => 'system/modules/wishlist/classes/GiveAwayHook.php',

	// Modules
	'ModuleWishlist'      => 'system/modules/wishlist/modules/ModuleWishlist.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_wishlist' => 'system/modules/wishlist/templates',
));

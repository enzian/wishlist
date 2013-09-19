<?php

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['wishlist'] = array(
	'tables' => array('tl_wishlist', 'tl_wishcategory', 'tl_wish', 'tl_giveaway', 'tl_giveawayitem'),
	'icon'   => 'system/modules/wishlist/assets/images/wishlist.png'
);

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['wishlist'] = array
(
	'wishlist'     => 'ModuleWishlist',
);

array_insert( $GLOBALS['TL_CTE']['wishlist'], 0, array( 'wishlist' => 'ModuleWishlist' ) );

$GLOBALS['TL_HOOKS']['processFormData'][] = array('GiveAwayHook', 'processGiveAway');

?>
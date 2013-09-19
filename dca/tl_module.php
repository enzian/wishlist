<?php


$GLOBALS['TL_DCA']['tl_module']['palettes']['wishlist']    = '{title_legend},name,headline,type;{config_legend},wishlists';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['wishlists'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['wishlists'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options_callback'        => array('tl_module_wishlists', 'getWishlists'),
	'eval'                    => array('multiple'=>true, 'mandatory'=>true),
	'sql'                     => "int(10) NOT NULL"
);

class tl_module_wishlists extends Backend
{

	/**
	 * Get all news archives and return them as array
	 * @return array
	 */
	public function getWishlists()
	{
		$arrArchives = array();
		$objArchives = $this->Database->execute("SELECT id, title FROM tl_wishlist ORDER BY title");

		while ($objArchives->next())
		{
			$arrArchives[$objArchives->id] = $objArchives->title;
		}

		return $arrArchives;
	}

}


?>
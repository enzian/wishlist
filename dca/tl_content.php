<?php


$GLOBALS['TL_DCA']['tl_content']['palettes']['wishlist']    = '{title_legend},name,headline,type;{config_legend},wishlist,wishlist_categories,wishlistIsEditable';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['wishlist'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['wishlist'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options_callback'        => array('tl_module_wishlist_categories', 'getWishlists'),
	'eval'                    => array('multiple'=>true, 'mandatory'=>true, 'submitOnChange' => true),
	'sql'                     => "int(10) NOT NULL"
);

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['wishlist_categories'] = array(
       'label' => &$GLOBALS['TL_LANG']['tl_content']['wishlist_categories'],
       'inputType' => 'checkbox',
       'exclude' => true,
       'options_callback' => array(
              'tl_module_wishlist_categories',
              'optionsCallbackListCategories'
       ),
       'eval' => array(
              'multiple' => true,
              'tl_class' => 'clr',
              'submitOnChange' => true
       ),
       'sql' => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['wishlistIsEditable'] = array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_news']['wishlistIsEditable'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>false),
      'sql'                     => "char(1) NOT NULL default ''"
);

class tl_module_wishlist_categories extends Backend
{
       public function __construct()
       {
             parent::__construct();

       }

       /**
       * options_callback fuer die Albumauflistung
       * @return string
       */
       public function optionsCallbackListCategories()
       {
            $objModule  = array();
    		$objModule = $this->Database->prepare('SELECT wishlist FROM tl_content WHERE id=?')->execute(Input::get('id'));
            $arrArchives = array();
            $objArchives = $this->Database->execute("SELECT id, title FROM tl_wishcategory WHERE pid='" . $objModule->wishlist . "' ORDER BY title");
            while ($objArchives->next())
            {
            	$arrArchives[$objArchives->id] = $objArchives->title;
            }

            return $arrArchives;
       }

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
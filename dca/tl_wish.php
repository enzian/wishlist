<?php

$GLOBALS['TL_DCA']['tl_wish'] = array
(
    // Config
    'config' => array
    (
      'dataContainer'               => 'Table',
      'ptable'                      => 'tl_wishcategory',
      'enableVersioning'            => true,
      'switchToEdit'                => true,
      'onload_callback'             => array
        (
          array('tl_wish', 'onloadCallback')
        ),
      'sql' => array
      (
        'keys' => array
        (
          'id' => 'primary',
          'pid' => 'index'
        )
      ),
   ),
  'label' => array
  (
    'fields'                  => array('title'),
    'format'                  => '%s'
  ),
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('sorting'),
      'headerFields'            => array('title', 'description'),
      'panelLayout'             => 'filter;search,limit',
      'child_record_callback'   => array('tl_wish', 'listWishesCallback'),
      'child_record_class'      => 'no_padding'
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      )
    ),
    'operations' => array
    (
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wish']['editmeta'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wish']['move'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset()"'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wish']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'button_callback'     => array('tl_wish', 'generateDeleteButton')
      )
    )
  ),
  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('wishtype'), 
    'default'       => '{title_legend},type,title,{wishlist_legend},description,image;wishtype' //totalamount,nofItems,nofItemsAvailable
  ),
  'subpalettes' => array( 'wishtype_shares' => 'pricepershare,nofItems,nofItemsAvailable', 'wishtype_amount' => 'targetamount, accievedamount'),
    // Fields
  'fields' => array
  (
    'id'     => array
    (
      'sql' => "int(10) unsigned NOT NULL auto_increment"
    ),
    'pid' => array
    (
      'foreignKey'              => 'tl_wishcategory.id',
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    ),
    'tstamp' => array
    (
      'sql' => "int(10) unsigned NOT NULL default '0'"
    ),
    'sorting' => array
    (
      'sql' => "int(10) unsigned NOT NULL default '0'"
    ),
    'title'  => array
    (
      'label'     => &$GLOBALS['TL_LANG']['tl_wish']['title'],
      'inputType' => 'text',
      'exclude'   => true,
      'sorting'   => true,
      'flag'      => 1,
      'search'    => true,
      'eval'      => array(
          'mandatory'   => true,
          'unique'      => true,
          'maxlength'   => 255,
        ),
      'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'description' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['description'],
      'exclude'                 => true,
      'search'                  => true,
      'sorting'                 => false,
      'inputType'               => 'textarea',
      'eval'                    => array('style' => 'width: 100%; height: 50px;'),
      'sql'                     => "text NULL"
    ),
    'image' => array(
           'label' => &$GLOBALS['TL_LANG']['tl_wish']['image'],
           'exclude' => true,
           'inputType' => 'fileTree',
           'eval' => array(
                  'fieldType'   => 'radio',
                  'files'       => true,
                  'filesOnly'   => true,
                  'extensions'  => 'jpeg,jpg,gif,png,bmp,tiff',
                  'tl_class'=>'ctr m12'
           ),
           'sql' => "int(10) unsigned NOT NULL default '0'"
    ),
    'wishtype' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['wishtype'],
      'exclude'                 => true,
      'search'                  => true,
      'sorting'                 => true,
      'inputType'               => 'select',
      'options'                 => array('shares', 'amount'),
      'reference'               => &$GLOBALS['TL_LANG']['tl_wish']['wishtypes'], 
      'eval'                    => array( 'rte'                 =>'tinyMCE',
                                          'tl_class'            =>'w50 ',
                                          'submitOnChange'      => true,
                                          'includeBlankOption'  => true,
                                          'mandatory'           => true),
      'sql'                     => "varchar(10) NOT NULL default '0'"
    ),
    'pricepershare' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['pricepershare'],
      'exclude'                 => true,
      'search'                  => true,
      'sorting'                 => true,
      'inputType'               => 'text',
      'eval'                    => array( 'tl_class'=>'w50'),
      'sql'                     => "int(10) NOT NULL default '0'"
    ),

    'nofItems' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['nofItems'],
      'exclude'                 => true,
      'search'                  => false,
      'sorting'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'decimal', 'tl_class'=>'w50'),
      'sql'                     => "int(10) NOT NULL default '1'"
    ),
    'nofItemsAvailable' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['nofItemsAvailable'],
      'exclude'                 => true,
      'search'                  => false,
      'sorting'                 => true,
      'inputType'               => 'text',
      'eval'                    => array(
            'rgxp'      =>'decimal',
            'tl_class'  =>'w50',
            'readonly'  => false,
            ),
      'sql'                     => "int(10) NOT NULL default '1'"
    ),
    'targetamount' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['targetamount'],
      'exclude'                 => true,
      'search'                  => false,
      'sorting'                 => true,
      'inputType'               => 'text',
      'eval'                    => array(
            'rgxp'      =>'integer',
            'tl_class'  =>'w50',
            'readonly'  => false,
            ),
      'sql'                     => "int(10) NOT NULL default '1'"
    ),
    'accievedamount' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wish']['accievedamount'],
      'exclude'                 => true,
      'search'                  => false,
      'sorting'                 => true,
      'inputType'               => 'text',
      'eval'                    => array(
            'rgxp'      =>'integer',
            'tl_class'  =>'w50 m12',
            'readonly'  => true,
            ),
      'sql'                     => "int(10) NOT NULL default '0'"
    ),
  )
); // end of $GLOBALS['TL_DCA']['tl_wish'] array


class tl_wish extends Backend
{
  /**
   * Add the type of input field
   * @param array
   * @return string
   */
  public function listWishesCallback($arrRow)
  {
    if($arrRow['wishtype'] == 'shares')
    {
     return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $GLOBALS['TL_LANG']['tl_wish']['pricepershare'][0] . ': ' . $arrRow['pricepershare'] . ' ' . $GLOBALS['TL_DCA']['tl_wish']['config']['currency'] . ', ' . $GLOBALS['TL_LANG']['tl_wish']['available'] . ': ' . $arrRow['nofItemsAvailable'] . ']</span></div>';
    }
    else if($arrRow['wishtype'] == 'amount')
    {
     return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $GLOBALS['TL_LANG']['tl_wish']['targetamount'][0] . ': ' . $arrRow['targetamount'] . ' ' . $GLOBALS['TL_DCA']['tl_wish']['config']['currency'] . ', ' . $GLOBALS['TL_LANG']['tl_wish']['accievedamount'][0] . ': ' . $arrRow['accievedamount'] . ' ' . $GLOBALS['TL_DCA']['tl_wish']['config']['currency'] . ']</span></div>';
    }
   }

  public function generateDeleteButton($row, $href, $label, $title, $icon, $attributes)
   {
      $db = Database::getInstance();
      $conflictResults = $db->prepare("SELECT wishes.id FROM tl_wish AS wishes WHERE (wishes.nofItems != wishes.nofItemsAvailable OR wishes.accievedamount > 0) && wishes.id = ? ")->execute($row['id']);
      if($conflictResults->count() == 0)
      {
        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
      }
   }

   public function onloadCallback($arrRow)
   {
      $db = Database::getInstance();
      $currencyResult = $db->prepare("SELECT tl_wishlist.currency_short FROM tl_wishcategory INNER JOIN tl_wishlist ON tl_wishcategory.pid = tl_wishlist.id WHERE tl_wishcategory.id = ?")->execute($arrRow->id);
      $GLOBALS['TL_DCA']['tl_wish']['config']['currency'] = $currencyResult->currency_short;
   }
}

?>
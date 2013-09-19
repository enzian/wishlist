<?php

$GLOBALS['TL_DCA']['tl_wishcategory'] = array
(
    // Config
    'config' => array
    (
      'dataContainer'               => 'Table',
      'ptable'                      => 'tl_wishlist',
      'ctable'                      => array('tl_wish'),
      'doNotDeleteRecords'          => false,
      'enableVersioning'            => true,
      'switchToEdit'                => true,
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
      'headerFields'            => array('title'),
      'panelLayout'             => 'filter;sort,search,limit',
      'child_record_callback'   => array('tl_wishcategory', 'listCategories'),
      'child_record_class'      => 'no_padding'
    ),
    'global_operations' => array
    (
      'giveaways' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['giveaways'] ,
        'href'                => 'table=tl_giveaway',
        'class'               => 'header_edit_all',
        'icon'                => 'system/modules/wishlist/assets/images/stats.png',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      ),
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      ),
      
    ),
    'operations' => array
    (
      'items' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['itemsedit'],
        'href'                => 'table=tl_wish',
        'icon'                => 'edit.gif'
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['editmeta'],
        'href'                => 'act=edit',
        'icon'                => 'header.gif'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['move'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset()"'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'button_callback'     => array('tl_wishcategory', 'generateDeleteButton')
      )
    )
  ),
  // Palettes
  'palettes' => array
  (
    'default'       => '{wishlist_legend},title,description'
  ),
    // Fields
  'fields' => array
  (
    'id'     => array
    (
      'sql' => "int(10) unsigned NOT NULL auto_increment"
    ),
    'pid' => array
    (
      'foreignKey'              => 'tl_wishlist.id',
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
      'label'     => &$GLOBALS['TL_LANG']['tl_wishcategory']['title'],
      'inputType' => 'text',
      'exclude'   => true,
      'sorting'   => true,
      'flag'      => 1,
      'search'    => true,
      'eval'      => array(
          'mandatory'   => true,
          'unique'      => true,
          'maxlength'   => 255,
          'tl_class'    => 'w50',
        ),
      'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'description' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_wishcategory']['description'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE', 'tl_class'=>'clr'),
      'sql'                     => "text NULL"
    )
  )
); // end of $GLOBALS['TL_DCA']['tl_wishcategory'] array


class tl_wishcategory extends Backend
{
  /**
   * Add the type of input field
   * @param array
   * @return string
   */
  public function listCategories($arrRow)
  {
    $db = Database::getInstance();
    $conflictResults = $db->prepare("SELECT COUNT(tl_wish.id) as nofWishes FROM tl_wishcategory INNER JOIN tl_wish ON tl_wish.pid = tl_wishcategory.id WHERE tl_wishcategory.id = ?")->execute($arrRow['id']);
    return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['tstamp']) . ', ' . $conflictResults->nofWishes . ' '. $GLOBALS['TL_LANG']['tl_wishcategory']['wishes'] . ']</span></div>';
  }

   public function generateDeleteButton($row, $href, $label, $title, $icon, $attributes)
   {
      $db = Database::getInstance();
      $conflictResults = $db->prepare("SELECT categories.id FROM tl_wishcategory AS categories INNER JOIN tl_wish AS wishes ON wishes.pid=categories.id INNER JOIN tl_giveawayitem AS givewayitems ON wishes.id = givewayitems.wid WHERE (wishes.nofItems > wishes.nofItemsAvailable OR wishes.accievedamount > 0) AND categories.id=?")->execute($row['id']);
      //die(print_r($conflictResults));
      if($conflictResults->count() == 0)
      {
        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
      }
   }
}

?>
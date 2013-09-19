<?php

$GLOBALS['TL_DCA']['tl_giveawayitem'] = array
(
    // Config
    'config' => array
    (
      'dataContainer'               => 'Table',
      'ptable'                      => 'tl_giveaway',
      'enableVersioning'            => false,
      'switchToEdit'                => false,
      'closed'                      => true,
      'ondelete_callback' => array
      (
        array('tl_giveawayitem', 'deleteGiveawayitem'),
      ),
      'onload_callback' => array
      (
        array('tl_giveawayitem', 'onloadCallback')
      ),
      'sql' => array
      (
        'keys' => array
        (
          'id' => 'primary',
          'pid' => 'index',
          'wid' => 'index'
        )
      ),
   ),
  'label' => array
  (
    'fields'                  => array('firstname', 'lastname'),
    'format'                  => '%s'
  ),
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array(),
      'flag'                    => 12,
      'headerFields'            => array('firstname', 'lastname', 'mail'),
      'panelLayout'             => 'filter;sort,search,limit',
      'child_record_callback'   => array('tl_giveawayitem', 'listGiveawayitems'),
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
      // 'edit' => array
      // (
      //   'label'               => &$GLOBALS['TL_LANG']['tl_giveawayitem']['edit'],
      //   'href'                => 'act=edit',
      //   'icon'                => 'edit.gif'
      // ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_wish']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif'
      )
    )
  ),
  // Palettes
  'palettes' => array
  (
    'default'       => '{title_legend},type,title,{wishlist_legend},description;totalamount,nofItems,nofItemsAvailable,customThumb,'
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
      'foreignKey'              => 'tl_giveaway.id',
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    ),
    'wid' => array
    (
      'foreignKey'              => 'tl_wish.id',
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    ),
    'nofItems' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_giveawayitem']['nofItems'],
      'exclude'                 => true,
      'search'                  => false,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'decimal', 'tl_class'=>'w50'),
      'sql'                     => "int(10) NULL"
    ),
    'amount' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_giveawayitem']['amount'],
      'exclude'                 => true,
      'search'                  => false,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'integer', 'tl_class'=>'w50'),
      'sql'                     => "int(10) NULL"
    )
  )
); // end of $GLOBALS['TL_DCA']['tl_giveawayitem'] array


class tl_giveawayitem extends Backend
{

  public function onloadCallback($arrRow)
   {
      $db = Database::getInstance();
      $currencyResult = $db->prepare("SELECT currency_short FROM tl_wishlist INNER JOIN tl_giveaway ON tl_wishlist.id = tl_giveaway.pid WHERE tl_giveaway.id = ?")->execute($arrRow->id);
      $GLOBALS['TL_DCA']['tl_giveawayitem']['config']['currency'] = $currencyResult->currency_short;
   }
  /**
   * Add the type of input field
   * @param array
   * @return string
   */
  public function listGiveawayitems($arrRow)
  {
    $db = Database::getInstance();
    $objResults = $db->prepare("SELECT * FROM tl_wish WHERE tl_wish.id=?")->execute($arrRow['wid']);
    $object = $objResults->fetchAllAssoc()[0];
    $amountstr;
    if($object['wishtype'] == 'shares')
    {
      $amountstr = $arrRow['nofItems'] . ' ' . $GLOBALS['TL_LANG']['wishlist']['shares'][0];
    }
    else if($object['wishtype'] == 'amount')
    {
      $amountstr = $arrRow['amount'] . ' ' . $GLOBALS['TL_DCA']['tl_giveawayitem']['config']['currency'];
    }
    return '<div class="tl_content_left">' . $object['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $amountstr . ']</span></div>';
  }

  public function deleteGiveawayitem($dc)
  {
      $delentry = $this->Database->prepare("SELECT id, wid, nofitems, amount FROM tl_giveawayitem WHERE id=?")->execute($dc->id);
      $delitem = $delentry->fetchAllAssoc()[0];
      $oldWishResult = $this->Database->prepare("SELECT id, nofItemsAvailable, accievedamount, wishtype FROM tl_wish WHERE id=?")->execute($delitem['wid']);
      $oldwish = $oldWishResult->fetchAllAssoc()[0];
      if($oldwish['wishtype'] == 'shares')
      {
        $newamount = $oldwish['nofItemsAvailable'] + $delitem['nofitems'];
        $statement = $this->Database->prepare("UPDATE tl_wish SET nofItemsAvailable=? WHERE id=?");
        $statement->execute(array($newamount, $oldwish['id']));
        $this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($dc->id);
      }
      else if($oldwish['wishtype'] == 'amount')
      {
        $newamount = $oldwish['accievedamount'] - $delitem['amount'];
        $statement = $this->Database->prepare("UPDATE tl_wish SET accievedamount=? WHERE id=?");
        $statement->execute(array($newamount, $oldwish['id']));
        $this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($dc->id);
      }
  }


}

?>
<?php

$GLOBALS['TL_DCA']['tl_giveaway'] = array
(
    // Config
    'config' => array
    (
      'dataContainer'               => 'Table',
      'ctable'                      => 'tl_giveawayitem',
      'ptable'                      => 'tl_wishlist',
      'closed'                      => true,
      'ondelete_callback' => array
      (
        array('tl_giveaway', 'deleteGiveawayCallback')
      ),
      'onload_callback' => array
      (
        array('tl_giveaway', 'onloadCallback')
      ),
      'sql' => array
      (
        'keys' => array
        (
          'id' => 'primary',
          'pid' => 'index',
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
      'fields'                  => array('tstamp ASC'),
      'headerFields'            => array('title'),
      'panelLayout'             => 'filter;sort,search,limit',
      'child_record_callback'   => array('tl_giveaway', 'listGiveaways'),
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
      'inspect' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_giveaway']['inspect'],
        'href'                => 'table=tl_giveawayitem',
        'icon'                => 'system/modules/wishlist/assets/images/mag_glass.png'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_giveaway']['editmeta'],
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
      'foreignKey'              => 'tl_wishlist.id',
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    ),
    'tstamp' => array
    (
      'default'                 => time(),
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'flag'                    => 8,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'firstname'  => array
    (
      'label'     => &$GLOBALS['TL_LANG']['tl_giveaway']['firstname'],
      'inputType' => 'text',
      'exclude'   => true,
      'sorting'   => true,
      'flag'      => 1,
      'search'    => true,
      'eval'      => array(
          'mandatory'   => true,
        ),
      'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'lastname'  => array
    (
      'label'     => &$GLOBALS['TL_LANG']['tl_giveaway']['lastname'],
      'inputType' => 'text',
      'exclude'   => true,
      'sorting'   => true,
      'flag'      => 1,
      'search'    => true,
      'eval'      => array(
          'mandatory'   => true,
        ),
      'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'mail'  => array
    (
      'label'     => &$GLOBALS['TL_LANG']['tl_giveaway']['mail'],
      'inputType' => 'text',
      'exclude'   => true,
      'sorting'   => true,
      'flag'      => 1,
      'search'    => true,
      'eval'      => array(
          'mandatory'   => true,
        ),
      'sql'       => "varchar(255) NOT NULL default ''"
    ),
  )
); // end of $GLOBALS['TL_DCA']['tl_giveaway'] array


class tl_giveaway extends Backend
{

  /**
   * Add the type of input field
   * @param array
   * @return string
   */
  public function listGiveaways($arrRow)
  {
    $db = Database::getInstance();
    $itemsResult = $db->prepare("SELECT tl_giveawayitem.nofItems as givennofitems, tl_giveawayitem.amount, tl_wish.wishtype, tl_wish.nofItems, tl_wish.accievedamount, tl_wish.pricepershare FROM tl_giveaway INNER JOIN tl_giveawayitem ON tl_giveaway.id = tl_giveawayitem.pid INNER JOIN tl_wish ON tl_giveawayitem.wid = tl_wish.id  WHERE tl_giveaway.id = ?")->execute($arrRow['id']);
    $total = 0;
    while ($itemsResult->next())
    {
      if($itemsResult->wishtype == 'shares')
      {
        $total = $total + ($itemsResult->givennofitems * $itemsResult->pricepershare);
      }
      else
      {
        $total = $total + $itemsResult->amount;
      }
    }
    //$currResult = $db->prepare("SELECT currency_short FROM tl_wishlist WHERE id = ?")->execute($arrRow['pid']);
    //$currency = $currResult->fetchAllAssoc();
    return '<div class="tl_content_left">' . $arrRow['firstname'] . ' ' . $arrRow['lastname'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $arrRow['mail'] . ', ' . $GLOBALS['TL_LANG']['tl_giveaway']['giveaway_amount'] . ': ' . $total . ' ' . $GLOBALS['TL_DCA']['tl_giveaway']['config']['currency'] . ']</span></div>';
  }


  public function deleteGiveawayCallback(DataContainer $dc)
  {
    $db = Database::getInstance();
    $itemsResult = $db->prepare("SELECT tl_giveawayitem.id, tl_giveawayitem.wid, tl_giveawayitem.nofItems, tl_giveawayitem.amount FROM tl_giveaway INNER JOIN tl_giveawayitem ON tl_giveaway.id = tl_giveawayitem.pid WHERE tl_giveaway.id = ?")->execute($dc->id);
    while ($itemsResult->next())
    {
      $oldWishResult = $this->Database->prepare("SELECT id, nofItemsAvailable, accievedamount, wishtype FROM tl_wish WHERE id=?")->execute($itemsResult->row()['wid']);
      $oldwish = $oldWishResult->fetchAllAssoc()[0];
      // $newamount = $oldwish['nofItemsAvailable'] + $itemsResult->row()['nofItems'];

      // $this->Database->prepare("UPDATE tl_wish SET nofItemsAvailable=? WHERE id=?")->execute(array($newamount, $oldwish['id']));
      // $this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($itemsResult->row()['id']);
      if($oldwish['wishtype'] == 'shares')
      {
        //die('deleting shared');
        $newamount = $oldwish['nofItemsAvailable'] + $itemsResult->row()['nofItems'];

        $this->Database->prepare("UPDATE tl_wish SET nofItemsAvailable=? WHERE id=?")->execute(array($newamount, $oldwish['id']));
        //$this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($itemsResult->row()['id']);
      }
      else
      {
        $newamount = $oldwish['accievedamount'] - $itemsResult->row()['amount'];
        $this->Database->prepare("UPDATE tl_wish SET accievedamount=? WHERE id=?")->execute(array($newamount, $oldwish['id']));
        //$this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($itemsResult->row()['id']);
      }
      $this->Database->prepare("DELETE FROM tl_giveawayitem WHERE id=?")->execute($itemsResult->row()['id']);
    }
    $this->Database->prepare("DELETE FROM tl_giveaway WHERE id=?")->execute($dc->id);
  }

  public function onloadCallback($arrRow)
   {
      $db = Database::getInstance();
      $currencyResult = $db->prepare("SELECT currency_short FROM tl_wishlist WHERE id = ?")->execute($arrRow->id);
      $GLOBALS['TL_DCA']['tl_giveaway']['config']['currency'] = $currencyResult->currency_short;
   }
}

?>
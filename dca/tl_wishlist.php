<?php

/**
 * Table tl_wishlist
 */
$GLOBALS['TL_DCA']['tl_wishlist'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => 'tl_wishcategory',
		'enableVersioning'            => true,
		'switchToEdit'                => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'filter,sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'stats' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wishcategory']['giveaways'],
				'href'                => 'table=tl_giveaway',
				'icon'                => 'system/modules/wishlist/assets/images/stats.png'
			),
			'items' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wishlist']['itemsedit'] ,
				'href'                => 'table=tl_wishcategory',
				'icon'                => 'edit.gif'
			),
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_wishlist']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'header.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_wishlist']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show'   => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_wishlist']['show'],
				'href'       => 'act=show',
				'icon'       => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('send_confirmation'),
		'default'       => '{title_legend},type,title,{wishlist_legend},form,currency_short;{confirmation},send_confirmation'
	),
	'subpalettes' => array
	(
		'send_confirmation' => 'conf_subject,conf_template,conf_sender,conf_senderMail',
	),
   
	// Fields
	'fields'   => array
	(
		'id'     => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'title'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['title'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'flag'      => 1,
            'search'    => true,
			'eval'      => array(
				'mandatory'   => true,
                'unique'         => true,
                'maxlength'   => 255,
				'tl_class'        => 'w50',
				),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'form'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['form'],
			'inputType' => 'select',
			'exclude'   => true,
            'options_callback' 		=> array(
            	'tl_wishlist',
            	'optionsCallbackListForms'
       		),
       		
			'eval'      => array(
				'mandatory'   => true,
				'tl_class'        => 'w50',
				'includeBlankOption' 	=> true,
       			'blankOptionLabel'		=> '-',
				),
			'sql'       => "int(10) NOT NULL"
		),
		'currency_short'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['currency_short'],
			'inputType' => 'text',
			'exclude'   => true,
			'flag'      => 1,
			'eval'      => array(
				'mandatory'   	=> false,
                'unique'        => false,
                'maxlength'   	=> 255,
				'tl_class'      => 'w50',
				),
			'sql'       => "varchar(10) NOT NULL default 'CHF'"
		),
		'send_confirmation'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['send_confirmation'],
			'inputType' => 'checkbox',
			'exclude'   => true,
			'flag'      => 1,
			'eval'      => array(
				'mandatory'   	=> false,
				'tl_class'      => 'w50',
				'submitOnChange'      => true,
				),
			'sql'       => "varchar(1) NOT NULL default '0'"
		),
		'conf_subject' => array
	    (
	      'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['conf_subject'],
			'inputType' => 'text',
			'exclude'   => true,
			'flag'      => 1,
			'eval'      => array(
				'mandatory'   	=> true,
                'unique'        => false,
                'maxlength'   	=> 255,
				'tl_class'      => 'w50',
				),
			'sql'       => "varchar(255) NOT NULL",
	    ),
		'conf_template' => array
	    (
	      'label'                   => &$GLOBALS['TL_LANG']['tl_wishlist']['conf_template'],
	      'exclude'                 => true,
	      'search'                  => true,
	      'sorting'                 => false,
	      'inputType'               => 'textarea',
	      'eval'                    => array
	      		(
	      			'style' => 'width: 100%; height: 200px;',
	      		),
	      'sql'                     => "text NULL"
	    ),
	    'conf_sender' => array
	    (
	      	'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['conf_sender'],
			'inputType' => 'text',
			'exclude'   => true,
			'flag'      => 1,
			'eval'      => array(
				'mandatory'   	=> true,
	            'unique'        => false,
	            'maxlength'   	=> 255,
				'tl_class'      => 'w50',
				'rgxp'			=> 'extnd',
				),
			'sql'       => "varchar(255) NOT NULL",
	    ),
	    'conf_senderMail' => array
	    (
	      	'label'     => &$GLOBALS['TL_LANG']['tl_wishlist']['conf_senderMail'],
			'inputType' => 'text',
			'exclude'   => true,
			'flag'      => 1,
			'eval'      => array(
				'mandatory'   	=> true,
                'unique'        => false,
                'maxlength'   	=> 255,
				'tl_class'      => 'w50',
				'rgxp'			=> 'mail',
				),
			'sql'       => "varchar(255) NOT NULL",
	    ),
	)
);

class tl_wishlist extends Backend
{
	public function optionsCallbackListForms()
	{
		$arrForms = array();
		$objForms = $this->Database->execute("SELECT id, title FROM tl_form ORDER BY title");

		while ($objForms->next())
		{
			$arrForms[$objForms->id] = $objForms->title;
		}

		return $arrForms;
	}
}
<?php
 
class ModuleWishlist extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_wishlist';
 
	/**
	 * Compile the current element
	 */
	protected function compile()
	{
		/** @var \Contao\Database\Result $rs */
		$db   = Database::getInstance();
		//$content_stmnt = $db->prepare("SELECT * FROM tl_content WHERE id=?");
		//$res  = $content_stmnt->execute($this->id);
 		//$res->next();
 		//$content = $res->row();
 		//die(print_r(deserialize($this->wishlist_categories, true)));
 		$categoriesID = deserialize($this->wishlist_categories, true);
 		$catResult = $db->execute("SELECT * FROM tl_wishcategory WHERE id IN (" . implode ( ',', $categoriesID)  . ") ORDER BY sorting ASC");
 		$categories = $catResult->fetchAllAssoc();

 		foreach ($categories as $key => $category)
 		{
 			$wishStMnt = $db->prepare("SELECT * FROM tl_wish WHERE pid=? ORDER BY sorting ASC");
 			$wishResult = $wishStMnt->execute($category['id']);
 			if($wishResult->count() > 0)
 			{
 				$wishes = $wishResult->fetchAllAssoc();
 				foreach ($wishes as $wkey => $wish)
 				{
 					if($wish['image'])
 					{
 						$wishes[$wkey]['imagesrc'] = \FilesModel::findByPk($wish['image'])->path;
 					}
 				}
 				$categories[$key]['wishes'] = $wishes;
 			}
 			
 		}

 		$wishlistResult = $db->prepare("SELECT form, currency_short FROM tl_wishlist WHERE id=?")->execute($this->wishlist);

 		$this->Template->formid 		= $wishlistResult->first()->form;
 		$this->Template->categories 	= $categories;
 		$this->Template->isEditable 	= $this->wishlistIsEditable;
 		$this->Template->wishlistid 	= $this->wishlist;
 		$this->Template->currency_short = $wishlistResult->first()->currency_short;
		
	}
} 

?>
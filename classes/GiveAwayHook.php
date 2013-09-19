<?php


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;
 
class GiveAwayHook extends Controller {
    public function processGiveAway($arrPost, $arrForm, $arrFiles)
    {
    	$dc = Database::getInstance();

    	//evaluate if the form belongs to a wishlist or not
    	$wishlistsResult = $dc->prepare("SELECT tl_wishlist.* FROM tl_wishlist INNER JOIN tl_form ON tl_wishlist.form=tl_form.id WHERE tl_form.id=? AND tl_wishlist.id=?")->execute(array($arrForm["id"], \Input::post("wishlistid")));
    	$wishlist = $wishlistsResult->fetchAllAssoc()[0];
    	$giveaway_total = 0;
    	if($wishlistsResult->count() > 0)
    	{
    		$giveaway  = array();
			$giveaway['tstamp'] = time();
			$giveaway['firstname'] = \Input::post("firstname");
			$giveaway['lastname'] = \Input::post("lastname");
			$giveaway['mail'] = \Input::post("mail");
			$wishlistResult = $dc->prepare("SELECT id FROM tl_wishlist WHERE id=?")->execute(\Input::post("wishlistid"));
			$giveaway['pid'] = $wishlistResult->id;

			$objData = $dc->prepare("INSERT INTO tl_giveaway %s")->set($giveaway)->execute();
			$newObjData = $dc->prepare("SELECT id FROM tl_giveaway WHERE tstamp=?")->execute($giveaway['tstamp']);
			$parent = $newObjData->fetchAllAssoc()[0];

			$wishStMnt = $dc->prepare("SELECT tl_wish.* FROM tl_wish JOIN tl_wishcategory ON tl_wish.pid=tl_wishcategory.id JOIN tl_wishlist ON tl_wishlist.id= tl_wishcategory.pid  WHERE tl_wishlist.id = ?");
	 		$wishResult = $wishStMnt->execute($wishlistResult->id);
	 		while ($wishResult->next())
	 		{
	 			$nofItems = Input::post('wish' . $wishResult->row()['id']);
	 			if($wishResult->wishtype == 'shares')
	 			{
	 				$nofItems = Input::post('wish' . $wishResult->row()['id']);
		 			if($nofItems  != '' && $nofItems > 0)
		 			{
		 				$giveawayitem = array();
		 				$giveawayitem['wid'] = $wishResult->row()['id'];
		 				$giveawayitem['pid'] = $parent[id];
		 				$giveawayitem['nofItems'] = $nofItems;
		 				$oldWishResult = $dc->prepare("SELECT id, nofItemsAvailable, pricepershare FROM tl_wish WHERE id=?")->execute($giveawayitem['wid']);
		 				$oldwish = $oldWishResult->fetchAllAssoc()[0];
		 				$newamount = $oldwish['nofItemsAvailable'] - $giveawayitem['nofItems'];

		 				if($newamount >= 0)
		 				{
		 					$giveaway_total = $giveaway_total + $giveawayitem['nofItems'] * $oldwish['pricepershare'];
		 					$objData = $dc->prepare("INSERT INTO tl_giveawayitem %s")->set($giveawayitem)->execute();
		 					$objData = $dc->prepare("UPDATE `tl_wish` SET `nofItemsAvailable`=? WHERE id=?")->execute(array($newamount, $giveawayitem['wid']));
		 				}
		 			}
	 			}
	 			else if($wishResult->wishtype == 'amount')
	 			{
	 				$amount = Input::post('wish' . $wishResult->row()['id']);
	 				if($wishResult->targetamount >=  $wish->accievedamount + $amount)
	 				{
	 					$giveawayitem = array();
		 				$giveawayitem['wid'] = $wishResult->row()['id'];
		 				$giveawayitem['pid'] = $parent[id];
		 				$giveawayitem['amount'] = $amount;
		 				$oldWishResult = $dc->prepare("SELECT id, accievedamount FROM tl_wish WHERE id=?")->execute($giveawayitem['wid']);
		 				$oldwish = $oldWishResult->fetchAllAssoc()[0];
		 				$newamount = $oldwish['accievedamount'] + $giveawayitem['amount'];

		 				if($newamount > $wish->accievedamount)
		 				{
		 					$giveaway_total = $giveaway_total + $giveawayitem['amount'];
		 					$objData = $dc->prepare("INSERT INTO tl_giveawayitem %s")->set($giveawayitem)->execute();
		 					$objData = $dc->prepare("UPDATE `tl_wish` SET `accievedamount`=? WHERE id=?")->execute(array($newamount, $giveawayitem['wid']));
		 				}
	 				}
	 			}
	 		}
	 		if($wishlist['send_confirmation'])
	 		{
	 			$this->sendConfirmation($wishlist, $giveaway, $giveaway_total);
	 		}
    	}
    }

    public function sendConfirmation($wishlist, $giveaway, $total_amount)
    {
    	$str_total_amount = $wishlist['currency_short'] . ' ' . $total_amount;
    	$content = str_replace ( '{{giveaway:amount}}' , $str_total_amount , $wishlist['conf_template']);

    	$str_name = $giveaway['firstname'] . ' ' . $giveaway['lastname'];
		$content = str_replace ( '{{giveaway:fullname}}' , $str_name , $content);

		$content = str_replace ( '{{giveaway:firstname}}' , $giveaway['firstname'] , $content);
		$content = str_replace ( '{{giveaway:lastname}}' , $giveaway['lastname'] , $content);


    	$mail = new Email();
    	$mail->subject = $wishlist['conf_subject'];
    	$mail->text = $content;
    	$mail->from = $wishlist['conf_senderMail'];
    	$mail->fromName = $wishlist['conf_sender'];
    	//$mail->charset = 'utf-8';
    	$mail->sendTo($giveaway['mail']);
    }
}

?>
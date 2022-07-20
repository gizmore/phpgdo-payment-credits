<?php
namespace GDO\PaymentCredits;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Payment\Orderable;
use GDO\Payment\PaymentModule;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Success;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Core\GDT_UInt;
/**
 * Order own credits and pay with another payment processor.
 * @author gizmore
 */
final class GDO_CreditsOrder extends GDO implements Orderable
{
	public function isPriceWithTax() { return false; }
	
	public function paymentCredits() { return Module_PaymentCredits::instance(); }
	public function getOrderCancelURL(GDO_User $user) { return url('PaymentCredits', 'OrderCredits', "&order={$this->getID()}&cancel=1"); }
	public function getOrderSuccessURL(GDO_User $user) { return url('PaymentCredits', 'OrderCredits'); }
	
	public function getOrderTitle($iso) { return t('card_title_credits_order', [$this->getCredits()]); }
	public function getOrderPrice()
	{
// 		$price = $this->paymentCredits()->creditsToPrice($this->getCredits());
		
		return $this->paymentCredits()->creditsToPrice($this->getCredits());
	}
	
	public function displayPrice() { return $this->paymentCredits()->displayPrice($this->getOrderPrice()); }
	public function canPayOrderWith(PaymentModule $module) { return !($module instanceof Module_PaymentCredits); }
	
	public function onOrderPaid()
	{
		$user = $this->getUser();
		$credits = $this->getCredits();
		$oldCredits = $user->getCredits();
		$user->increase('user_credits', $credits);
		$newCredits = $user->getCredits();
		return GDT_Success::make()->text('msg_credits_purchased', [$credits, $oldCredits, $newCredits]);
	}
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return array(
			GDT_AutoInc::make('co_id'),
			GDT_User::make('co_user')->notNull(),
			GDT_UInt::make('co_credits')->icon('credits')->notNull()->label('credits'),
		);
	}
	
	##############
	### Getter ###
	##############
	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->gdoValue('co_user'); }
	public function getUserID() { return $this->gdoVar('co_user'); }
	public function getCredits() { return $this->gdoVar('co_credits'); }
	
	##############
	### Render ###
	##############
	public function renderCard() { return GDT_Template::php('PaymentCredits', 'card/credits_order.php', ['gdo' => $this]); }
	public function renderOrderCard() { return $this->renderCard(); }

}

<?php
namespace GDO\PaymentCredits;

use GDO\Address\GDO_Address;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Decimal;
use GDO\Payment\GDO_Order;
use GDO\Payment\GDT_Money;
use GDO\Payment\Module_Payment;
use GDO\Payment\Orderable;
use GDO\Payment\PaymentModule;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * Pay with own credits.
 * Buy own credits.
 *
 * @version 7.0.1
 * @since 4.0.0
 * @author gizmore
 */
final class Module_PaymentCredits extends PaymentModule
{

	#####################
	### PaymentModule ###
	#####################
	public function makePaymentButton(GDO_Order $order = null)
	{
		$button = parent::makePaymentButton($order);
		return $button->label('buy_paymentcredits', [sitename()]);
	}

	##############
	### Module ###
	##############
	public function getDependencies(): array { return ['Payment']; }

	public function getClasses(): array { return ['GDO\PaymentCredits\GDO_CreditsOrder']; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/credits'); }

	public function getConfig(): array
	{
		return array_merge(parent::getConfig(), [
			GDT_Checkbox::make('paycreds_guests')->initial('0'),
			GDT_Checkbox::make('hook_sidebar')->initial('1'),
			GDT_Decimal::make('paycreds_min_purchase')->digits(6, 2)->initial('5.00'),
			GDT_Decimal::make('paycreds_rate')->digits(1, 4)->initial('0.01'),
		]);
	}

	##############
	### Config ###
	##############

	public function getUserConfig(): array
	{
		return [
			GDT_Credits::make('credits')->initial('0'),
		];
	}

	public function onInitSidebar(): void
	{
		if ($this->cfgRightBar())
		{
			$user = GDO_User::current();
			if ($user->isAuthenticated())
			{
				$navbar = GDT_Page::$INSTANCE->rightBar()->getField('menu_payment');
				$link = GDT_Link::make()->text('link_credits', [$user->getCredits()])->href(href('PaymentCredits', 'OrderCredits'));
				$navbar->addField($link);
			}
// 	        if ($user->isStaff())
// 	        {
// 	        	$navbar = GDT_Page::$INSTANCE->rightBar()->getField('menu_admin');
// 	        	$link = GDT_Link::make()->text('link_grant_credits')->href(href('PaymentCredits', 'GrantCredits'));
// 	            $navbar->addField($link);
// 	        }
		}
	}

	public function cfgRightBar() { return $this->getConfigValue('hook_sidebar'); }

	public function getPrice(Orderable $orderable, GDO_Address $address)
	{
		$price = $orderable->getOrderPrice();
		$price = round(($this->cfgFeeBuy() + 1.00) * floatval($price), 2);
		return $price;
	}

	public function payment() { return Module_Payment::instance(); }

	public function cfgAllowGuests() { return $this->getConfigValue('paycreds_guests'); }

	public function cfgMinPurchaseCredits() { return $this->priceToCredits($this->cfgMinPurchasePrice()); }

	public function priceToCredits($price) { return floor($this->cfgConversionRateToCredits() * $price); }

	################
	### Settings ###
	################

	public function cfgConversionRateToCredits() { return 1 / $this->cfgConversionRate(); }

	###############
	### Convert ###
	###############

	public function cfgConversionRate() { return $this->getConfigValue('paycreds_rate'); }

	public function cfgMinPurchasePrice() { return $this->getConfigValue('paycreds_min_purchase'); }

	public function displayCreditsPrice($credits) { return $this->displayPrice($this->creditsToPrice($credits)); }

	public function displayPrice($price) { return sprintf('%.02f %s', $price, GDT_Money::$CURR); }

	###############
	### Sidebar ###
	###############

	public function creditsToPrice($credits) { return round($this->cfgConversionRateToCurrency() * $credits, 2); }

	################
	### Override ###
	################

	public function cfgConversionRateToCurrency() { return $this->cfgConversionRate(); }

}

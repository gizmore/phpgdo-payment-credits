<?php
namespace GDO\PaymentCredits\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Payment\Orderable;
use GDO\Payment\Payment_Order;
use GDO\PaymentCredits\GDO_CreditsOrder;
use GDO\PaymentCredits\Module_PaymentCredits;
use GDO\UI\GDT_Paragraph;
use GDO\User\GDO_User;

/**
 * Order more gwf credits.
 *
 * @author gizmore
 */
final class OrderCredits extends Payment_Order
{

	public function getOrderable(): Orderable
	{
		return GDO_CreditsOrder::blank([
			'co_user' => GDO_User::current()->getID(),
			'co_credits' => $this->getForm()->getFormVar('co_credits'),
		]);
	}

	protected function createForm(GDT_Form $form): void
	{
		$module = Module_PaymentCredits::instance();
		$gdo = GDO_CreditsOrder::table();
		$form->addFields(
			GDT_Paragraph::make()->text('info_credits_exchange_rate', [
				$module->displayPrice($module->cfgConversionRate()),
			]),
			$gdo->gdoColumn('co_credits')->initial($module->cfgMinPurchaseCredits()),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function onCancelOrder(): void {}

}

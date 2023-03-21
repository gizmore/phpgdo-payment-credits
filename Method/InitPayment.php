<?php
namespace GDO\PaymentCredits\Method;

use GDO\Payment\GDO_Order;
use GDO\Payment\MethodPayment;

/**
 * Pay with own gwf credits.
 *
 * @version 6.10
 * @since 5.0
 * @author gizmore
 */
final class InitPayment extends MethodPayment
{

	public function getMethodTitle(): string
	{
		return t('payment');
	}

	public function execute()
	{
		if (!($order = $this->getOrderPersisted()))
		{
			return $this->error('err_order');
		}
		return $this->renderOrder($order)->addField($this->templateButton($order));
	}

	private function templateButton(GDO_Order $order)
	{
		return $this->templatePHP('paybutton.php', ['order' => $order]);
	}

}

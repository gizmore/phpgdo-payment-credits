<?php
namespace GDO\PaymentCredits\Method;

use GDO\Core\Method;
use GDO\Admin\MethodAdmin;
use GDO\UI\GDT_Dashboard;
use GDO\UI\GDT_Link;

final class Admin extends Method
{
	use MethodAdmin;
	
	public function execute()
	{
		return GDT_Dashboard::makeWith(
			GDT_Link::make()->href(href('PaymentCredits', 'GrantCredits')),
		);
	}
	
}

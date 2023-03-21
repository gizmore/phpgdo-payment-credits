<?php
namespace GDO\PaymentCredits;

use GDO\Core\GDT_UInt;

/**
 * Credit field.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Credits extends GDT_UInt
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('money');
	}

}

<?php

/**
 * This is the configuration-object
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

final class FrontendDealerConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}
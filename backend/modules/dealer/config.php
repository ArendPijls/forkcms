<?php

/**
 * The configuration-object for the dealer module.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

final class BackendDealerConfig extends BackendBaseConfig
{
	/**
	 * The default action.
	 *
	 * @var  string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions.
	 *
	 * @var  array
	 */
	protected $disabledActions = array();

	/**
	 * The disabled AJAX actions.
	 *
	 * @var  array
	 */
	protected $disabledAJAXActions = array();
}

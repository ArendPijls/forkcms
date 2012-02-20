<?php

/**
 * In this file we store all generic functions that we will be using in the dealer module
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class FrontendDealerModel
{
	/**
	 * Get the visible dealers with the given ID.
	 *
	 * @param int $id The id of the item to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getDB()->getRecord(
			'SELECT *
			 FROM dealer
			 WHERE id = ? AND hidden = ?
			 LIMIT 1',
			array((int) $id, 'N')
		);
	}

	/**
	 * Get all dealer.
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT *
			 FROM dealer
			 WHERE hidden = ? AND language = ?
			 ORDER BY sequence',
			array('N', FRONTEND_LANGUAGE)
		);
	}

	/**
	 * Get a random visible dealer.
	 *
	 * @return array
	 */
	public static function getRandom()
	{
		// get a random ID
		$allIds = FrontendModel::getDB()->getColumn(
			'SELECT id
			 FROM dealer
			 WHERE hidden = ? AND language = ?',
			array('N', FRONTEND_LANGUAGE)
		);

		// return an empty array when there are no visible dealers
		if(empty($allIds)) return array();

		// return the dealers with a random ID
		return self::get($allIds[array_rand($allIds)]);
	}
}
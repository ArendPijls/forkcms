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
	public static function getAll($area, $brands)
	{
		// loop selected brands and put them in a like %% query
		$extendWhereQuery ="";
		foreach($brands as $brand)
		{
			$extendWhereQuery  .= 'AND brands like "%;'.$brand.';%" ';
		}

		$tempArr = (array) FrontendModel::getDB()->getRecords(
				'SELECT *
				FROM dealer
				WHERE hidden = ? AND language = ? '.$extendWhereQuery.'
				ORDER BY sequence',
				array('N', FRONTEND_LANGUAGE)
		);

		$count=count($tempArr);

		// loop database records
		$authors = array();
		for($i=0;$i<$count;$i++){

			$authors[$i] = $tempArr[$i];
			$authors[$i]['name'] = $tempArr[$i]['name'];
			$checked = explode(';', $tempArr[$i]['brands']);
			foreach($checked as $brands)
			{
				$authors[$i]['brandInfo'][] = FrontendDealerModel::getBrand($brands);
			}

		}

		return $authors;
	}

	/**
	 * Get all the brands.
	 *
	 * @return array
	 */
	public static function getAllBrands()
	{
		return (array) FrontendModel::getDB()->getRecords(
				'SELECT *
				FROM dealer_brands',
				array()
		);
	}

	/**
	 * Get brand info.
	 *
	 * @param int $id The id of the item to fetch.
	 * @return array
	 */
	public static function getBrand($id)
	{
		return (array) FrontendModel::getDB()->getRecord(
			'SELECT *
			 FROM dealer_brands
			 WHERE id = ?
			 LIMIT 1',
			array((int) $id)
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
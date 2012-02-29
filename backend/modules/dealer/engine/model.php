<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * All model functions for the dealer locater module.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */
class BackendDealerModel
{
	/**
	 * Overview of the dealer locaters.
	 *
	 * @var	string
	 */
	const QRY_BROWSE =
		'SELECT id, name, avatar, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
	     FROM dealer
	     WHERE language = ?';

	/**
	 * Overview of the brands.
	 *
	 * @var	string
	 */
	const QRY_BROWSE_BRANDS =
		'SELECT id, name, image
		 FROM dealer_brands
		 WHERE language = ?';

	/**
	 * Delete a brand.
	 *
	 * @param int $id 		The id of the dealer locater to delete.
	 */
	public static function deleteBrand($id)
	{
		// get image file name
		$imageFilname = (string) BackendModel::getDB()->getVar(
			'SELECT image
			 FROM dealer_brands
			 WHERE id = ?',
			array((int) $id)
		);

		// delete brand images
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/brands/source/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/brands/128x128/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/brands/64x64/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/brands/32x32/' . $imageFilname);

		// delete brand
		BackendModel::getDB(true)->delete('dealer_brands', 'id = ?', array((int) $id));
	}

	/**
	 * Delete a dealer.
	 *
	 * @param int $id The id of the dealer locater to delete.
	 */
	public static function deleteDealer($id)
	{
		// get avatar file name
		$imageFilname = (string) BackendModel::getDB()->getVar(
			'SELECT avatar
			 FROM dealer
			 WHERE id = ?',
			array((int) $id)
		);

		// delete brand images
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/avatars/source/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/avatars/128x128/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/avatars/64x64/' . $imageFilname);
		SpoonFile::delete(FRONTEND_FILES_PATH . '/dealer/avatars/32x32/' . $imageFilname);

		// delete dealer
		BackendModel::getDB(true)->delete('dealer', 'id = ?', array((int) $id));
	}

	/**
	 * Does the brand exist?
	 *
	 * @param	int $id		The id of the brand to check for existence.
	 * @return bool
	 */
	public static function existsBrand($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM dealer_brands
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Does the dealer locater exist?
	 *
	 * @param int $id The id of the dealer to check for existence.
	 * @return bool
	 */
	public static function existsDealer($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM dealer
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get all the brands.
	 *
	 * @return array
	 */
	public static function getAllBrands()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT *
			 FROM dealer_brands
			 WHERE language = ?',
			array(BL::getWorkingLanguage())
		);
	}

	/**
	 * Get all data for the brand with the given ID.
	 *
	 * @param int $id The id for the dealer locater to get.
	 * @return array
	 */
	public static function getBrand($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM dealer_brands
			 WHERE id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Get all data for the one dealer locater with the given ID.
	 *
	 * @param int $id The id for the dealer locater to get.
	 * @return array
	 */
	public static function getDealer($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, m.url
			 FROM dealer AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Get all data for the brands with the given dealer ID.
	 *
	 * @param int $id The id of the dealer locator for getting the brands
	 * @return array
	 */
	public static function getDealerBrands($id)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT brand_id
			 FROM dealer_index
			 WHERE dealer_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Retrieve the unique URL for an item
	 *
	 * @param string $URL The URL to base on.
	 * @param int[optional] $id The id of the item to ignore.
	 * @return string
	 */
	public static function getURL($URL, $id = null)
	{
		$URL = (string) $URL;

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar(
					'SELECT COUNT(i.id)
					FROM dealer AS i
					INNER JOIN meta AS m ON i.meta_id = m.id
					WHERE i.language = ? AND m.url = ?',
					array(BL::getWorkingLanguage(), $URL)
			);

			// already exists
			if($number != 0)
			{
				$URL = BackendModel::addNumber($URL);
				return self::getURL($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar(
					'SELECT COUNT(i.id)
					FROM dealer AS i
					INNER JOIN meta AS m ON i.meta_id = m.id
					WHERE i.language = ? AND m.url = ? AND i.id != ?',
					array(BL::getWorkingLanguage(), $URL, $id)
			);

			// already exists
			if($number != 0)
			{
				$URL = BackendModel::addNumber($URL);
				return self::getURL($URL, $id);
			}
		}

		return $URL;
	}

	/**
	 * Add a new dealer locater.
	 *
	 * @param array $item The data to insert.
	 * @return int The ID of the newly inserted dealer locater.
	 */
	public static function insertDealer(array $item)
	{
		return BackendModel::getDB(true)->insert('dealer', $item);
	}

	/**
	 * Add a new brand.
	 *
	 * @param array $item The data to insert.
	 * @return int The ID of the newly inserted brand.
	 */
	public static function insertBrand(array $item)
	{
		return BackendModel::getDB(true)->insert('dealer_brands', $item);
	}

	/**
	 * Update an existing dealer locater.
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateDealer(array $item)
	{
		return BackendModel::getDB(true)->update('dealer', $item, 'id = ?', array((int) $item['id']));
	}

	/**
	 * Update an existing brand.
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateBrand(array $item)
	{
		return BackendModel::getDB(true)->update('dealer_brands', $item, 'id = ?', array((int) $item['id']));
	}

	/**
	 * Update brand index for dealer
	 *
	 * @param   int $id         The dealer id
	 * @param   array $brands 	Array with all dealer brands
	 * @return void
	 */
	public static function updateBrandsForDealer($id, $brands)
	{
		BackendModel::getDB(true)->delete('dealer_index', 'dealer_id = ?', array((int) $id));

		$brands = (array) $brands;

		foreach($brands as $brand)
		{
			BackendModel::getDB(true)->insert('dealer_index', array('dealer_id' => $id, 'brand_id' => $brand));
		}
	}
}

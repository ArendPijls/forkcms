<?php

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
		'SELECT id, name, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
	     FROM dealer
	     WHERE language = ?
	     ORDER BY sequence';
	/**
	 * Overview of the brands.
	 *
	 * @var	string
	 */
	const QRY_BROWSE_BRANDS =
		'SELECT id, name, image
		FROM dealer_brands';

	/**
	 * Delete a dealer.
	 *
	 * @param int $id The id of the dealer locater to delete.
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('dealer', 'id = ?', array((int) $id));
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
	 * Does the brand exist?
	 *
	 * @param int $id The id of the brand to check for existence.
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
	 * Get all data for the dealer locater with the given ID.
	 *
	 * @param int $id The id for the dealer locater to get.
	 * @return array
	 */
	public static function getDealer($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *, UNIX_TIMESTAMP(created_on) AS created_on, UNIX_TIMESTAMP(edited_on) AS edited_on
		     FROM dealer
		     WHERE id = ?
		     LIMIT 1',
			array((int) $id)
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
	 * Get all the brands.
	 *
	 * @return array
	 */
	public static function getAllBrands()
	{
		return (array) BackendModel::getDB()->getRecords(
				'SELECT *
				FROM dealer_brands',
				array()
		);
	}
	/**
	 * Get the max sequence id for a dealer locater
	 *
	 * @return int
	 */
	public static function getMaximumSequence()
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(sequence)
			 FROM dealer'
		);
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
}
<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the dealer module
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */
class FrontendDealerModel
{
	/**
	 * Get an dealer locator
	 *
	 * @param string $URL The URL for the item.
	 * @return array
	 */
	public static function get($URL)
	{
		$return = (array) FrontendModel::getDB()->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			 m.url,
			 m.data AS meta_data
			 FROM dealer AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ? AND i.hidden = ? AND m.url = ?
			 LIMIT 1',
			array(FRONTEND_LANGUAGE, 'N', (string) $URL)
		);

		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);

		// init url
		$linkLocator = FrontendNavigation::getURLForBlock('dealer', 'locator');
		$return['full_url'] = $linkPlace . '/' . $return['url'];

		// add brands
		$brands = FrontendDealerModel::getDealerBrands($return['id']);;
		foreach($brands as $brand)
		{
			$return['brandInfo'][] = FrontendDealerModel::getBrand($brand['brand_id']);
		}

		// return
		return $return;
	}

	/**
	 * Get all dealer.
	 *
	 * @param string $area 			The city or postcode
	 * @param array $brands 		An array of selected brands
	 * @param string $country 		Search only in: BE, FR and NL
	 * @return array
	 */
	public static function getAll($area, $brands, $country)
	{
		// get module settings
		$moduleSettings = FrontendModel::getModuleSettings('dealer');
		$limit = $moduleSettings['limit'];
		$distance = $moduleSettings['distance'];
		$unit = $moduleSettings['units'];

		// The url for quering Google Maps api to get latitude/longitude coordinates for an address.
		$urlGoogleMaps = 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';

		// build address & full url to google
		$fullAddress = $area . ', ' . $country;
		$url = sprintf($urlGoogleMaps, urlencode($fullAddress));

		// fetch data from google
		$geocode = json_decode(SpoonHTTP::getContent($url));

		// results found?
		$lat = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
		$lng = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;

		// radius of earth; @note: the earth is not perfectly spherical, but this is considered the 'mean radius'
		if($unit == 'KM') $radius = 6371.009; // in kilometers
		elseif($unit == 'MILES') $radius = 3958.761; // in miles

		// latitude boundaries
		$maxLat = (float) $lat + rad2deg($distance / $radius);
		$minLat = (float) $lat - rad2deg($distance / $radius);

		// longitude boundaries (longitude gets smaller when latitude increases)
		$maxLng = (float) $lng + rad2deg($distance / $radius / cos(deg2rad((float) $lat)));
		$minLng = (float) $lng - rad2deg($distance / $radius / cos(deg2rad((float) $lat)));

		// show only dealers in selected country
		$sqlCountry = "";
		if($country != "") $sqlCountry = " AND country = '" . $country . "'";

		// show only selected brands
		$sqlBrands = "";
		if(!empty($brands)) $sqlBrands = ' AND di.brand_id IN (' . implode(',', $brands) . ')';

		// set db records in temp arr
		$tempArr = (array) FrontendModel::getDB()->GetRecords(
			'SELECT *, d.name as name
			 FROM dealer AS d
			 INNER JOIN dealer_index AS di ON di.dealer_id = d.id
			 INNER JOIN dealer_brands AS b ON di.brand_id = b.id
			 INNER JOIN meta AS m ON d.meta_id = m.id
			 WHERE d.language = ? AND d.lat > ? AND d.lat < ? AND d.lng > ? AND d.lng < ? AND d.hidden = ? ' . $sqlCountry . ' ' . $sqlBrands . '
			 GROUP BY dealer_id
			 ORDER BY ABS(d.lat - ?) + ABS(d.lng - ?) ASC
			 LIMIT ?',
			array(FRONTEND_LANGUAGE, $minLat, $maxLat, $minLng, $maxLng, 'N', (float) $lat, (float) $lng, (int) $limit)
		);

		// loop db records and add brand info
		$dealers = array();
		for($i=0; $i < count($tempArr); $i++)
		{
			// init url
			$dealers[$i] = $tempArr[$i];
			$linkLocator = FrontendNavigation::getURLForBlock('dealer', 'locator');
			$dealers[$i]['full_url'] = $linkLocator . '/' . $dealers[$i]['url'];

			$brands = FrontendDealerModel::getDealerBrands($dealers[$i]['dealer_id']);;
			foreach($brands as $brand)
			{
				$dealers[$i]['brandInfo'][] = FrontendDealerModel::getBrand($brand['brand_id']);
			}
		}
		return $dealers;
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
			 FROM dealer_brands
			 WHERE language = ?',
			array(FRONTEND_LANGUAGE)
		);
	}

	/**
	 * Get an dealer locator
	 *
	 * @param string $URL The URL for the item.
	 * @return array
	 */
	public static function getBrandInfo($URL)
	{
		$return = (array) FrontendModel::getDB()->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			 m.url,
			 m.data AS meta_data
			 FROM dealer_brands AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ? AND m.url = ?
			 LIMIT 1',
			array(FRONTEND_LANGUAGE, (string) $URL)
		);

		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);

		// return
		return $return;
	}

	/**
	 * Get all data of the dealers with the given ID.
	 *
	 * @param int $id The id of the item to fetch.
	 * @return array
	 */
	public static function getBrandDealers($id)
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT dealer_id
			 FROM dealer_index
			 WHERE brand_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get all data for the brand with the given ID.
	 *
	 * @param int $id The id for the dealer locater to get.
	 * @return array
	 */
	public static function getDealerBrands($id)
	{
		$return = (array) FrontendModel::getDB()->getRecords(
			'SELECT *
			 FROM dealer AS d
			 INNER JOIN dealer_index AS di ON d.id = di.dealer_id
			 INNER JOIN meta AS m ON d.meta_id = m.id
			 WHERE di.brand_id = ?',
			array((int) $id)
		);

		// loop db records and add full url
		for($i=0; $i < count($return); $i++)
		{
			// init url
			$linkLocator = FrontendNavigation::getURLForBlock('dealer', 'locator');
			$return[$i]['full_url'] = $linkLocator . '/' . $return[$i]['url'];
		}

		return $return;
	}

	/**
	 * Parse the search results for this module
	 *
	 * Note: a module's search function should always:
	 * 		- accept an array of entry id's
	 * 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	 *
	 *
	 * @param array $ids The ids of the found results.
	 * @return array
	 */
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getDB()->getRecords(
			'SELECT i.id, i.name, m.url, i.name as title, i.name as text
			 FROM dealer AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ? AND i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
			array('N', FRONTEND_LANGUAGE), 'id'
		);

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = FrontendNavigation::getURLForBlock('dealer', 'locator') . '/' . $item['url'];
		}

		// return
		return $items;
	}
}
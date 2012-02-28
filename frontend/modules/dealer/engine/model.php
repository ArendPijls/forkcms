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
		if($country == "BE") $fullCountryText = "Belgium";
		if($country == "NL") $fullCountryText = "Netherlands";
		if($country == "FR")
		{
			$fullCountryText = "France";
		}
		else
		{
			$fullCountryText = "Belgium";
		}
		$fullAddress = $area . ', ' . $fullCountryText;
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
		if($country == "BE" || $country == "FR" || $country == "NL") $sqlCountry = " AND country = '" . $country . "'";

		// show only selected brands
		$sqlBrands = "";
		if(!empty($brands)) $sqlBrands = 'AND di.brand_id IN (' . implode(',', $brands) . ')';

		// set db records in temp arr
		$tempArr = (array) FrontendModel::getDB()->GetRecords(
				'SELECT *, d.name as name
				FROM dealer AS d
				INNER JOIN dealer_index AS di ON di.dealer_id = d.id
				INNER JOIN dealer_brands AS b ON di.brand_id = b.id
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
			$dealers[$i] = $tempArr[$i];
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
	 * Get all data for the brand with the given ID.
	 *
	 * @param int $id The id for the dealer locater to get.
	 * @return array
	 */
	public static function getDealerBrands($id)
	{
		return (array) FrontendModel::getDB()->getRecords(
				'SELECT brand_id
				FROM dealer_index
				WHERE dealer_id = ?',
				array((int) $id)
		);
	}
}
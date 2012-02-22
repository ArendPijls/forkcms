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
	public static function getAll($area, $brands, $country, $limit = 50, $distance = 25, $unit = 'km')
	{


		// The url for quering Google Maps api to get latitude/longitude coordinates for an address.
		$urlGoogleMaps = 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';

		// build address & full url to google
		$fullAddress = $area . ', Belgium';
		$url = sprintf($urlGoogleMaps, urlencode($fullAddress));

		// fetch data from google
		$geocode = json_decode(SpoonHTTP::getContent($url));

		// results found?
		$lat = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
		$lng = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;

		// radius of earth; @note: the earth is not perfectly spherical, but this is considered the 'mean radius'
		if($unit == 'km') $radius = 6371.009; // in kilometers
		elseif($unit == 'mi') $radius = 3958.761; // in miles

		// latitude boundaries
		$maxLat = (float) $lat + rad2deg($distance / $radius);
		$minLat = (float) $lat - rad2deg($distance / $radius);

		// longitude boundaries (longitude gets smaller when latitude increases)
		$maxLng = (float) $lng + rad2deg($distance / $radius / cos(deg2rad((float) $lat)));
		$minLng = (float) $lng - rad2deg($distance / $radius / cos(deg2rad((float) $lat)));


		// loop selected brands and put them in a like %% query
		$extendWhereQuery = "";
		foreach($brands as $brand)
		{
			$extendWhereQuery  .= 'AND brands like "%;'.$brand.';%" ';
		}

		// show only dealers around users loctation
		if($country == "AROUND") $country = "BE";

		// set db records in temp arr
		$tempArr = (array) FrontendModel::getDB()->getRecords(
				'SELECT *
				FROM dealer
				WHERE lat > ? AND lat < ? AND lng > ? AND lng < ? AND hidden = ? AND country = ? '.$extendWhereQuery.'
				ORDER BY ABS(lat - ?) + ABS(lng - ?) ASC
				LIMIT ?',
				array($minLat, $maxLat, $minLng, $maxLng, 'N', $country, (float) $lat, (float) $lng, (int) $limit)
		);

		// loop db records and add brand info
		$dealers = array();
		for($i=0; $i < count($tempArr); $i++)
		{
			$dealers[$i] = $tempArr[$i];
			$brands = explode(';', $tempArr[$i]['brands']);
			foreach($brands as $brand)
			{
				$dealers[$i]['brandInfo'][] = FrontendDealerModel::getBrand($brand);
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

}
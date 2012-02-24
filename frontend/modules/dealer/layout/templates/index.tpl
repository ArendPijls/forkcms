{*
	variables that are available:
	- {$dealerItems}: contains data about all dealers
	- {$dealerHeadingText|sprintf:{$numDealers}}
	- {$dealerErrorNoDealers}
	- {$lblBrands}
	- {$lblCityOrZip|ucfirst} {$lblTel} {$lblFax} {$lblAddress} {$lblNumber} {$lblSite}
	- {$lblRequiredField}
	- {$lblFindDealer|ucfirst}
	- {$msgDealerNoItems}
	- {$msgViewOnMap}
	- {$msgViewOnBigMap}
*}
{form:searchForm}
	<div class="alignBlocks">
		<p {option:txtAreaError}class="errorArea"{/option:txtAreaError}>
			<label for="area">{$lblCityOrZip|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtArea} {$txtAreaError}
		</p>
	</div>
	<div>
		<p>
			{$ddmCountry}
		</p>
	</div>
	<div>
		{option:type}
			<ul>
				{iteration:type}<li>{$type.chkType} <label for="{$type.id}">{$type.label|ucfirst}</label></li>{/iteration:type}
			</ul>
		{/option:type}
	</div>
	<p>
		<input class="inputSubmit" type="submit" name="Search" value="{$lblFindDealer|ucfirst}" />
	</p>
{/form:searchForm}

{option:dealerErrorNoDealers}{$msgDealerNoItems}{/option:dealerErrorNoDealers}

{option:dealerItems}
	<h2>{$dealerHeadingText|sprintf:{$numDealers}}</h2>
	<div id="map" style="height: {$dealerSettings.height}px; width: {$dealerSettings.width}px;"></div>

	{* Store item text in a div because JS goes bananas with multiline HTML *}
	{iteration:dealerItems}
		<div id="markerText{$dealerItems.dealer_id}" style="display:none;">	
			{option:dealerItems.avatar}
				<img src="{$FRONTEND_FILES_URL}/frontend_dealer/avatars/64x64/{$dealerItems.avatar}" width="64" height="64" alt="" style="float:right; margin: 5px;" />
			{/option:dealerItems.avatar}
										
			{$dealerItems.street} {$dealerItems.number}<br>
			{$dealerItems.zip} {$dealerItems.city} <br>
			
			{option:dealerItems.tel}
				{$lblTel}: {$dealerItems.tel} <br>
			{/option:dealerItems.tel}
			
			{option:dealerItems.fax}
				{$lblFax}: {$dealerItems.fax} <br>
			{/option:dealerItems.fax}
			
			{option:dealerItems.email}
				{$lblEmail}: {$dealerItems.email} <br>
			{/option:dealerItems.email}
									
			{option:dealerItems.site}
				{$lblSite}: {$dealerItems.site} <br>
			{/option:dealerItems.site}
			
			<strong>{$lblBrands}</strong> <br>
        	{iteration:dealerItems.brandInfo}
        		{option:dealerItems.brandInfo.name}
           			{$dealerItems.brandInfo.name}, 
           		{/option:dealerItems.brandInfo.name}
        	{/iteration:dealerItems.brandInfo}
        	<a href="http://maps.google.com/?q={$dealerItems.street|urlencode}+{$dealerItems.number|urlencode}+{$dealerItems.zip|urlencode}+{$dealerItems.city|urlencode}" target="_blank">{$msgViewOnBigMap}</a>
		</div>
	{/iteration:dealerItems}

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript">
	
		var marker =new Array();
		
		var initialize = function()
		{
			// create boundaries
			var latlngBounds = new google.maps.LatLngBounds();
			
			
			// set options
			var options =
			{
				// set zoom as defined by user, or as 0 if to be done automatically based on boundaries
				zoom: '{$dealerSettings.zoom_level}' == 'auto' ? 0 : {$dealerSettings.zoom_level},
				// set default center as first item's location
				center: new google.maps.LatLng({$dealerItems.0.lat}, {$dealerItems.0.lng}),
				// no interface, just the map
				disableDefaultUI: false,
				// dragging the map around
				draggable: true,
				// no zooming in/out using scrollwheel
				scrollwheel: false,
				// no double click zoom
				disableDoubleClickZoom: true,
				// set map type
				mapTypeId: google.maps.MapTypeId.{$dealerSettings.map_type}
			};

			// create map
			var map = new google.maps.Map(document.getElementById('map'), options);


			// function to add markers to the map
			function addMarker(id, lat, lng, title, text)
			{
				// create position
				position = new google.maps.LatLng(lat, lng);

				// add to boundaries
				latlngBounds.extend(position);

				// add marker
				
				marker[id] = new google.maps.Marker(
				{
					// set position
					position: position,
					// add to map
					map: map,
					// set title
					title: title
				});

				// add click event on marker
				google.maps.event.addListener(marker[id], 'click', function()
				{
					// create infowindow
					new google.maps.InfoWindow({ content: '<h1>'+ title +'</h1>' + text }).open(map, marker[id]);
				});
			}
			
			// loop items and add to map
			{iteration:dealerItems}
				{option:dealerItems.lat}{option:dealerItems.lng}addMarker({$dealerItems.dealer_id}, {$dealerItems.lat}, {$dealerItems.lng}, '{$dealerItems.name}', $('#markerText' + {$dealerItems.dealer_id}).html());{/option:dealerItems.lat}{/option:dealerItems.lng}
			{/iteration:dealerItems}

			// set center to the middle of our boundaries
			map.setCenter(latlngBounds.getCenter());

			// set zoom automatically, defined by points (if allowed)
			if('{$dealerSettings.zoom_level}' == 'auto') map.fitBounds(latlngBounds);
			
		}
		
		function openMarker(id, title, text)
		{
			// create infowindow
			new google.maps.InfoWindow({ content: '<h1>'+ title +'</h1>' + text }).open(map, marker[id]);
		}			

		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	<div id="dealerItems">
		{iteration:dealerItems}
			<div style="width:900px; height:180px;">
				<div class="inner">
					<h4>{$dealerItems.name}</h4>
					{option:dealerItems.avatar}
						<img src="{$FRONTEND_FILES_URL}/frontend_dealer/avatars/128x128/{$dealerItems.avatar}" width="128" height="128" alt="" style="float:left; margin: 5px;" />
					{/option:dealerItems.avatar}
					<a href="#" onClick="openMarker({$dealerItems.dealer_id}, '{$dealerItems.name}', $('#markerText' + {$dealerItems.dealer_id}).html());">{$msgViewOnMap}</a> <br>
					<a href="http://maps.google.com/?q={$dealerItems.street|urlencode}+{$dealerItems.number|urlencode}+{$dealerItems.zip|urlencode}+{$dealerItems.city|urlencode}" target="_blank">{$msgViewOnBigMap}</a>
					<div style="width:300px;  float:left;">
						{$dealerItems.street} {$dealerItems.number} <br>
						{$dealerItems.zip} {$dealerItems.city} <br>
						
						{option:dealerItems.tel}
							{$lblTel}: {$dealerItems.tel} <br>
						{/option:dealerItems.tel}
						
						{option:dealerItems.fax}
							{$lblFax}: {$dealerItems.fax} <br>
						{/option:dealerItems.fax}
						
						{option:dealerItems.email}
							{$lblEmail}: {$dealerItems.email} <br>
						{/option:dealerItems.email}
												
						{option:dealerItems.site}
							{$lblSite}: {$dealerItems.site} <br>
						{/option:dealerItems.site}
					</div>
					<div style="width:400px; float:left;">
						<strong>{$lblBrands}</strong> <br>
				        	 <ul>
				            	{iteration:dealerItems.brandInfo}
				            		{option:dealerItems.brandInfo.name}
				               			<li>
				               			<img src="{$FRONTEND_FILES_URL}/frontend_dealer/brands/32x32/{$dealerItems.brandInfo.image}" width="32" height="32" alt="" style="float:left; margin: 5px;" />
				               			{$dealerItems.brandInfo.name}
				               			</li>
				               		{/option:dealerItems.brandInfo.name}
				            	{/iteration:dealerItems.brandInfo}
				        	 </ul>
						</ul>
					</div>
				</div>
			</div>
		{/iteration:dealerItems}
	</div>
{/option:dealerItems}
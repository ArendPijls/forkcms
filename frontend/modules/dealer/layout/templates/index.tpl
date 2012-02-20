{*
	variables that are available:
	- {$dealerItems}: contains data about all testimonials
*}

{option:!dealerItems}
	<div id="dealerItems">
		<div class="mod">
			<div class="inner">
				<div class="bd">
					<p>{$msgDealerNoItems}</p>
				</div>
			</div>
		</div>
	</div>
{/option:!dealerItems}
{option:dealerItems}
	<div id="map" style="height: {$dealerSettings.height}px; width: {$dealerSettings.width}px;"></div>

	{* Store item text in a div because JS goes bananas with multiline HTML *}
	{iteration:dealerItems}
		<div id="markerText{$dealerItems.id}" style="display:none;">						
			{$dealerItems.street} <br>
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
	{/iteration:dealerItems}

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
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
				disableDefaultUI: true,
				// no dragging the map around
				draggable: true,
				// no zooming in/out using scrollwheel
				scrollwheel: false,
				// no double click zoom
				disableDoubleClickZoom: false,
				// set map type
				mapTypeId: google.maps.MapTypeId.{$dealerSettings.map_type}
			};

			// create map
			var map = new google.maps.Map(document.getElementById('map'), options);

			// function to add markers to the map
			function addMarker(lat, lng, title, text)
			{
				// create position
				position = new google.maps.LatLng(lat, lng);

				// add to boundaries
				latlngBounds.extend(position);

				// add marker
				var marker = new google.maps.Marker(
				{
					// set position
					position: position,
					// add to map
					map: map,
					// set title
					title: title
				});

				// add click event on marker
				google.maps.event.addListener(marker, 'click', function()
				{
					// create infowindow
					new google.maps.InfoWindow({ content: '<h2>'+ title +'</h2>' + text }).open(map, marker);
				});
			}

			// loop items and add to map
			{iteration:dealerItems}
				{option:dealerItems.lat}{option:dealerItems.lng}addMarker({$dealerItems.lat}, {$dealerItems.lng}, '{$dealerItems.name}', $('#markerText' + {$dealerItems.id}).html());{/option:dealerItems.lat}{/option:dealerItems.lng}
			{/iteration:dealerItems}

			// set center to the middle of our boundaries
			map.setCenter(latlngBounds.getCenter());

			// set zoom automatically, defined by points (if allowed)
			if('{$dealerSettings.zoom_level}' == 'auto') map.fitBounds(latlngBounds);
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
					<div style="width:300px;  float:left;">
						{$dealerItems.street} <br>
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
						<strong>Merken</strong> <br>
						<ul>
							<li>appels</li>
							<li>peren</li>
							<li>bananen</li>
							<li>druiven</li>
														<li>appels</li>
							<li>peren</li>
						</ul>
					</div>
				</div>
			</div>
		{/iteration:dealerItems}
	</div>
{/option:dealerItems}
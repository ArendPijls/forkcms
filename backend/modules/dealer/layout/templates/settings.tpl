{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblDealer}</h2>
</div>

{form:settings}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblMapSettings|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="zoomLevel">{$lblZoomLevel|ucfirst}</label>
				{$ddmZoomLevel} {$ddmZoomLevelError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="width">{$lblWidth|ucfirst}</label>
				{$txtWidth} {$txtWidthError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="height">{$lblHeight|ucfirst}</label>
				{$txtHeight} {$txtHeightError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="mapType">{$lblMapType|ucfirst}</label>
				{$ddmMapType} {$ddmMapTypeError}
			</p>
		</div>
	</div>
	
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblSearch|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="units">{$lblUnits|ucfirst}</label>
				{$ddmUnits} {$ddmUnitsError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="distance">{$lblDistance|ucfirst}</label>
				{$txtDistance} {$txtDistanceError}
			</p>
		</div>
		<div class="options">
			<p>
				<label for="distance">{$lblLimit|ucfirst}</label>
				{$txtLimit} {$txtLimitError}
			</p>
		</div>
	</div>
	
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
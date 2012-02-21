{*
	variables that are available:
	- {$dealerItems}: contains data about all dealers
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
	{form:searchForm}
		<div class="alignBlocks">
			<p {option:txtAreaError}class="errorArea"{/option:txtAreaError}>
				<label for="area">{$lblCityOrPostcode|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtArea} {$txtAreaError}
			</p>
		</div>
		<div>
			<p>
				<label for="where">{$lblWhere|ucfirst}</label>
				{$ddmWhere} {$ddmWhereError}
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
			<input class="inputSubmit" type="submit" name="Search" value="{$msgFindDealer|ucfirst}" />
		</p>
	{/form:searchForm}
{/option:dealerItems}
{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblDealer|ucfirst}: {$msgEdit|sprintf:{$item.name}}</h2>
</div>

{form:edit}
	<p>
		<label for="title">{$lblName|ucfirst}</label>
		{$txtName} {$txtNameError}
	</p>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td id="leftColumn">

				<div class="box">
					<div class="heading">
						<h3>{$lblDealer|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
					</div>
					<div class="optionsRTE">
						{$txtDealer} {$txtDealerError}
					</div>
				</div>
				
				<div class="box">
					<div class="heading">
						<h3>{$lblAddress|ucfirst}</h3>
					</div>
					
					<div class="options">
						<p>
							<label for="street">{$lblStreet|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtStreet} {$txtStreetError}
						</p>
						<p>
							<label for="number">{$lblNumber|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtNumber} {$txtNumberError}
						</p>
						<p>
							<label for="zip">{$lblZip|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtZip} {$txtZipError}
						</p>
						<p>
							<label for="city">{$lblCity|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtCity} {$txtCityError}
						</p>
						<p>
							<label for="country">{$lblCountry|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$ddmCountry} {$ddmCountryError}
						</p>
					</div>
				</div>
			</td>

			<td id="sidebar">

				<div id="publishOptions" class="box">
					<div class="heading">
						<h3>{$lblStatus|ucfirst}</h3>
					</div>

					<div class="options">
						<ul class="inputList">
							{iteration:hidden}
								<li>
									{$hidden.rbtHidden}
									<label for="{$hidden.id}">{$hidden.label}</label>
								</li>
							{/iteration:hidden}
						</ul>
					</div>
				</div>
				
				<div class="box">
					<div class="heading">
						<h3>{$lblContact|ucfirst}</h3>
					</div>

					<div class="options">
						<p>
							<label for="tel">{$lblTel|ucfirst}</label>
							{$txtTel} {$txtTelError}
						</p>
						<p>
							<label for="fax">{$lblFax|ucfirst}</label>
							{$txtFax} {$txtFaxError}
						</p>
						<p>
							<label for="email">{$lblEmail|ucfirst}</label>
							{$txtEmail} {$txtEmailError}
						</p>
						<p>
							<label for="site">{$lblSite|ucfirst}</label>
							{$txtSite} {$txtSiteError}
						</p>
					</div>
				</div>
				
				<div class="box">
					<div class="heading">
						<h3>{$lblAvatar|ucfirst}</h3>
					</div>

					<div class="options">
						<p>
							{option:item.avatar}
								<img src="{$FRONTEND_FILES_URL}/frontend_dealer/avatars/128x128/{$item.avatar}" width="128" height="128" alt="" />
							{/option:item.avatar}
						</p>
						<p>
							<label for="avatar">{$lblAvatar|ucfirst}</label>
							{$fileAvatar} {$fileAvatarError}
							<span class="helpTxt">{$msgHelpAvatar}</span>
						</p>
					</div>
				</div>
			</td>
		</tr>
	</table>

	
	<div class="fullwidthOptions">
		{option:showDealerDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.name}}
			</p>
		</div>
		{/option:showDealerDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
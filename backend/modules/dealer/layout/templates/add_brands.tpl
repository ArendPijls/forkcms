{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblBrands|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<p>
		<label for="title">{$lblName|ucfirst}</label>
		{$txtName} {$txtNameError}
	</p>
	
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
	<div class="buttonHolderRight">
		<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblAdd|ucfirst}" />
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
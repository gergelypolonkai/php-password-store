{include file="head.tpl" title="Search" needjquery=1}
		<h2>Search</h2>
		<div id="error"></div>
		<input type="text" id="query" name="query" /><input type="button" id="clearsearch" value="" class="submit" />
		<div id="results"></div>
		<div id="info"></div>
		<div id="passwordgroups">
			<h2>Accessible password groups</h2>
{foreach $passwordgroups as $passwordgroup=>$permissions}
	{if $permissions@first}
			<ul>
	{/if}
				<li>
					<span class="pwgname" id="pwgname_{$passwordgroup}">{$passwordgroup}</span>{if ($permissions == 'rw') || ($isadmin)} [Add password]{/if}
				</li>
	{if $permissions@last}
{if $isadmin}
				<li id="addnewgroup">Create new group</li>
{/if}
			</ul>
	{/if}
{/foreach}
		</div>
		<div id="passwords">
			<h2>Passwords in group <span id="openedgroupname"></span></h2>
			<div id="passwordlist">
			</div>
			<span id="showpasswordgroups" class="button">Back to password groups</span>
		</div>
		<div id="passwordinfo"></div>
{include file="foot.tpl"}

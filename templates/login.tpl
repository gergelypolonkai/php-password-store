{include file="head.tpl" title="Login" login=1}
{if ($loginerror == true)}
		<div class="error">
			Bad username or password. Please try again. Please also note, that this try was logged.
		</div>
{/if}
		<div id="content">
			<form method="post" action="{$self}">
				<input type="hidden" name="redirect" value="{$redirect}" />
				<p>
					<label class="formlabel" for="username">Username:</label><br />
					<input type="text" name="username" id="username" value="{$user}" /><br />
				</p>
				<p>
					<label class="formlabel" for="password">Password:</label><br />
					<input type="password" name="password" id="password" value="" /><br />
				</p>
				<input type="submit" class="submit" value="Login" />
			</form>
		</div>
{include file="foot.tpl"}

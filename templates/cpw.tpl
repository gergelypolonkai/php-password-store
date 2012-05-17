{include file="head.tpl" title="Change Password"}
{if $errno == 1}
		<div class="error">
			The new passwords you typed in does not match.
		</div>
{elseif $errno == 2}
		<div class="error">
			The old password you typed in is incorrect.
		</div>
{elseif $errno == 3}
		<div class="error">
			The new password you typed in is too weak. It must contain small and capital letters, and numbers.
		</div>
{elseif $errno == 4}
		<div class="error">
			No such username. How can this be???
		</div>
{elseif $errno == 5}
		<div class="error">
			You did not provide a new password!
		</div>
{elseif $errno == 254}
		<div class="error">
			Something weird happened on server side.
		</div>
{elseif $errno == 255}
		<div class="error">
			The new password could not be saved due to a database error.
		</div>
{elseif $errno == 0}
		<div class="info">
			Password successfully changed.
		</div>
{/if}
		<form method="post">
			<table>
				<tr>
					<td>Old password:</td>
					<td><input type="password" name="oldpw" /></td>
				</tr>
				<tr>
					<td>New password:</td>
					<td><input type="password" name="newpw1" /></td>
				</tr>
				<tr>
					<td>Retype new password:</td>
					<td><input type="password" name="newpw2" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="Save" /></td>
				</tr>
			</table>
		</form>
{include file="foot.tpl"}



<div id="content">

<div id="form_content">

<h3>Change Password</h3>

<p>


<form id="changePassword" name="changePassword">

<input type="hidden" id="id" value="{$id}">

<table>

<tbody id = "form_body">

	<tr>
		<td>New Password</td><td><input  type="password" id="password" name="password" maxlength="64"></td>
	</tr>

	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Change Password" onclick="return change_password();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
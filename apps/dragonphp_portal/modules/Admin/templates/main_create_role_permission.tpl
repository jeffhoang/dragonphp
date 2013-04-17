

<div id="content">

<div id="form_content">

<h3>Create a New Role Permission</h3>

<form id="createRole" name="createRole" onsubmit='return false;'>

<input type="hidden" id="role_id" value="{$role_id}">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Permission (arbitrary permission handle, i.e. crud, all, special_area, etc...)</td><td><input id="permission" name="permission" maxlength="128"></td>
	</tr>
	
	<tr>
		<td>Permission Type (arbitrary type, i.e. action, url, pattern, etc...)</td><td><input id="permission_type" name="permission_type" maxlength="12"></td>
	</tr>
	
	<tr>
		<td>Pattern (if permission type is pattern, enter the pattern you want to enforce, i.e. /?control_panel)</td><td><input id="pattern" name="pattern" maxlength="255"></td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Create" onclick="return create_role_permission();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
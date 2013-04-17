

<div id="content">

<div id="form_content">

<h3>Update Role Permission</h3>

<form id="createRole" name="createRole" onsubmit='return false;'>

<input type="hidden" id="role_id" value="{$role_id}">
<input type="hidden" id="id" value="{$id}">
<input type="hidden" id="original_permission_handle" value="{$original_permission_handle}">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Permission (arbitrary permission handle, i.e. crud, all, special_area, etc...)</td><td><input id="permission" name="permission" maxlength="128" value="{$permission}"></td>
	</tr>
	
	<tr>
		<td>Permission Type (arbitrary type, i.e. action, url, pattern, etc...)</td><td><input id="permission_type" name="permission_type" maxlength="12" value="{$permission_type}"></td>
	</tr>
	
	<tr>
		<td>Pattern (if permission type is pattern, enter the pattern you want to enforce, i.e. /?control_panel)</td><td><input id="pattern" name="pattern" maxlength="255" value="{$pattern}"></td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Update" onclick="return update_role_permission();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
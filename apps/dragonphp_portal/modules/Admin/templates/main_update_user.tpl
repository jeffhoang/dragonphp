<div class="overlay" id="apple"> 

<div id="content">

<div id="form_content">

<h3>Update User</h3>

<form id="updateUser" name="updateUser">

<input type="hidden" id="id" value="{$user->id}">
<input type="hidden" id="original_email" value="{$user->email}">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>First Name</td><td><input id="first_name" name="first_name" value="{$user->first_name}" maxlength="128"></td>
	</tr>
	<tr>
		<td>Last Name</td><td><input id="last_name" name="last_name" value="{$user->last_name}" maxlength="128"></td>
	</tr>
	<tr>
		<td>Email</td><td><input id="email" name="email" value="{$user->email}" maxlength="256"></td>
	</tr>
	<tr>
		<td>Organization</td><td>{html_options name=organization_id id=organization_id options=$list_of_organizations selected=$selected_organization}</td>
	</tr>
	<tr>
		<td>Role</td><td>{html_options name=role_id id=role_id options=$list_of_roles selected=$selected_role}</td>
	</tr>
	<tr>
		<td>Is Active?</td><td>{html_options name=is_active id=is_active options=$activestatus selected=$selected_status}</td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Update" onclick="return update_user_info();"></td>
	</tr>


</tbody>

</table>

</form>

</div>

</div>

</div>
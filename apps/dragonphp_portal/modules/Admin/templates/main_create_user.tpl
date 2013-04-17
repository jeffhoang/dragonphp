

<div id="content">

<div id="form_content">

<h3>Create User</h3>


<form id="createUser" name="createUser">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>First Name</td><td><input id="first_name" name="first_name" maxlength="128"></td>
	</tr>
	<tr>
		<td>Last Name</td><td><input id="last_name" name="last_name" maxlength="128"></td>
	</tr>
	<tr>
		<td>Email</td><td><input id="email" name="email" maxlength="256"></td>
	</tr>
	<tr>
		<td>Password</td><td><input type="password" id="password" name="password" maxlength="64"></td>
	</tr>
	<tr>
		<td>Organization</td><td>{html_options name=organization_id id=organization_id options=$list_of_organizations}</td>
	</tr>
	
	<tr>
		<td>Role</td><td>{html_options name=role_id id=role_id options=$list_of_roles}</td>
	</tr>
	
	<tr>
		<td>Is Active?</td><td>{html_options name=is_active id=is_active options=$activestatus}</td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Create" onclick="return create_user();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
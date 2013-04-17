

<div id="content">

<div id="form_content">

<h3>Create a New Role</h3>

<form id="createRole" name="createRole" onsubmit='return false;'>

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Role Name</td><td><input id="name" name="name" maxlength="64"></td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Create" onclick="return create_role();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
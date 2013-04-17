

<div id="content">

<div id="form_content">

<h3>Create Organization</h3>


<form id="createOrganization" name="createOrganization" onsubmit="return false;">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Name</td><td><input id="name" name="name" maxlength="128"></td>
	</tr>
	<tr>
		<td>City</td><td><input id="city" name="city" maxlength="128"></td>
	</tr>
	<tr>
		<td>State</td><td><input id="state" name="state" maxlength="128"></td>
	</tr>
	<tr>
		<td>Zipcode</td><td><input id="zipcode" name="zipcode" maxlength="12"></td>
	</tr>
	
	<tr>
		<td>Province</td><td><input id="province" name="province" maxlength="128"></td>
	</tr>
	
	<tr>
		<td>Country</td><td><input id="country" name="country" maxlength="128"></td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Create" onclick="return create_organization();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>


<div id="content">

<div id="form_content">

<h3>Update Organization</h3>


<form id="updateOrganization" name="updateOrganization" onsubmit="return false;">
<input type="hidden" id="id" name="id" value="{$id}">
<input type="hidden" id="original_org_name" name="original_org_name" value="{$original_org_name}">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Name</td><td><input id="name" name="name" maxlength="128" value="{$name}"></td>
	</tr>
	<tr>
		<td>City</td><td><input id="city" name="city" maxlength="128" value="{$city}"></td>
	</tr>
	<tr>
		<td>State</td><td><input id="state" name="state" maxlength="128" value="{$state}"></td>
	</tr>
	<tr>
		<td>Zipcode</td><td><input id="zipcode" name="zipcode" maxlength="12" value="{$zipcode}"></td>
	</tr>
	
	<tr>
		<td>Province</td><td><input id="province" name="province" maxlength="128" value="{$province}"></td>
	</tr>
	
	<tr>
		<td>Country</td><td><input id="country" name="country" maxlength="128" value="{$country}"></td>
	</tr>
	
	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Update" onclick="return update_organization();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
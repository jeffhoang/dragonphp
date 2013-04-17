{literal}
<script>

$('form').submit(function(){
    // On submit disable its submit button
    $('input[type=submit]', this).attr('disabled', 'disabled');
});

</script>

{/literal}

<div id="content">

<div id="form_content">

<h3>Update Role</h3>

<form id="updateRole" name="updateRole" onsubmit="return false;">

<input type="hidden" id="id" value="{$id}">

<table>

<thead>
	<th>Field Name</th>
	<th>Value</th>
</thead>

<tbody id = "form_body">

	<tr>
		<td>Name</td><td><input id="name" name="name" value="{$name}" maxlength="64"></td>
	</tr>

	<tr>
	<td colspan="2" align="middle"><input type="button" id="submit" value="Update" onclick="return update_role();"></td>
	</tr>


</tbody>

</table>

</form>

</div>
</div>
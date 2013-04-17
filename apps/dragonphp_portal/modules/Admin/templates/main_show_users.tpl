<div id="content">

<h3>Show Users</h3>

<p><a class="iFrameRegister" href="/?create_user">+ Create a New User</a></p>

{literal}
<script>

$(document).ready(function() {

	$("a.iFrameRegister").fancybox({
		'hideOnOverlayClick': false,
		'enableEscapeButton': true,
		'overlayColor': '#bbbbbb',
		'overlayOpacity': .6,
		'cyclic': false,
		'showNavArrows': false,
		'disableNavigation': true
    });
    
    $.fancybox.hideActivity;
});

</script>

{/literal}

<table>

<thead>
	<th>ID</th>
	<th>Name</th>
	<th>Email</th>
	<th>Created Date</th>
	<th>Organization</th>
	<th>Is Active</th>
	<th>Commands</th>
</thead>

<tbody>
{foreach from=$list_of_users key=id item=user}

<tr>

	<td>{$id}</td>
	<td class="fn_{$id}" id="fn_{$id}">{$user->first_name} {$user->last_name}</td>
	<td class="ln_{$id}" id="ln_{$id}">{$user->email}</td>
	<td>{$user->created_date}</td>
	<td class="on_{$id}" id="on_{$id}">{$user->org_name}</td>
	<td class="o=ia_{$id}" id="ia_{$id}">{if $user->is_active == 1}true{else if $user->is_active == 0}false{/if}</td>
	<td><a class="iFrameRegister" href="/?update_user&id={$id}">update</a> | <a class="iFrameRegister" href="/?changepassword&id={$id}">change password</a>{if strcmp($id, $current_id) != 0} | <a class="iFrameRegister" href="/?remove_user&id={$id}">remove</a>{/if}</td>
</tr>
{/foreach}

</tbody>

</table>

</div>
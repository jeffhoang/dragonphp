<div id="content">

<h3><a href="/?manage_roles">Roles</a> >> Permissions for Role "{$name}"</h3>

<p><a class="iFrameRegister" href="/?create_role_permission&role_id={$role_id}">+ Create a New Role Permission</a></p>

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
		'autoDimensions': false,
		'disableNavigation': true,
		'width': 550,
		'height': 300
    });
    
    $("a.iFrameRegister2").fancybox({
		'hideOnOverlayClick': false,
		'enableEscapeButton': true,
		'overlayColor': '#bbbbbb',
		'overlayOpacity': .6,
		'cyclic': false,
		'showNavArrows': false,
		'autoDimensions': true,
		'disableNavigation': true,
    });
    
	$('#updateRolePermission').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$('#createRolePermission').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$.fancybox.hideActivity;
});

</script>

{/literal}

<table>

<thead>
	<th>ID</th>
	<th>Permission</th>
	<th>Commands</th>
</thead>

<tbody>
{foreach from=$list_of_role_permissions key=id item=role_permission}

<tr>

	<td>{$role_permission->id}</td>
	<td class="fn_{$role_permission->id}" id="fn_{$role_permission->id}">permission: {$role_permission->permission}<br>
	type: {$role_permission->permission_type}<br>
	pattern: {$role_permission->pattern}
	</td>
	<td><a class="iFrameRegister" href="/?update_role_permission&id={$role_permission->id}&role_id={$role_id}">update</a> | <a class="iFrameRegister2" href="/?remove_role_permission&id={$role_permission->id}">remove</a></td>
</tr>
{/foreach}

</tbody>

</table>  

</div>
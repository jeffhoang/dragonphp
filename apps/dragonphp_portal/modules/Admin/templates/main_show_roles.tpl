<div id="content">

<h3>Show Roles</h3>

<p><a class="iFrameRegister" href="/?create_role">+ Create a New Role</a></p>

{literal}
<script>

$(document).ready(function() {

	$("a.iFrameRegister").fancybox({
		'hideOnOverlayClick': false,
		'enableEscapeButton': true,
		'overlayColor': '#bbbbbb',
		'overlayOpacity': .6,
		'cyclic': false,
		'autoDimensions': true,
		'showNavArrows': false,
		'disableNavigation': true
    });

	    
	$('#updateRole').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$('#createRole').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$.fancybox.hideActivity;
});

</script>

{/literal}

<table>

<thead>
	<th>ID</th>
	<th>Role</th>
	<th>Commands</th>
</thead>

<tbody>
{foreach from=$list_of_roles key=id item=role}

<tr>

	<td>{$role->id}</td>
	<td class="fn_{$role->id}" id="fn_{$role->id}">{$role->name}</td>
	<td><a class="iFrameRegister" href="/?update_role&id={$role->id}">update</a> | <a href="/?manage_role_permissions&id={$role->id}">role permissions</a> | <a class="iFrameRegister" href="/?remove_role&id={$role->id}">remove</a></td>
</tr>
{/foreach}

</tbody>

</table>  

</div>
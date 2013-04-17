<div id="content">

<h3>Show Organizations</h3>

<p><a class="iFrameRegister" href="/?create_organization">+ Create a New Organization</a></p>

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

	    
	$('#updateOrg').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$('#createOrg').submit(function(){
	    $('input[type=submit]', this).attr('disabled', 'disabled');
	});
	
	$.fancybox.hideActivity;
});

</script>

{/literal}

<table>

<thead>
	<th>ID</th>
	<th>Organization Info</th>
	<th>Commands</th>
</thead>

<tbody>
{foreach from=$list_of_organizations key=id item=organization}

<tr>

	<td>{$organization->id}</td>
	<td class="fn_{$organization->id}" id="fn_{$organization->id}">Name: {$organization->name}<br>
	City: {$organization->city}<br>
	State: {$organization->state}<br>
	Zipcode: {$organization->zipcode}<br>
	Province: {$organization->province}<br>
	Country: {$organization->country}
	</td>
	<td><a class="iFrameRegister" href="/?update_organization&id={$organization->id}">update</a> | <a class="iFrameRegister" href="/?remove_organization&id={$organization->id}">remove</a></td>
</tr>
{/foreach}

</tbody>

</table>  

</div>
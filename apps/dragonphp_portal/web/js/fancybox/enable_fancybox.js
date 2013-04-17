<script>
$(document).ready(function() {

	/* This is basic - uses default settings */
	
	$("a#fancy_action").fancybox();
	
	/* Using custom settings */
	
	$("a#inline").fancybox({
		'hideOnContentClick': true
	});

	$("a.group").fancybox({
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});
});

</script>
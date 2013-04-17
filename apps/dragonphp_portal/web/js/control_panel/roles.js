
function update_role(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	var name = $("#name").val();
	
	if(id == "") {
		errorMsg = "Id is missing.\n";
		hasError = true;
	}
	
	if(name == "") {
		errorMsg += "Name is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'id=' + id + '&name=' + name + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?update_role",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Role has been updated.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			return false;
	   		}
	     }  
	});
} 

function create_role(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var name = $("#name").val();
	
	if(name == "") {
		errorMsg = "Name is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'name=' + name + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?create_role",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">New role was successfully created.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			return false;
	   		}
	     }  
	});
} 

function remove_role(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	
	if(id == "") {
		errorMsg = "ID is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'id=' + id + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?remove_role",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Role has been removed.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			$.fancybox.close();
	   		}
	     }  
	});
} 

function create_role_permission(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var roleId = $("#role_id").val();
	var permission = $("#permission").val();
	var permissionType = $("#permission_type").val();
	var pattern = $("#pattern").val();
	
	if(roleId == "") {
		errorMsg = "Role ID is required.\n";
		hasError = true;
	}
	
	if(permission == "") {
		errorMsg += "Permission is required.\n";
		hasError = true;
	}
	
	if(permissionType == "") {
		errorMsg += "Permission type is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'permission=' + permission + '&permission_type=' + permissionType + '&role_id=' + roleId + '&pattern=' + pattern + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?create_role_permission",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">New role permission was successfully created.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			return false;
	   		}
	     }  
	});
}


function remove_role_permission(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	
	if(id == "") {
		errorMsg = "ID is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'id=' + id + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?remove_role_permission",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Role permission has been removed.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			$.fancybox.close();
	   		}
	     }  
	});
} 

function update_role_permission(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	var roleId = $("#role_id").val();
	var permission = $("#permission").val();
	var permissionType = $("#permission_type").val();
	var pattern = $("#pattern").val();
	var originalPermissionHandle = $("#original_permission_handle").val();
	
	if(id == "") {
		errorMsg = "Id is required.\n";
		hasError = true;
	}
	
	if(roleId == "") {
		errorMsg += "Role ID is required.\n";
		hasError = true;
	}
	
	if(permission == "") {
		errorMsg += "Permission is required.\n";
		hasError = true;
	}
	
	if(permissionType == "") {
		errorMsg += "Permission type is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'original_permission_handle=' + originalPermissionHandle + '&id=' + id + '&permission=' + permission + '&permission_type=' + permissionType + '&role_id=' + roleId + '&pattern=' + pattern + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?update_role_permission",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">New role permission was successfully created.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   			window.location.reload();
	   			
	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			return false;
	   		}
	     }  
	});
}
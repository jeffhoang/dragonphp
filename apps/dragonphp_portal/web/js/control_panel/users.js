function update_user_info(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	var email = $("#email").val();
	var originalEmail = $("#original_email").val();
	var firstName = $("#first_name").val();
	var lastName = $("#last_name").val();
	var organizationId = $("#organization_id").val();
	var isactive = $("#is_active").val();
	var roleId= $("#role_id").val();
	
	if(email == "") {
		errorMsg = "Email address is required\n";
		hasError = true;
	}
	
	if(organizationId == "0"){
		errorMsg += "* Please Select an Organization\n";
		hasError = true;
	}
	
	if(roleId == "0"){
		errorMsg += "* Please Select a Role\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		
		return false;
	}
		
	var dataString = 'role_id=' + roleId + '&id=' + id + '&email=' + email + '&firstname=' + firstName + '&lastname=' + lastName + '&organizationid=' + organizationId + '&submit=true' + '&original_email=' + originalEmail + '&is_active=' + isactive;
	
	$.ajax({  
		type: "POST",  
	   	url: "/?update_user",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">User information successfully updated.</td></tr></tbody>');
	   			
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

function create_user(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var email = $("#email").val();
	var firstName = $("#first_name").val();
	var lastName = $("#last_name").val();
	var organizationId = $("#organization_id").val();
	var isactive = $("#is_active").val();
	var password = $("#password").val();
	var roleId= $("#role_id").val();
	
	if(email == "") {
		errorMsg = "* Email address is required\n";
		hasError = true;
	}
	
	if(organizationId == "0"){
		errorMsg += "* Please Select an Organization\n";
		hasError = true;
	}
	
	if(password == ""){
		errorMsg += "* Password is required\n";
		hasError = true;
	}
	
	if(roleId == "0"){
		errorMsg += "* Please Select a Role\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		return false;
	}
		
	var dataString = 'role_id=' + roleId + '&password=' + password + '&email=' + email + '&firstname=' + firstName + '&lastname=' + lastName + '&organizationid=' + organizationId + '&submit=true' + '&is_active=' + isactive;
	
	$.ajax({  
		type: "POST",  
	   	url: "/?create_user",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">User successfully created.</td></tr></tbody>');
	   			
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

function change_password(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';
	
	var id = $("#id").val();
	var password = $("#password").val();
	
	if(password == "") {
		errorMsg = "Please enter a password.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		
		return false;
	}
		
	var dataString = 'id=' + id + '&password=' + password + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?changepassword",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Password successfully changed.</td></tr></tbody>');
	   			
	   			$.fancybox.close();

	   		}else if(responseCode < 0){
	   			message = responseObject.message;
	   			
	   			alert(message);
	   			
	   			$('.content').attr('enabled', 'enabled');
	
	   			return false;
	   		}
	     }  
	});
}

function remove_user(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	
	if(id == "") {
		errorMsg = "Id is missing.\n";
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
	   	url: "/?remove_user",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">User has been removed.</td></tr></tbody>');
	   			
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

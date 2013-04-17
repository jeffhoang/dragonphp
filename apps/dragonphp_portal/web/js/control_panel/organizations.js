
function update_organization(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var id = $("#id").val();
	var name = $("#name").val();
	var city = $("#city").val();
	var state = $("#state").val();
	var zipcode = $("#zipcode").val();
	var province = $("#province").val();
	var country = $("#country").val();
	var originalOrgName = $("#original_org_name").val();
	
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
		
	var dataString = 'original_org_name=' + originalOrgName + '&city=' + city + '&state=' + state + '&zipcode=' + zipcode + '&province=' + province + '&country=' + country + '&id=' + id + '&name=' + name + '&submit=true';
	
	$.ajax({  
		type: "POST",  
	   	url: "/?update_organization",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Organization has been updated.</td></tr></tbody>');
	   			
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

function create_organization(){
	
	$('.content').attr('disabled', 'disabled');
	   			
	var hasError = false;
	var errorMsg = '';

	var name = $("#name").val();
	var city = $("#city").val();
	var state = $("#state").val();
	var zipcode = $("#zipcode").val();
	var province = $("#province").val();
	var country = $("#country").val();
	
	if(name == "") {
		errorMsg = "Name is required.\n";
		hasError = true;
	}
	
	if(hasError == true){
		alert(errorMsg);
		$('.content').attr('enabled', 'enabled');
		
		return false;
	}
		
	var dataString = 'city=' + city + '&state=' + state + '&zipcode=' + zipcode + '&province=' + province + '&country=' + country + '&name=' + name + '&submit=true';

	$.ajax({  
		type: "POST",  
	   	url: "/?create_organization",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">New organization was successfully created.</td></tr></tbody>');
	   			
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

function remove_organization(){
	
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
	   	url: "/?remove_organization",  
	   	data: dataString, 
	   	dataType: "json",
	   	success: function(msg) {
	   		
	   		var responseObject = eval(msg);

	   		responseCode = responseObject.response_code;
	   			
	   		if(responseCode == 1){
	   		
	   			$("#form_body").replaceWith('<tbody id="form_body"l><tr><td colspan="2">Organization has been removed.</td></tr></tbody>');
	   			
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
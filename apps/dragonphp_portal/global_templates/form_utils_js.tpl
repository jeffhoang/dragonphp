{literal}
<script type="text/javascript">
        function placeFocus(formNumber) {

        	if (document.forms.length > 0) {
                        
            	var field = document.forms[formNumber];
                        
                for (i = 0; i < field.length; i++) {
                                
                	if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea") 
|| (field.elements[i].type.toString().charAt(0) == "s")) {
                		document.forms[formNumber].elements[i].focus();
                    	break;
                	}
				}
			}
        }

</script>

{/literal}
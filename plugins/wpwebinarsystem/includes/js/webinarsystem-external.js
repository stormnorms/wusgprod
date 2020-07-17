	
	   jQuery(document).on('change', 'select[name="inputday"]', function () {
	
	    jQuery('#inputtime').empty();
	    jQuery('#inputtime').append(jQuery('<option></option>').html(wpexternal.available_timeslots));
	    	
		
		var input_day = jQuery(this).val();
		if (input_day == 'rightnow') {
		    jQuery('select[name="inputtime"]').slideUp();
		} 
		else {
		jQuery('select[name="inputtime"]').slideDown();
		var wbnid = jQuery("input[name=wbnid]").val();
		
		jQuery.ajax({
			'type': 'GET',
			'url' : wpexternal.ajaxurl,
			data: {
				action: 'getInputTimes',
				input_day: input_day,
				wbnid: wbnid
			},
			success: function(result){
				jQuery('#inputtime').empty();
				options = '';
				 var data = JSON.parse(result);
				 for(var i in data)
				 {
				if(data[i].value == 'no'){
                 options += '<option value="" disabled="disabled" selected="selected">' + data[i].label +'</option>';
				 break;
                } else if(data[i].value == 'default'){
					options += '<option disabled="disabled" selected="selected" value="'+ data[i].value +'">'+ data[i].label +'</option>';
				}
                 else{
				 options += '<option value="'+ data[i].value +'">'+ data[i].label +'</option>';
				 }}
				  jQuery('#inputtime').html(options);
			},
			error: function(exception){
				//alert(exception);
			}
			
		});
			
		}
		
    });
	       
    jQuery(document).on('keyup','.custom-reg-field[type="tel"]',function (){
        this.value = this.value.replace(/[^0-9\.]/g,'');;
    });
         
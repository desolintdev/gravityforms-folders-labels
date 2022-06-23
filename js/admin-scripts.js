
jQuery(document).ready(function($){

	//	jQuery('#new-folder-name').hide();
		jQuery('input[name="gf_custom_label"]').on('click', function(){
			if ( jQuery(this).prop('checked') ) {
				jQuery('#new-folder-name').show();
				jQuery('#gffolder-list').hide();
			} 
			else {
				jQuery('#new-folder-name').hide();
				jQuery('#gffolder-list').show();

			}
		});
		clicked = true;
		$("div[id^='folder-id']").click(function() {
		    $('.column-gflabel .folder').css('color','');
		    $('.column-gflabel .folder').css({"color":"", "font-weight":"normal"});
		   	$(this).css({"color":"red", "font-weight":"bold"});
			var genre = $(this).text(); 
			$.ajax({
				url: getgformsbyid.getgformsbyidajaxurl,
				data: { action: "flgf_getformsbyid", genre: genre },
				type: 'post',
				success: function(response) {
					response = response.slice(0, - 1);
					$("#special-content").html(response);
				}
			});
		});	


		$("div[id^='label-id']").click(function() {
		    $('.column-gflabel .folder').css('color','');
		    $('.column-gflabel .folder').css({"color":"", "font-weight":"normal"});
		   	$(this).css({"color":"red", "font-weight":"bold"});
			var label = $(this).text(); 
			$.ajax({
				url: getgformsbyid.getgformsbyidajaxurl,
				data: { action: "flgf_getformsbylabelid", label: label },
				type: 'post',
				success: function(response) {
					response = response.slice(0, - 1);
					$("#special-content").html(response);
				}
			});
		});	
	
	
	jQuery( "<div id='special-content'></div>" ).insertAfter( ".gflabels" );	

	jQuery( "<tr><th scope='row' class='check-column'></th><td class='gflabel column-gflabel has-row-actions column-primary'><div class='all-forms folder' id='allgforms'>All Forms</div><button type='button' class='toggle-row'><span class='screen-reader-text'>Show more details</span></button></td></tr>" ).insertBefore( ".gflabels tbody tr:first-child" );	

		$("#allgforms").click(function() {
		    $('.column-gflabel .folder').css('color','');
		    $('.column-gflabel .folder').css({"color":"", "font-weight":"normal"});
		   	$(this).css({"color":"red", "font-weight":"bold"});
		   	
			$.ajax({
				url: getAllgforms.getAllgformsajaxurl,
				data: {action: "flgf_gfformslists"},
				type: 'post',
			success: function(response) {
			    response = response.slice(0, - 1);
				$("#special-content").html(response);
			}
			});	
    	});	
	

    $(".delete-folder").click(function() {
        var folder_id = $(this).data("folderid");
        var $ele = $(this).closest("tr");
        $.ajax({
            type: 'post',
            url: gfDel.gfdelajaxurl,
            data: { action: "flgf_delgffolder", folder_id: folder_id },
        success: function(response) {
			response = response.slice(0, - 1);
             console.log(response);
            if(response) {
                $ele.fadeOut().remove();
            }
         }
            
        });
    });	

	
	
    $(".delete-label").click(function() {
        var label_id = $(this).data("labelid");
        var $ele = $(this).closest("tr");
        $.ajax({
            type: 'post',
            url: gfDel.gfdelajaxurl,
            data: { action: "flgf_delgflabel", label_id: label_id },
        success: function(response) {
			response = response.slice(0, - 1);
             console.log(response);
            if(response) {
                $ele.fadeOut().remove();
            }
            
         }
            
        });
    });	
	
    
    $(".edit-folder").click(function() {	
        var folder_id = $(this).data("folderid");
		var parentfolder = '#folder-id-'+ folder_id;
		$(parentfolder).hide();
 		$(parentfolder).next('.folder-edit-div').show();
		$(this).next('.update-folder-btn').show();
		$(this).hide();

	});

	
    $(".edit-label").click(function() {	
        var label_id = $(this).data("labelid");
		var parentfolder = '#label-id-'+ label_id;
		$(parentfolder).hide();
 		$(parentfolder).next('.label-edit-div').show();
		$(this).next('.update-label-btn').show();
		$(this).hide();

	});
	

    $(".update-folder-btn").click(function() {
		
        var folder_id = $(this).data("folderid");
		var parentfolder = '#folder-id-'+ folder_id;
		var inputID = '#'+folder_id;
        var newVal = $('body').find(inputID).val();
        $(parentfolder).text(newVal);
		$(parentfolder).show();
 		$(parentfolder).next('.folder-edit-div').hide();
 		$(this).prev('.edit-folder').show();
		$(this).hide();

        $.ajax({
            type: 'post',
            url:  updategffolder.gfupdateajaxurl,
            data: { action: "flgf_updgffolder", folder_id: folder_id, folder_name: newVal },
        success: function(response) {
            if(response == "Done0") {
            }
            else {
               console.log(JSON.stringify(error));
            }
         }
            
        });
    });	    
	

	
    $(".update-label-btn").click(function() {
		
        var label_id = $(this).data("labelid");
		var parentfolder = '#label-id-'+ label_id;
		var inputID = '#'+label_id;
        var newVal = $('body').find(inputID).val();
        $(parentfolder).text(newVal);
		$(parentfolder).show();
 		$(parentfolder).next('.label-edit-div').hide();
 		$(this).prev('.edit-label').show();
		$(this).hide();

        $.ajax({
            type: 'post',
            url:  updategffolder.gfupdateajaxurl,
            data: { action: "flgf_updgflabel", label_id: label_id, label_name: newVal },
        success: function(response) {
			console.log(response);
            if(response == "Done0") {
            }
            else {
               console.log(JSON.stringify(error));
            }
         }
            
        });
    });	  	
	
	
 
        /*$('.all-forms').css({"color":"red", "font-weight":"bold"});
         $.ajax({
            url: getAllgforms.getAllgformsajaxurl,
            data: { action: "flgf_gfformslists"},
            type: 'post',
        success: function(response) {
            response = response.slice(0, - 1);
            $("#special-content").html(response);
         }
        });*/
        

});	



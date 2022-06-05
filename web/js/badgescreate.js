
$(document).ready(function(){


    $(document).on('change', '#badges-file-input', function(e){
        

        $("#badges-import").find("input[type=file]").each(function(index, field){
            $.ajax({
                type: "POST",
                url: ['index.php?r=badges%2Fcreate-new-badges'],
                data: {
                    files_length: field.files.length,
                    },
                success: function (response) {
                    $(".badges-modal-body-table").html(response);
                    for(var i=0;i<field.files.length;i++) {
                        const file = field.files[i];
                        $("#badges-name-" + i).val(file['name']);
                        $("#newBadge-type-" + i).text(file['type']);
                        $("#newBadge-size-" + i).text(file['size']);
                        $("#newBadge-preview-" + i).prop("src", URL.createObjectURL(file));
                    }
                },
                error: function (error) {
                    console.log(error)
                }
            })
            
        });
 
        

    });

    $(document).on('click', '[id^="badges-actions-"]', function(e){

    	var badgeName = '';
    	var badgeRateCondition = '';
    	var badgeAllowUsers = '';
    	var badgeId = $(this).attr('id').replace('badges-actions-', '');
    	var surveyId = $("#surveyId").val();

    	if( $(this).hasClass('edit-badge') ){
    		$(this).removeClass('fa-pencil edit-badge').addClass('fa-check save-badge');
    		$(this).css("color", "#77dd77");
    		$(".edit-badge-name-" + badgeId).toggle();
    		$(".edit-badge-ratecondition-" + badgeId).toggle();
    		$(".edit-badge-allowusers-" + badgeId).toggle();
    		return;
    	}else if ( $(this).hasClass('delete-badge') ){
    		var action = "delete";
    		
    	}else if ( $(this).hasClass('save-badge') ){
    		var action = "modify";
    		badgeName = $("#badge-name-" + badgeId).val();
			badgeRateCondition = $("#badge-ratecondition-" + badgeId).val();
			// badgeAllowUsers = $("#badge-allowusers-" + badgeId).prop("checked");
            badgeAllowUsers = $("#badge-allowusers-" + badgeId).val();
			// alert(badgeAllowUsers);
    	}

    	
    	var element = $(this);
    	$.ajax({
            type: "POST",
            url: ['index.php?r=badges%2Fbadge-edit'],
            data: {
                action: action,
                badgeId: badgeId,
                surveyId: surveyId,
                badgeName: badgeName,
				badgeRateCondition: badgeRateCondition,
				badgeAllowUsers: badgeAllowUsers
                },
            success: function (response) {
                
                if( response.action == 'modify' ){
                	$(".badge-name-" + badgeId + "-text").text(badgeName);
    				$(".badge-ratecondition-" + badgeId + "-text").text(badgeRateCondition);
    				if(response.badgeAllowUsers){
    					$("#badge-allowusers-" + badgeId).text('Yes');
    				}else{
    					$("#badge-allowusers-" + badgeId).text('No');
    				}
    				$(".edit-badge-name-" + badgeId).toggle();
    				$(".edit-badge-ratecondition-" + badgeId).toggle();
    				$(".edit-badge-allowusers-" + badgeId).toggle();
                	element.removeClass('fa-check save-badge').addClass('fa-pencil edit-badge');
                	element.css("color", "#949494");
                }else if( response.action == 'delete' ){
                	alert("deleted");
                	element.parent().parent().remove();
                }
            },
            error: function (error) {
                console.log(error)
            }
        })
    });

});
$(document).ready(function(){

	$("body").removeClass("h-100");
	var mode = $("#survey-message").val();

	function createUserTable(){

		$(".survey-fields").each(function( ) {
		  $(this).css("display", "none");
		});
		$(".table-row").css("display", "");
		$(".submit-button").attr('name', 'finalize');
		var users = jQuery.parseJSON( $("#users_array").val() );

		var participants = '<table class="table table-striped table-bordered participants-table"> <thead class="control-label" for="surveys-starts" style = "text-decoration: none;"><tr><th colspan = "3" style = "width:80%;">Participants</th><th  style = "text-align: center;" ><a class="fas fa-angle-down" style = "cursor: pointer; text-decoration: none;"></a></tr></thead><tbody class = "participants-body">';
		var non_participants =  participants.replace("Participants", "Already in SurvAnnT").replace("participants", "non-participants").replace("participants-body", "non-participants-body");
		participants = '<div class = "col-md-6" style ="position: relative; max-height: 635px; overflow: auto;">' + participants;
		var view_icon = '<a href="/SurvAnnT/web/index.php?r=user-management%2Fuser%2Fview&id=<id>" target = "_blank" class="fas fa-eye" title = "View user" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>';
		var invite_icon = '<a id = "add-user-<id>" class="fas fa-user-check add-user" title = "Invite!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>';
		var delete_icon = '<a id = "delete-user-<id>" class="fas fa-user-slash delete-user" title = "Revoke participation!" style = "cursor: pointer; text-decoration: none; color:#949494;"></a>';
		var pending_icon = '<a class="fas fa-hourglass-start" title ="Pending registration" style = "cursor: pointer; text-decoration: none; color:orange;"></a>&nbsp;&nbsp;';
		for (var i = users.length - 1; i >= 0; i--) {

			row = '<tr><td style = "width: 20%;">' + users[i].name+ '</td><td style = "width: 20%;">' + users[i].surname + '</td><td style = "width: 40%;">' + users[i].email+ '</td>';
			
			
			if ( users[i].participates == 1 ){
				
				row += '<td style = "text-align: center;">'+ view_icon.replace('<id>', users[i].id) + '&nbsp;&nbsp;';
				if ( users[i].request == '0' ){
					var invite_icon = '<a id = "add-user-<id>" class="fas fa-user-check add-user" title = "Allow user to participate!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>';
					row += invite_icon.replace('<id>', users[i].id) + '&nbsp;&nbsp;';
				}
				if ( users[i].owner != 1 ){
					row += delete_icon.replace('<id>', users[i].id) +'</td></tr>';
				}else{
					row += '<i class="fa-solid fa-crown" title = "Owner"></i> </td></tr>';
				}
				
				participants += row;
			}else if( users[i].participates == 0 ){
				row += '<td style = "text-align: center;">'+ view_icon.replace('<id>', users[i].id) + '&nbsp;&nbsp;';
				row += invite_icon.replace('<id>', users[i].id) +'</td></tr>';
				non_participants += row;
			}else{
				row += '<td style = "text-align: center;">' + pending_icon;
				row += delete_icon + '</td></tr>';
				participants += row;
			}
			
		}
		participants += '</tbody></table></div>';
		non_participants += '</tbody></table>';
		if($('.non-participants-table').length == 0) {
			$(".table-row-2 > .col-md-6:first").prepend(non_participants);
		}
		if($('.participants-table').length == 0) {
			$(".table-row").prepend(participants);
		}

	}

	$('body').on('click', 'a#edit-survey', function() {
		$(".survey-fields").each(function( ) {
		  $(this).css("display", "block");
		});
		
		$(".table-row").css("display", "none");
		$(".submit-button").attr('name', 'non-finalize');
	});

	$('body').on('click', 'a#user-invitation', function() {
		
		createUserTable();
	});

	if ( $("#step").val() == 2 ) {
		
		createUserTable();
	}

	if ( $("#action").val() == 'generate-participants' ) {
		
		createUserTable();
	}

	$('body').on('click', 'a.delete-user', function() {
		var userid = $(this).attr("id");
		var email = $(this).parent().prev().text();
		var surveyid = $("#surveyid").val();
		var to_remove = $(this).parent().parent();

		if ( userid == 'delete-user-<id>' ){
			userid = -1;
		}else{
			userid = userid.replace('delete-user-', '');
			var to_replace = '</table>';
			var calling_td = $(this).parent().html();
			var view_icon = '<a href="/SurvAnnT/web/index.php?r=user-management%2Fuser%2Fview&id=' + userid + '" target = "_blank" class="fas fa-eye" title = "View user" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>&nbsp;&nbsp;';
			var invite_icon = '<a id = "add-user-' + userid + '" class="fas fa-user-check add-user" title = "Invite!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>';
			var row = $(this).parent().parent().html().replace( calling_td, view_icon + invite_icon );
			var participants = $(".participants-table").html().replace(to_replace, "");
			
		}

		$.ajax({
            url: ['index.php?r=site%2Fsurvey-participants'],
            type: 'POST',
            async : true,
            cache: false,
            data: {
                _csrf: yii.getCsrfToken(),
                'action': 'delete',
                'surveyid': surveyid,
                'userid': userid,
                'email': email
            },
            success  : function(response, status) {
            	to_remove.remove();
            	if ( userid != -1 ){
            		$(".non-participants-table").append("<tr>" + row + "</tr></table>");
					
					// defined in veto.js
					removeFromExpertSet(userid);
            	}
            },
            error : function(){
            	console.log("error");
            }
        });
	    
	});

	
	$('body').on('click', 'a#add-invitations', function() {

		var input = '<input type="email" name="new-user-email" class ="form-control">';
		var inv_icon = '<a id = "invite-new-user" class="fas fa-envelope add-user" title = "Invite!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>';
		$(".invite-body > tr:last").before("<tr><td>" + input + "</td><td>" + inv_icon + "</td></tr>");
	});
	$('body').on('click', 'a.add-user', function() {
		var userid = $(this).attr("id");
		var surveyid = $("#surveyid").val();
		if ( userid == 'invite-new-user' ){

			var email = $(this).parent().prev().find("input").val();
			var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i

			if(!pattern.test(email))
			{
			  	$(this).parent().prev().append("<div class = 'help-block'> Email is not valid!</div>");
			  	return;

			}else{
				if ( $(this).parent().prev().find('.help-block') ){
					$(this).parent().prev().find('.help-block').remove();
				}
				var pending_icon = '<a class="fas fa-hourglass-start" title ="Pending registration" style = "cursor: pointer; text-decoration: none; color:orange;"></a>&nbsp;&nbsp;';
				var delete_icon = '<a id = "delete-user-<id>" class="fas fa-user-slash delete-user" title = "Revoke participation!" style = "cursor: pointer; text-decoration: none; color:#949494;"></a>';
				var row = '<td>-</td><td>-</td><td>' + email + '</td><td>' + pending_icon + delete_icon + '</td>';
				var rand_id = String( Math.random() );
				userid = 'add-user--1';
				$(this).parent().prev().html(email);
				$(this).css("display", "none");
				$(this).parent().append('<img id = "email-' + rand_id + '" src="images/loading.gif" width = "80px" height = "50px">');
				var item = $(this).parent();
				
			}
			
		}else{
			var email = '';
			var to_replace = '</table>';
			userid = userid.replace('add-user-', '');
			var calling_td = $(this).parent().html();
			var view_icon = '<a href="/SurveiFy/web/index.php?r=user-management%2Fuser%2Fview&id=' + userid + '" target = "_blank" class="fas fa-eye" title = "View user" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>&nbsp;&nbsp;';
			var delete_icon = '<a id = "delete-user-' + userid + '" class="fas fa-user-slash delete-user" title = "Revoke participation!" style = "cursor: pointer; text-decoration: none; color:#949494;"></a>';
			var row = $(this).parent().parent().html().replace( calling_td, view_icon + delete_icon );
			var to_remove = $(this).parent().parent();
			var participants = $(".participants-table").html().replace(to_replace, "");
		}
		

		$.ajax({
            url: ['index.php?r=site%2Fsurvey-participants'],
            type: 'POST',
            async : true,
            cache: false,
            data: {
                _csrf: yii.getCsrfToken(),
                'action': 'add',
                'surveyid': surveyid,
                'userid': userid.replace('add-user-', ''),
                'email': email
            },
            success  : function(response, status) {

            	if (userid != 'add-user--1'){
            		if ( to_remove.find(".add-user").attr('title') == "Allow user to participate!" ){
            			var text = parseInt( $('.dot').html() );
            			if ( text == 1 ){
            				$(".dot").remove();
						}else{
							$(".dot").html(text - 1);
						}
            			
            		}
            		to_remove.remove();
            	}else{
            		item.find("img").remove();
            		
            	}
            	if ( response.response != 'User already invited' && response.response != 'User already participating' && response.response != 'User already registered'){
            		if ( item ){
            			item.append('<a class = "fas fa-check" title ="Invited!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>');
            		}
            		$(".participants-table").append("<tr>" + row + "</tr></table>");

					// defined in veto.js
					let name = $($(row)[0]).html();
					let surname = $($(row)[1]).html();
					addToExpertSet(userid, `${name} ${surname}`);
            	}else{
            		item.prev().html('<input type="email" name="new-user-email" class ="form-control" value = "' + email + '">');
            		item.find('#invite-new-user').css("display", "block"); //('<a id = "invite-new-user" class="fas fa-user-check add-user" title = "Invite!" style = "color: #949494; cursor: pointer; text-decoration: none;"></a>&nbsp; &nbsp;');
            		item.prev().append("<div class = 'help-block'> " + response.response + "!</div>");
            	}
            	
            },
            error : function(){
            	console.log("error");
            }
        });
	   
	});

	$('body').on('click', 'a.fa-angle-down', function() {
		
		$(this).attr('class', 'fas fa-angle-up');
		var bodyClass = $(this).parent().parent().parent().parent().find('tbody').attr('class');
		$("." + bodyClass ).toggle();

	});

	$('body').on('click', 'a.fa-angle-up', function() {

		$(this).attr('class', 'fas fa-angle-down');
		var bodyClass = $(this).parent().parent().parent().parent().find('tbody').attr('class');
		$("." + bodyClass ).toggle();
		
	});

	$('body').on('click', 'input#reset-filter', function() {
		$(".submit-button").attr('name', 'non-finalize');
		$(".survey-create").submit();
	});

});


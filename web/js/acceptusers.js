$(document).ready(function(){
	$('body').on('click', "a[id^='participant-']", function(e) {
		var id = $(this).attr('id').replace('participant-', '');
		var surveyid = $("#participatesin-" + id + "-surveyid").val();
		var userid = $("#participatesin-" + id + "-userid").val();

		if ( $(this).hasClass('accept-user') ){
			var action = 'add';
			var class_change = 'fa-check';
			$(this).removeClass('fa-user-check');
			$(this).next().removeClass('fa-user-slash');
		}else{
			var action = 'delete';
			var class_change = 'fa-times';
			$(this).removeClass('fa-user-slash');
			$(this).prev().removeClass('fa-user-check');
		}
		var item = $(this);
		$(this).addClass(class_change);
		
		$.ajax({
            url: ['index.php?r=site%2Fsurvey-participants'],
            type: 'POST',
            async : true,
            cache: false,
            data: {
                _csrf: yii.getCsrfToken(),
                'action': action,
                'surveyid': surveyid,
                'userid': userid
            },
            success  : function(response, status) {
            	item.addClass(class_change);
            },
            error : function(){
            	console.log("error");
            }
        });
	});
});
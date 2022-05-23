$(document).ready(function(){

	

	$(".end-time").each(function( ) {
		var time = $(this).text();
		$(this).text("");
		if ( time == '(not set)' ){
			$(this).text("No expiration date");
			$(this).css("color", "#949494");
			return;
		}
		var variable = $(this);
		time_spl = time.split(" ");
		time_1 = time_spl[0];
		time_2 = time_spl[1];
		time_1 = time_1.split("-");
		time_2 = time_2.split(":");
		var end_year = parseInt(time_1[0]);
		var end_month = parseInt(time_1[1]) - 1;
		var end_day = parseInt(time_1[2]);
		var end_hour = parseInt(time_2[0]);
		var end_minute = parseInt(time_2[1]);
		var end_second = parseInt(time_2[2]);
		// 1995-12-17T03:24:00
		var end_date = end_year+"-"+end_month+"-"+"T0"+end_hour+":"+end_minute+":"+end_second
		var countDownDate = new Date(end_year, end_month, end_day, end_hour, end_minute, end_second).getTime(); 
		var x = setInterval(function() {
	 		// alert($(this).text());
	 		
	 	  	// Get today's date and time
			var now = new Date().getTime();
		  	// Find the distance between now and the count down date
		  	var distance = countDownDate - now;
		  	// alert(countDownDate + " " + distance);

		  	// Time calculations for days, hours, minutes and seconds
		  	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  	var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  	if ( seconds < 10 ){
		  		var seconds = "0" + seconds;
		  	}
		  	if ( minutes < 10 ){
		  		var minutes = "0" + minutes;
		  	}
		  	if ( hours < 10 ){
		  		var hours = "0" + hours;
		  	}
		  	if ( days < 10 ){
		  		var days = "0" + days;
		  	}
		  	if (distance < 0) {
			    clearInterval(x);
				$(variable).text("Expired");
				$(variable).css("color", "#dd7777");
			}else{
				$(variable).text(days + "d " + hours + "h " + minutes + "m " + seconds + "s ");
			}
  		}, 1000);
	});

	$(".start-time").each(function( ) {
		var time = $(this).text();
		$(this).text("");
		if ( time == '(not set)' ){
			$(this).text("No starting date");
			$(this).css("color", "#949494");
			return;
		}
		var variable = $(this);
		time_spl = time.split(" ");
		time_1 = time_spl[0];
		time_2 = time_spl[1];
		time_1 = time_1.split("-");
		time_2 = time_2.split(":");
		var end_year = parseInt(time_1[0]);
		var end_month = parseInt(time_1[1]) - 1;
		var end_day = parseInt(time_1[2]);
		var end_hour = parseInt(time_2[0]);
		var end_minute = parseInt(time_2[1]);
		var end_second = parseInt(time_2[2]);
		// 1995-12-17T03:24:00
		var end_date = end_year+"-"+end_month+"-"+"T0"+end_hour+":"+end_minute+":"+end_second
		var countDownDate = new Date(end_year, end_month, end_day, end_hour, end_minute, end_second).getTime(); 
		var start_interval = setInterval(function() {
	 		// alert($(this).text());
	 		
	 	  	// Get today's date and time
			var now = new Date().getTime();
		  	// Find the distance between now and the count down date
		  	var distance = countDownDate - now;
		  	// alert(countDownDate + " " + distance);

		  	// Time calculations for days, hours, minutes and seconds
		  	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  	var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  	if ( seconds < 10 ){
		  		var seconds = "0" + seconds;
		  	}
		  	if ( minutes < 10 ){
		  		var minutes = "0" + minutes;
		  	}
		  	if ( hours < 10 ){
		  		var hours = "0" + hours;
		  	}
		  	if ( days < 10 ){
		  		var days = "0" + days;
		  	}
		  	if (distance < 0) {
			    clearInterval(start_interval);
			    // variable.append()
				// $(variable).text(time).append('&nbsp;<a class="fas fa-clock" title ="Expired" style = "text-decoration: none; cursor: pointer; color: #dd7777;"></a>');
				$(variable).text(time);
				// $(variable).css("color", "#dd7777");
			}else{
				// $(variable).text(time).append('&nbsp;<a class="fas fa-clock" title ="' + days + "d " + hours + "h " + minutes + "m " + seconds + "s " + '" style = "text-decoration: none; cursor: pointer; color: #77dd77;"></a>');
				$(variable).text(days + "d " + hours + "h " + minutes + "m " + seconds + "s ");
			}
  		}, 1000);
	});


});
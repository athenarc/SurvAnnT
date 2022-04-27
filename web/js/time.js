

var start = new Date().getTime();


$('#save').click(function() {

    var end = new Date().getTime();
	var time = end - start;
    var secondsLabelactive = document.getElementById("seconds_active");
    var totalSecondsactive = secondsLabelactive.innerHTML;
    var times = document.getElementsByClassName("time");
    for (var i = 0; i < times.length; i++) {
       times[i].value = totalSecondsactive;
    }

});


window.onfocus = function() {

    
    localStorage.setItem('TimeActive', 1);
    if ( localStorage.getItem('TimeInactive') !== null ){
        localStorage.removeItem('TimeInactive');
    }
    // localStorage.removeItem('TimeInactive');

    var secondsLabelactive = document.getElementById("seconds_active");
    var totalSecondsactive = secondsLabelactive.innerHTML;
    var activeRefresh = setInterval(setTimeActive, 1000);
    
    

    function setTimeActive() {

        if ( localStorage.getItem("TimeActive") === null ) {
            clearInterval(activeRefresh);
        }
        ++totalSecondsactive;
        secondsLabelactive.innerHTML = totalSecondsactive;
        // console.log("Seconds active: " + totalSecondsactive);
    }
};

window.onblur = function(activeRefresh) {

    // localStorage.removeItem('TimeActive');
    localStorage.setItem('TimeInactive', 1);
    if ( localStorage.getItem('TimeActive') !== null ){
        localStorage.removeItem('TimeActive');
    }


    var secondsLabelinactive = document.getElementById("seconds_inactive");
    var totalSecondsinactive = secondsLabelinactive.innerHTML;
    var inactiveRefresh = setInterval(setTimeInactive, 1000);


    function setTimeInactive() {
        if (  localStorage.getItem("TimeInactive") === null  ) {
            clearInterval(inactiveRefresh);
        }    
        // console.log("inactive var: ", localStorage.getItem("TimeInactive"));
        ++totalSecondsinactive;
        secondsLabelinactive.innerHTML = totalSecondsinactive;
        // console.log("Seconds inactive: " + totalSecondsinactive);
    }

    
};




        
//     if (typeof SESSION['inactive_start'] !== 'undefined') {
//         alert("User active again");
//         SESSION['inactive_stop'] = SESSION['inactive_start'] - new Date().getTime();
//         alert("User was inactive for: ", SESSION['inactive_stop']);
//     }


const rankingsBody = document.querySelector("#rankings > tbody");

// function loadRankings () {
//     const request = new XMLHttpRequest();

//     request.open("get", "https://codepen.io/imisterk/pen/MLgwOa.js");
//     request.onload = () => {
//         try {
//             const json = JSON.parse(request.responseText);
//             populateRankings(json);
//         } catch (e) {
//             console.warn("Could not load Player Rankings! :(");
//         }
//     };

//     request.send();
// }

function populateRankings (json) {
    // Populate Leaderboard
    json.forEach((row) => {
        const tr = document.createElement("tr");

        row.forEach((cell) => {
            const td = document.createElement("td");
            td.textContent = cell;
            tr.appendChild(td);
        });

        rankingsBody.appendChild(tr);
    });
}

// document.addEventListener("DOMContentLoaded", () => { loadRankings (); });
$(document).on('keyup', 'input[id^="search-leaderboard-"]' , function() {

    var value = this.value;
    var id = this.id.replace('search-leaderboard-', '');
    
    $( "#rankings-" + id ).find("tr").each(function(index) {

        if (index === 0) return;

        var if_td_has = false;
        $(this).find('td').each(function () {
            if_td_has = if_td_has || $(this).text().indexOf(value) !== -1; //Check if td's text matches key and then use OR to check it for all td's
        });

        $(this).toggle(if_td_has);

    });
});

$(document).on('change', ".leaderboard-selection" , function() {

    // alert($(this).val());
    $(".leaderboard-form").submit();
});

// $(document).on('click', ".text-statistics" , function() {
//     var id = $(this).data("value");
//     $.ajax({
//         url: ['index.php?r=site%2Fget-abstract-stats'],
//         type: 'POST',
//         data : { 'abstract_id_rated' : id},
//         cache : false,
//         async : true,
        
//         success  : function(data) {
            
            
//             $('.abstract-info-dialog').html(data);

//         },

//         error: function(){
//             var html = ' <table class="table table-bordered table-hover flag-table"> \
//                     <tr> \
//                     <th colspan = 3 class ="flag-variant-modal-header-1" style = "color:#B5312D !important; ">Unfortunately no statistics for abstracts were retrieved. Please try again.</th> \
//                     </tr> \
//                 </table> \ ';
//             $('.abstract-info-dialog').html(html);


//         }

//     });
//     $('.abstract-info').modal("toggle");
// });
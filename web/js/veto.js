let expertSet = [];
let found = false;

async function addToExpertSet(userId, name) {
    console.log(userId);
    console.log(name);
    try {
        await getExpert(userId, name);
    } catch (err) {
        console.error(err);
    }
}

function removeFromExpertSet(userId) {

    // hide name from found participants
    $(`#veto-participant-${userId}`).hide();

    // delete from expert set
    expertSet = expertSet.filter( (e) => e.userId !== userId);
}

function getExpert(userId, name) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `https://veto.imsi.athenarc.gr/api/datasets/get?term=${name}`,
            type: 'GET',
            async : true,
            cache: false,
            success  : function(response) {

                const isEmpty = Object.keys(response).length === 0 && response.constructor === Object; // find a more elegant way to check if obj is empty (consider lodash?)
                
                if (!isEmpty) {
                    if( $('#found-participants').html() == '<i>Sorry, no selected participant can be found in the selected dataset.</i>' ){
                        $('#found-participants').html("");
                    }
                    $('#found-participants').prepend(`<span id="veto-participant-${userId}" veto-id="${response.id}">${name + ((found) ? ', ' : '')}</span>`);
                    expertSet.push({
                        userId, 
                        vetoId: response.id,
                    });

                    // first time a participant is found
                    if (!found) {

                        // enable exec button and change title
                        $('#veto-exec-button').attr("disabled", false);
                        $('#veto-exec-button').attr("title", "Execute analysis to retrieve recommendations."); 
                        found = true;
                    }
                }
                resolve();
            },
            error : function(err){
                reject(err);
            }
        });
    });
}

$(document).ready( function() {
    
    let analysisId = null;
    let interval = null;

    getExpertSet();

    async function getExpertSet() {
        expertSet = [];
        found = false;

        let participants = JSON.parse($("#veto-participants").val());

        for (const [key, participant] of Object.entries(participants)) {

            let participantName = `${participant.name} ${participant.surname}`
            
            try {

                // check if the specified name exists in veto
                await getExpert(participant.id, participantName);

            } catch (err) {
                console.error(err);
            }
        }

        // hide loading for participants
        $("#found-participants-loading").hide();

        // no particants found in veto's dataset
        if (!found) {
            $('#found-participants').html("<i>Sorry, no selected participant can be found in the selected dataset.</i>");
        }
    }

    function getStatus() {
        $.ajax({
            url: `https://veto.imsi.athenarc.gr/api/analysis/status?id=${analysisId}`,
            type: 'GET',
            async : true,
            cache: false,
            success  : function(response) {
                
                // update loading message
                $('#veto-loading-message').attr('style', `width: ${response.progress}%`);

                // analysis has completed
                if (response.progress == 100) {
                    
                    // stop polling for updated status, analysis has finished
                    clearInterval(interval); 

                    // get the results
                    getResults();
                }
            },
            error : function(err){
                clearInterval(interval);
                console.error(err);
            }
        });
    }

    function getResults() {
        $.ajax({
            url: `https://veto.imsi.athenarc.gr/api/analysis/get?id=${analysisId}`,
            type: 'GET',
            async : true,
            cache: false,
            success  : function(response) {
                // re-enable button and hide progress bar
                $('#veto-exec-button').attr("disabled", false);
                $('#veto-loading-progress').hide();

                // show first results
                const resultItems = response.docs.slice(0, 5).map( (result) => {
                    return `<li class="list-group-item" style="padding: 0.3rem 0.7rem;">
                        <div class="row">
                                <div class="col-md-8">
                                    ${result.name}
                                </div> 
                                <div class="col-md-4">
                                    <span class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: ${result.Score*100}%"></div>
                                    </span>
                                </div>
                        </div>
                    </li>`;
                });
                const moreItem = `<li class="list-group-item text-center" style="padding: 0.3rem 0.7rem;"><a href="https://veto.imsi.athenarc.gr/jobs/${analysisId}" target="_blank" class="text-info">See more results <i class="fa-solid fa-square-arrow-up-right"></i></a></li>`
                resultItems.push(moreItem);

                $('#veto-results').html(resultItems);
            },
            error : function(err){
                console.error(err);
            }
        });
    }

    $('#veto-exec-button').on('click', function() {

        // reset progress from previous execution
        $('#veto-loading-message').attr('style', 'width: 0%');
        $('#veto-results').html('');
       
        $.ajax({
            url: 'https://veto.imsi.athenarc.gr/api/analysis/submit',
            type: 'POST',       
            contentType: "application/json",
            async : false,
            cache: false,
            data: JSON.stringify({
                expertSet: expertSet.map( (e) => e.vetoId ),

                // default veto params
                simThreshold: 0.2,
                simMinValues: 3,
                simsPerExpert: 100,
                apvWeight: 0.5,
                aptWeight: 0.5,
                outputSize: 100
            }),
            success: function(response) {
                analysisId = response.id;
            },
            error: function(err){
                console.error(err);
            }
        });

        // disable exec button and show progress bar
        $('#veto-exec-button').attr("disabled", true);
        $('#veto-loading-progress').show();
        
        // check for analysis status every 2sec
        interval = setInterval(getStatus, 2000);

        return false;
    });
});
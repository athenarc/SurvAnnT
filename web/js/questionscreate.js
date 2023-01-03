$(document).ready(function(){


    $('body').on('click', 'a.add-question', function(e) {
        // FUNCTION THAT ADDS QUESTION FIELD
        e.preventDefault();

        var numItems = parseInt( $(".dataset-tools").last().attr("id").replace("dataset-tools-", "") ) + 1;
        var dataset_tools = $(".dataset-tools").last().clone().prop('outerHTML').replaceAll(numItems - 1, numItems).replace("Dataset " + numItems, "Dataset " + ( numItems + 1 ) ).replace("Question " + numItems, "Question " + ( numItems + 1 ) );
        var dataset_form = $(".dataset-form").last().clone().removeClass("col-md-12").prop('outerHTML').replaceAll("-" + ( numItems - 1 ), "-" + numItems).replaceAll("[" + ( numItems - 1 ), "[" + numItems);

        var ownerid = $("div[class*=-" + ( numItems - 1 ) + "-ownerid] > input").last().val();
        var destroy = $("div[class*=-" + ( numItems - 1 ) + "-ownerid] > input").last().val();
        var dataset_ownerid = $("div[class*=-" + ( numItems - 1 ) + "-ownerid]").last().clone().attr("value",ownerid).prop('outerHTML').replaceAll(numItems - 1, numItems);
        var dataset_destroy = $("div[class*=-destroy-" + ( numItems - 1 ) +"]" ).last().clone().attr("value",destroy).prop('outerHTML').replaceAll(numItems - 1, numItems);


        $(".datasets-table > #questions-form >  .button-row-2").before(dataset_tools);
        $(".datasets-table > #questions-form >  .button-row-2").before(dataset_form);
        $(".datasets-table > #questions-form > .button-row-2").before(dataset_ownerid);
        $(".datasets-table > #questions-form >  .button-row-2").before(dataset_destroy);
        $(".dataset-form").last().addClass("col-md-12");
        $(".dataset-form").last().find(".form-group").each(function( ) {
            $(this).find("textarea").text("");
            $(this).find("input").text("");
            $(this).removeClass("has-error");
            $(this).find(".help-block").text("");
        });

        $('#questions-form').yiiActiveForm('add', {
            id: 'questions-'+numItems+'-question',
            name: '['+numItems+'][question]',
            container: '.field-questions-'+numItems+'-question',
            input: '#questions-'+numItems+'-question',
            error: '.help-block',
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {message: "Question cannot be blank."});
            }
        });
    });

    $(document).on('change','select',function(){
        // FUNCTION THAT CHANGES ANSWERVALUES FIELD ACCORDING TO ANSWERTYPE SELECT OPTION
        if ( $(this).attr('id') == 'surveys-fields' ){
            return;
        }
        var answer_type = $(this).val().replace("(", "-").replace(")", "");
        if ( $(this).attr("id") ){
            var id = $(this).attr("id").replace("questions-", "").replace("-answertype", "");
            var ans = String(answer_type + '-' + id);
            if ( answer_type == 'Likert-5' || answer_type == 'Likert-7' ){
                var likert_obj = jQuery.parseJSON( $( "#" + answer_type ).val( ) );
                var table =    `Table <a id ="link-show-` + answer_type + `-` + id + `" class="fa-solid fa-caret-down link-icon"></a>
                                <table id = "table-show-` + answer_type + `-` + id + `" class = "table table-striped table-bordered" style ="display: none;"> 
                                    <tr class = "dataset-table-header-row"> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 10%;">  </td> 
                                    </tr>`;
                var counter = 0;
                for (key in likert_obj) {
                    table +=    `<tr> 
                                    <td> 
                                        <input type="text" value="` + likert_obj[key] + `" name="question-` + id + `-` + answer_type + `-<` + counter + `>-answer" class = "form-control"> 
                                    </td> 
                                    <td> 
                                        <input type="text" value="` + key + `" name="question-` + id + `-` + answer_type + `-<` + counter + `>-value" class = "form-control"> 
                                    </td> 
                                    <td> 
                                        <a id = "delete-radioList-` + key + `-` + likert_obj[key] + `" class="fas fa-trash-alt link-icon delete-radioList-key"></a> 
                                    </td> 
                                </tr>`;
                    counter += 1;
                }
                table += '</table>';
            }

            $( "#" + ans ).parent().children().each(function( ) {
                if ( $(this).attr('id') ){
                    if ( $(this).attr('id') != ans ){
                        $(this).css("display", "none");
                    }else{
                        $(this).css("display", "table-cell");
                        $(this).html(table);
                    }
                }
            });
        }
    });

    $(document).on('click', '[id^="link-show"]', function(e){
        if ( $(this).hasClass("fa-caret-down") ){
            $(this).removeClass("fa-caret-down").addClass("fa-caret-up");
            $("#" + this.id.replace("link", "table")).css("display", "table");
        }else if ( $(this).hasClass("fa-caret-up") ){
            $(this).removeClass("fa-caret-up").addClass("fa-caret-down");
            $("#" + this.id.replace("link", "table")).css("display", "none");
        }
        

    });

    $(document).on('click', '[id^="questions-actions-"]', function(e){

        var questionQuestion = '';
        var questionTooltip = '';
        var questionAllowUsers = '';
        var questionAnswer = '';
        var questionAnswerType = '';
        var questionAnswerValues = '';
        var questionId = $(this).attr('id').replace('questions-actions-', '');
        var surveyId = $("#surveyId").val();

        if( $(this).hasClass('edit-question') ){
            $(this).removeClass('fa-pencil edit-question').addClass('fa-check save-question');
            $(this).css("color", "#77dd77");
            $(".edit-question-question-" + questionId).toggle();
            $(".edit-question-tooltip-" + questionId).toggle();
            $(".edit-question-allowusers-" + questionId).toggle();
            $(".edit-question-answertype-" + questionId).toggle();
            $(".edit-question-answer-" + questionId).toggle();
            return;
        }else if ( $(this).hasClass('delete-question') ){
            var action = "delete";
            
        }else if ( $(this).hasClass('save-question') ){
            var action = "modify";
            questionQuestion = $("#question-question-" + questionId).val();
            questionTooltip = $("#question-tooltip-" + questionId).val();
            questionAllowUsers = $("#question-allowusers-" + questionId).val();
            questionAnswerType = $("#questions-" + questionId + "-answertype").val();
            
            if (questionAnswerType == 'textInput'){
                questionAnswer = $("#questions-" + questionId + "-answer").val();
            }else{
                questionAnswerValues = '';
                var counter = 0;
                var total_counter = 0;
                $("#" + questionAnswerType + "-" + questionId).find(".form-control").each(function( ) {
                    if ( counter == 1 ){

                        questionAnswerValues += this.value + "<<>>";
                        // ANSWER
                        $(".question-answertype-" + questionId + "-text-key-"+ total_counter ).text(this.value);

                    }else{
                        // KEY
                        questionAnswerValues += this.value + "<>";
                        $(".question-answertype-" + questionId + "-text-answer-"+ total_counter ).text(this.value);
                    }
                    
                    counter ++;
                    
                    if ( counter == 2 ){
                        total_counter ++;
                        counter = 0;
                    }
                });
            }
            questionAnswerValues = questionAnswerValues.slice(0, -4);
        }

        
        var element = $(this);
        $.ajax({
            type: "POST",
            url: ['index.php?r=questions%2Fquestion-edit'],
            data: {
                action: action,
                questionId: questionId,
                surveyId: surveyId,
                questionQuestion: questionQuestion,
                questionTooltip: questionTooltip,
                questionAllowUsers: questionAllowUsers,
                questionAnswerType: questionAnswerType,
                questionAnswer: questionAnswer,
                questionAnswerValues: questionAnswerValues
                },
            success: function (response) {
                
                if( response.action == 'modify' ){
                    $(".question-question-" + questionId + "-text").text(questionQuestion);
                    $(".question-tooltip-" + questionId + "-text").text(questionTooltip);
                    $(".question-answertype-" + questionId + "-text").text(questionAnswerType);
                    // $(".question-answervalues-" + questionId + "-text").text(questionAnswervalues);
                    if(response.questionAllowUsers == 1){
                        $(".edit-question-allowusers-" + questionId).text('Yes');
                    }else{
                        $(".edit-question-allowusers-" + questionId).text('No');
                    }
                    $(".edit-question-question-" + questionId).toggle();
                    $(".edit-question-tooltip-" + questionId).toggle();
                    $(".edit-question-allowusers-" + questionId).toggle();
                    $(".edit-question-answertype-" + questionId).toggle();
                    $(".edit-question-answer-" + questionId).toggle();
                    element.removeClass('fa-check save-question').addClass('fa-pencil edit-question');
                    element.css("color", "#949494");

                }else if( response.action == 'delete' ){
                    element.parent().parent().remove();
                    if($('.questions-table-row').length == 0){

                        $(".questions-delete-all").remove();
                    }
                }
            },
            error: function (error) {
                console.log(error)
            }
        })
    });

    
    $(document).on('click','.delete-radioList-key',function(){
        // FUNCTION THAT DELETES QUESTION FIELD
        if ( $(this).parent().parent().parent().find("tr").length > 2 ){
            $(this).parent().parent().remove();
        }
        
    });

});
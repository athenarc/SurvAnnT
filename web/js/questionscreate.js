$(document).ready(function(){

    $(document).on('change','select',function(){
        // FUNCTION THAT CHANGES ANSWERVALUES FIELD ACCORDING TO ANSWERTYPE SELECT OPTION
        if ( $(this).attr('id') == 'surveys-fields' ){
            return;
        }
        var answer_type = $(this).val().replace("(", "-").replace(")", "");
        
        
        if ( $(this).attr("id") )
        {

            var id = $(this).attr("id").replace("questions-", "").replace("-answertype", "");
            var ans = String(answer_type + '-' + id);

            if ( answer_type == 'Likert-5' || answer_type == 'Likert-7' ){
                var likert_obj = jQuery.parseJSON( $( "#" + answer_type ).val( ) );
                
                var table =     `<table class = "table table-striped table-bordered"> 
                                    <tr class = "dataset-table-header-row"> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td> 
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 10%;">  </td> 
                                    </tr>`;
                var counter = 0;
                for (key in likert_obj) {
                    // alert('key='+key+', value='+likert_obj[key]);
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

    $(document).on('click','.delete-question',function(){
        // FUNCTION THAT DELETES QUESTION FIELD
        var id = $(this).attr('id').replace("dataset-", "");
        $( "#dataset-tools-" + id ).remove();
        $( "#dataset-form-" + id ).remove();
        $( "#destroy-" + id ).val(1);
    });

    $(document).on('click','.delete-radioList-key',function(){
        // FUNCTION THAT DELETES QUESTION FIELD
        if ( $(this).parent().parent().parent().find("tr").length > 2 ){
            $(this).parent().parent().remove();
        }
        
    });

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

        

        $(".datasets-table > .button-row-2").before(dataset_tools);
        $(".datasets-table > .button-row-2").before(dataset_form);
        $(".datasets-table > .button-row-2").before(dataset_ownerid);
        $(".datasets-table > .button-row-2").before(dataset_destroy);
        $(".dataset-form").last().addClass("col-md-12");
        $(".dataset-form").last().find(".form-group").each(function( ) {
          $(this).find("textarea").text("");
          $(this).find("input").text("");
          $(this).removeClass("has-error");
          $(this).find(".help-block").text("");
        });
    });
});
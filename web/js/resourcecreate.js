$(document).ready(function(){

	// $(document).on('change', '.user-type-select, .resource-type', function(){
 //        var item_class = $(this).val();
 //        if ( $(this).hasClass('user-type-select') ){
 //            var input = $("<input>").attr("type", "hidden").attr("name", "db").val("db");
 //            if ( $(this).val() != '' ){
 //                $('.resource-form').append($(input));
 //                $(".resource-form").submit();
 //            }
            
 //        }else{
 //            $( ".resource-types" ).css("display", "none");
 //            $( "." + item_class ).toggle();
 //        }
        

 //    });

    $(document).on('change', '#user-resource-types, #db-resource-types, #dir-resource-types', function(){

        var datastring = $(".resource-before-form").serialize();
        $(this).attr('name', 'resources-type');
        // $(".resource-before-form").submit();
        
    });

    $(document).on('click', '.submit-action-form', function(e){
        e.preventDefault();
        $(".resources-number > .col-md-6 ").each(function( ) {
            // alert($(this).css("display"));
            if ( $(this).css("display") == "block" ){
                $(this).find("select").attr("name", "resources-type");
            }
        });
        $('.resource-before-form').yiiActiveForm('remove', 'collection-name');
        $(".resource-before-form").submit();
        
    });



    $(document).on('change', '.user-resource-select', function(){

        if ( $(this).val() == 'db-load' ){
            
            $(".db-resource-types").css("display", "block");
            $(".db-resource-types").find("select").prop("disabled", false);
            
            $(".user-resource-types").css("display", "none");
            $(".user-resource-types").find("select").prop("disabled", true);

            $(".dir-resource-types").css("display", "none");
            $(".dir-resource-types").find("select").prop("disabled", true);

        }else if ($(this).val() == 'user-form') {
            
            $(".user-resource-types").css("display", "block");
            $(".user-resource-types").find("select").prop("disabled", false);

            $(".db-resource-types").css("display", "none");
            $(".db-resource-types").find("select").prop("disabled", true);

            $(".dir-resource-types").css("display", "none");
            $(".dir-resource-types").find("select").prop("disabled", true);

        }else{

            $(".dir-resource-types").css("display", "block");
            $(".dir-resource-types").find("select").prop("disabled", false);

            $(".user-resource-types").css("display", "none");
            $(".user-resource-types").find("select").prop("disabled", true);

            $(".db-resource-types").css("display", "none");
            $(".db-resource-types").find("select").prop("disabled", true);
            
        }

    });

    $(document).on('click', '.edit-button', function(e){
        e.preventDefault();
        $(".edit-tools").toggle();

    });

    $(document).on('click', '.use-all-button', function(e){
        e.preventDefault();
        $("[id*='use-']").each(function( ) {
            if ( $(this).not(':checked') ){
                $(this).attr("checked", true);
            }
        });

    });
    

    $(document).on('click', '.add-button', function(e){
        e.preventDefault();
        var resources_count = parseInt( $("[class*='resource-object-']").length );
        var last_id = resources_count - 1;
        var new_id = resources_count;
        var new_resource = $( ".resource-object-" + last_id ).prop("outerHTML").replaceAll("-" + last_id, "-" + new_id ).replaceAll("[" + last_id + "]", "[" + new_id + "]" );

        $(this).parent().parent().before(new_resource);

        var image ='<div class="form-group field-image-' + new_id + '"> \
        <label class="control-label" for="image-' + new_id + '">Image</label> \
        <input type="hidden" name="Resources[' + new_id + '][image]" value=""><input type="file" id="image-' + new_id + '" name="Resources[' + new_id + '][image]" value=""> \
        <div class="help-block"></div> \
        </div>';

        var allowusers = '<div class="form-group field-resources-' + new_id + '-allowusers"> \
            <input type="hidden" name="Resources[' + new_id + '][allowusers]" value="1"><label><input type="checkbox" id="resources-' + new_id + '-allowusers" name="Resources[' + new_id + '][allowusers]" value="1" > Allow</label> \
            <div class="help-block"></div>\
        </div>';

        // alert();
        if ( $(".resource-object-" + new_id).last().find(".float-left").find(".form-group").length == 0 ){
            $(".resource-object-" + new_id).last().find(".float-left").append(allowusers);
        }

        if ( $(".resource-object-" + new_id).last().find("tbody:nth-child(1) > tr:nth-child(2) > .image-input").find(".form-group").length == 0 ){
            $(".resource-object-" + new_id).last().find(".image-input").append(image);
        }

        $("[class*='resource-object-']").last().find(".form-group").each(function( ) {
            
          $(this).find("textarea").removeAttr("disabled").text("");
          // alert($(this).find("#resources-" + new_id + "-type").attr("value"));
          // $(this).find("input").find("[id*=article-allowusers-" + new_id + "]").removeAttribute("checked");
          if( $(this).find("input:checkbox").attr("id") != undefined ){
            $(this).find("input:checkbox").removeAttr("checked");
          }
          
          $(this).find("input").not("#resources-" + new_id + "-type").removeAttr("disabled").attr("value", null);
          if( $(this).find("input:checkbox").attr("value") == undefined ){
            $(this).find("input:checkbox").attr("value", 1);
          }
          $(this).find( "#image-" + new_id ).removeAttr("disabled").attr("value", null);
          $( "#image-preview-" + new_id ).removeAttr("disabled").attr("src", null);
          $( "#resources-" + new_id + "-id" ).removeAttr("disabled").val("");
          $(this).removeClass("has-error");
          $(this).find(".help-block").text("");

        });

    });

    $(document).on('click', "input[id^='use-']", function(e){

        if ($(this).val() == 0){
            $(this).val(1);
        }else{
            $(this).val(0);
        }
    });

    $('button[name="next"]').click(function(e){
        e.preventDefault();
        var resources_count = $( "input[id^='use-']" ).length;
        count = 0;
        $( "input[id^='use-']" ).each(function() {
          if (this.checked){
            count ++;
          }
        });
        if ( count == 0 ){
            // $(".button-row > div:nth-child(1)").html("<div class = 'help-block text-center'>No resource selected for usage!</div>");
            if ( ! $(".datasets-table").last().find('row').hasClass("no-resource") ){
                $(".datasets-table").last().prepend("<row class = 'text-center help-block no-resource'> <div class = 'col-md-12'><h3><i>No resource selected for usage. Please select at least one resource.</i></h3></div></row><br>");
            }
            return ;
        }
        $(".resources-number > .col-md-6 ").each(function( ) {
            // alert($(this).css("display"));
            if ( $(this).css("display") == "block" ){
                $(this).find("select").attr("name", "resources-type");
            }
        });
        $(this).attr("name" , "submit-resource-form");

        if ( $(".resource-before-form").length ){
            $(".resource-before-form").submit();
        }
        

    });


    $('body').on('click', 'a.hide-dataset', function() {

        if ( $(this).hasClass('collections') ){
            var id = $(this).attr('id').replace("dataset", "collection-resources");
            $("#" + id ).toggle();
        }else{
            var id = $(this).attr('id').replace("dataset", "table");
            $("." + id ).toggle();
        }
        
        

    });

    $('body').on('click', 'a.delete-dataset', function() {

        
        var id = $(this).attr('id').replace("dataset", "table");
        var hidden_input = id.replace("table-", "");

        // CHANGE HIDDEN INPUT VALUE TO 1, IN ORDER TO DELETE IN BACKEND
        if ( $(this).hasClass("fa-user-slash") ){
            $("#destroy-" + hidden_input ).val("1");
            $(this).prev().removeClass("fa-eye");
            $(this).removeClass("fa-user-slash").addClass("fa-undo").prop("title", "Undo delete");
            $("." + id ).css("display", "none");
        }else{
            $("#destroy-" + hidden_input ).val("0");
            $(this).prev().addClass("fa-eye");
            $(this).removeClass("fa-undo").addClass("fa-user-slash").prop("title", "Delete dataset");
            $("." + id ).css("display", "block");
        }

    });

    $('body').on('click', 'a.delete-resource', function() {

        
        var id = $(this).attr('id').replace("dataset", "table");
        alert(id);

    });

    $('body').on('click', 'a.add-dataset', function(e) {

        e.preventDefault();
        var numItems = parseInt( $('.dataset-tools').length );
        var dataset_tools = $(".dataset-tools").last().clone().prop('outerHTML').replaceAll(numItems - 1, numItems).replace("Dataset " + numItems, "Dataset " + ( numItems + 1 ) ).replace("Question " + numItems, "Question " + ( numItems + 1 ) );
        var dataset_form = $(".dataset-form").last().clone().prop('outerHTML').replaceAll("-" + ( numItems - 1 ), "-" + numItems).replaceAll("[" + ( numItems - 1 ), "[" + numItems);
        
        var dataset_ownerid = $("div[class*=-" + ( numItems - 1 ) + "-ownerid]").last().clone().prop('outerHTML').replaceAll(numItems - 1, numItems);
        var dataset_surveyid = $("div[class*=-" + ( numItems - 1 ) + "-surveyid]").last().clone().prop('outerHTML').replaceAll(numItems - 1, numItems);
        var dataset_destroy = $("div[class*=-" + ( numItems - 1 ) +"]" ).last().clone().prop('outerHTML').replaceAll(numItems - 1, numItems);

        $(".datasets-table > .button-row-2").before(dataset_tools);
        $(".datasets-table > .button-row-2").before(dataset_form);
        $(".datasets-table > .button-row-2").before(dataset_ownerid);
        $(".datasets-table > .button-row-2").before(dataset_surveyid);
        $(".datasets-table > .button-row-2").before(dataset_destroy);

        $(".dataset-form").last().find(".form-group").each(function( ) {
          $(this).find("textarea").text("");
          $(this).find("input").text("");
          $(this).removeClass("has-error");
          $(this).find(".help-block").text("");
        });
        // alert($(this).parent().parent().html());

    });

    $(document).on('change','select',function(){
        if ( $(this).attr('id') == 'surveys-fields' ){
            return;
        }
        var answer_type = $(this).val();
        if ( $(this).attr("id") )
        {
            var id = $(this).attr("id").replace("questions-", "").replace("-answertype", "");
            var help_modal = '<a data-toggle="modal" data-target=".help" class="fas fa-info-circle tooltip-icon" title="" aria-hidden="true"></a>';
            if ( answer_type == 'textInput' ){
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answer").parent().css("display", "block");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues").parent().css("display", "none");
                $(this).parent().parent().parent().prev().find("td:nth-child(2)").text("Answer");
                $(this).parent().parent().parent().find(".likert-7").css("display", "none");
                $(this).parent().parent().parent().find(".likert-5").css("display", "none");
            }else if( answer_type == 'radioList' ){
                $(this).parent().parent().parent().prev().find("td:nth-child(2)").text("Answer values");
                $(this).parent().parent().parent().prev().find("td:nth-child(2)").append(help_modal);
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answer").parent().css("display", "none");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues").parent().css("display", "block");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues > textarea").css("color", "lightgrey");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues > textarea").attr("placeholder", "{\n\t\"1\" : \"value\"\n}");
                $(this).parent().parent().parent().find(".likert-7").css("display", "none");
                $(this).parent().parent().parent().find(".likert-5").css("display", "none");
            }else{
                $(this).parent().parent().parent().prev().find("td:nth-child(2)").text("Answer => Value");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues").parent().css("display", "none");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answer").parent().css("display", "none");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues > textarea").attr("placeholder", "");
                $(this).parent().parent().parent().find(" td > .field-questions-" + id + "-answervalues > textarea").val("");
                if ( answer_type == 'Likert(5)' ){
                    $(this).parent().parent().parent().find(".likert-5").css("display", "block");
                    $(this).parent().parent().parent().find(".likert-7").css("display", "none");
                }else{
                    $(this).parent().parent().parent().find(".likert-7").css("display", "block");
                    $(this).parent().parent().parent().find(".likert-5").css("display", "none");
                }
                
            }
        }
    });
    
});
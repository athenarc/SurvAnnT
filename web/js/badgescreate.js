$(document).ready(function(){

    $(document).on('click', '.add-badge', function(e){
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

        if ( $(".resource-object-" + new_id).last().find(".float-left").find(".form-group").length == 0 ){
            $(".resource-object-" + new_id).last().find(".float-left").append(allowusers);
        }

        if ( $(".resource-object-" + new_id).last().find("tbody:nth-child(1) > tr:nth-child(2) > .image-input").find(".form-group").length == 0 ){
            $(".resource-object-" + new_id).last().find(".image-input").append(image);
        }

        $("[class*='resource-object-']").last().find(".form-group").each(function( ) {
            
            $(this).find("textarea").removeAttr("disabled").text("");
            $(this).find("input").each(function( index ) {
                if ( $( this ).attr('id') == 'rate-condition-' + new_id || $( this ).attr('id') == 'survey-condition-' + new_id ){
                    $(this).removeAttr("disabled").attr("value", 0);
                }else if( $( this ).attr('id') == 'badges-' + new_id + '-name' ){
                    $(this).removeAttr("disabled").attr("value", 'badge-' + ( new_id + 1 ) );
                }else{
                    $(this).removeAttr("disabled").attr("value", null);
                }
            });

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

    $(document).on('click', '#badges-used', function(e){
        if ( this.checked ){
            $(".datasets-table").css("display", "block");
            $(".edit-button").css("display", "block");
        }else{
            // alert("not checked");   
            $(".datasets-table").css("display", "none");
            $(".edit-button").css("display", "none");
        }
    });

});
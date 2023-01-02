$(document).ready(function(){
let new_fields = [];
let appended_fields = [];
    // $(document).on('change', '.user-resource-select, #user-resource-types', function(){
    //     // $('.resource-before-form').yiiActiveForm('remove', 'collection-name');
    //     $(".resource-before-form").submit();

    //     // alert($(".resource-before-form").serialize());
    // });

    $('button[name="discard-collection"]').click(function(e){
        e.preventDefault();
        $('button[name="discard-collection"]').val("discard");
        $(".resource-before-form").submit();
    });

    $(document).on('submit', 'form#db-resources-form', function(event) {
        
        $(".resource-use").each(function( ) {
            var checkbox = $("#" + $(this).attr('id')).clone().css("display", "none");
            $("#db-resources-form").append(checkbox);
        });
    }) 
    $(document).on('click', '.close-status-message', function(){
        $(this).parent().parent().parent().css("display", "none");
    });

    $(document).on('click', '[id^="resources-actions-"]', function(e){

        var resourceTitle = '';
        var resourceAbstract = '';
        var resourceText = '';
        var resourcePmc = '';
        var resourceDoi = '';
        var resourcePubmedId = '';
        var resourceAuthors = '';
        var resourceYear = '';
        var resourcePublic = '';
        var resourceJournal = '';
        var resourceId = $(this).attr('id').replace('resources-actions-', '');
        var surveyId = $("#surveyId").val();

        if( $(this).hasClass('edit-resource') ){
            $(this).removeClass('fa-pencil edit-resource').addClass('fa-check save-resource');
            $(".edit-resource-" + resourceId).parent().removeClass('text-overflow-ellipsis');
            $(this).css("color", "#77dd77");
            $(".edit-resource-" + resourceId).toggle();
            return;
        }else if( $(this).hasClass('delete-resource') ){
           var action = "delete";
            
        }else if ( $(this).hasClass('save-resource') ){
            var action = 'modify';
            resourceTitle = $("input[name=resource-title-"+resourceId+"]").val();
            resourceAbstract = $("textarea[name=resource-abstract-"+resourceId+"]").val();
            resourceText = $("textarea[name=resource-text-"+resourceId+"]").val();
            resourceYear = $("input[name=resource-year-"+resourceId+"]").val();
            resourcePmc = $("input[name=resource-pmc-"+resourceId+"]").val();
            resourceDoi = $("input[name=resource-doi-"+resourceId+"]").val();
            resourcePubmedId = $("input[name=resource-pubmed_id-"+resourceId+"]").val();
            resourceAuthors = $("input[name=resource-authors-"+resourceId+"]").val();
            resourceJournal = $("input[name=resource-journal-"+resourceId+"]").val();
            resourcePublic = $("select[name=resource-allowusers-"+resourceId+"]").val();
        }


        var element = $(this);
        $.ajax({
            type: "POST",
            url: ['index.php?r=resources%2Fresource-edit'],
            data: {
                action: action,
                resourceId: resourceId,
                surveyId: surveyId,
                resourceTitle: resourceTitle,
                resourceAbstract: resourceAbstract,
                resourceText: resourceText,
                resourceYear: resourceYear,
                resourcePmc: resourcePmc,
                resourceDoi: resourceDoi,
                resourceAuthors: resourceAuthors,
                resourcePublic: resourcePublic,
                resourceJournal: resourceJournal,
                resourcePubmedId: resourcePubmedId
                },
            success: function (response) {
                
                if( response.action == 'modify' ){
                    
                    if(response.resourcePublic == 1){
                        $(".resource-public-" + resourceId).text('Yes');
                    }else{
                        $(".resource-public-" + resourceId).text('No');
                    }
                    $(".resource-title-" + resourceId).text(resourceTitle);
                    $(".resource-abstract-" + resourceId).text(resourceAbstract);
                    $(".resource-text-" + resourceId).text(resourceText);
                    $(".resource-year-" + resourceId).text(resourceYear);
                    $(".resource-pmc-" + resourceId).text(resourcePmc);
                    $(".resource-authors-" + resourceId).text(resourceAuthors);
                    $(".resource-doi-" + resourceId).text(resourceDoi);
                    $(".resource-pubmed_id-" + resourceId).text(resourcePubmedId);
                    $(".resource-journal-" + resourceId).text(resourceJournal);
                    $(".edit-resource-" + resourceId).toggle();
                    $(".edit-resource-" + resourceId).parent().addClass('text-overflow-ellipsis');
                    element.removeClass('fa-check save-resource').addClass('fa-pencil edit-resource');
                    element.css("color", "#949494");
                }else if( response.action == 'delete' ){ 
                    element.parent().parent().remove();
                    if ( $(".resource-table-row").length == 0 ){
                         $(".resources-delete-all").remove();
                        $("#db-resources-button").attr('disabled', false);
                        $("#user-resources-button").attr('disabled', false);
                    }
                }
            },
            error: function (error) {
                console.log(error)
            }
        })

    });
    $(document).on('click', "input[id^='resource-use-']", function(e){

        // FUNCTION WHICH DESELECTS PREVIOUSLY SELECTED COLLECTIONS WHEN THE RESOURCE IS QUESTIONAIRE
        var flag = 0;
        var id = $(this).attr('id');
        if ( $( '#resourcessearch-type' ).val() == 'questionaire' ){
            flag = 1;
        }

        if ( flag == 1){
            var count = 0;
            $( "input[id^='resource-use']" ).each(function() {
              if (this.checked && this.id != id){
                $("#" + this.id).prop( "checked", false );
              }
            });
        }

        if ($(this).val() == 0){
            $(this).val(1);
        }else{
            $(this).val(0);
        }
    });

    $(document).on('change', "#user-form-resource-type", function(e){

        for (var i = appended_fields.length - 1; i >= 0; i--) {
            var replacement =  appended_fields[i]['container'].replace("field-", "").replace(".", "");
            $('#resources-user-form').yiiActiveForm('remove', replacement);
            // console.log("removing: " + replacement);
            
        }
        appended_fields = [];
        var type = $(this).val();
        $(".user-form-field" ).css("display", "none");
        $(".user-" + type ).toggle();
        $("#resources-user-form input[type=text], input[type=textarea], input[type=file]").each(function() {
            if (this.id != 'surveyId'){
                this.value = null;
            } 
        });
        $("select[id^='resource-type-']").each(function() {
            this.value = type;
        });

        $('.resource-form-table-0').not(':first').remove();
    });

    $(document).on('click', "#add-resource", function(e){

        // FUNCTION THAT DUPLICATES RESOURCES
        e.preventDefault();
        var numItems = parseInt( $('.dataset-tools').length );
        var oldId = numItems - 1;
        var dataset_tools = $(".dataset-tools").last().clone();
        var type = $("#user-form-resource-type").val();
        dataset_tools.find('.form-group').each(function() { 

             //Perform the same replace as above
    

            var th = $(this).find('input[type=text], input[type=textarea], select, input[type=file]');
            if (th.attr('id')){
                // alert(th.attr('id'));
                // var newID = th.attr('id').replace(/-\d+-$/, function(str) { return parseInt(str) + 1; });
                
                var newID = th.attr('id').replace(/(\d+)+/g, function(match, number) {
                       return parseInt(number)+1;
                });
                var newName = th.attr('name').replace(/(\d+)+/g, function(match, number) {
                       return parseInt(number)+1;
                });
                var newClass = th.parent().attr('class').replace(/(\d+)+/g, function(match, number) {
                       return parseInt(number)+1;
                });
                th.attr('id', newID);
                th.attr('name', newName);
                th.parent().attr('class', newClass);
                th.parent().removeClass('has-error has-success');
                th.parent().find('.help-block').text("");

                if(!newID.includes('type') && !newID.includes('allowusers')){
                    th.val(null);
                }

                if(newID.includes('type')){
                    th.val(type);
                }

                if(newID.includes('title') && type != 'image'){
                    // alert("pushing title " + newID);
                    new_fields.push(
                    {
                        id: newID,
                        name: newName.replace('Resources', ''),
                        container: '.' + newClass.replace('form-group ',''),
                        input: '#' + newID,
                        error: '.help-block'
                    });
                }  

                if(newID.includes('image') && type == 'image'){
                    // alert("pushing image" + newID);
                    new_fields.push(
                    {
                        id: newID,
                        name: newName.replace('Resources', ''),
                        container: '.' + newClass.replace('form-group ',''),
                        input: '#' + newID,
                        error: '.help-block'
                    });
                }                   
            }
            
        });


        $(".button-row-2").before(dataset_tools);

        for (var i = 0; i < new_fields.length; i++) {
            if( type != 'image' ){
                var message = 'Title cannot be blank.';
            }else{
                var message = 'Image cannot be blank.';
            }
            // console.log("Adding field: " + new_fields[i]['container'] + "\n");
            $('#resources-user-form').yiiActiveForm('add', {
                id: new_fields[i]['id'],
                name: new_fields[i]['name'],
                container: new_fields[i]['container'],
                input: new_fields[i]['input'],
                error: '.help-block',
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: message});
                }
            });  
            appended_fields.push(new_fields[i]);
            new_fields.shift(); 
            
        }
        return;
        
        
    });

});
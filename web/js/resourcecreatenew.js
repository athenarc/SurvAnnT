$(document).ready(function(){

    $(document).on('change', '.user-resource-select, #user-resource-types', function(){

        $(".resource-before-form").submit();
        
    });

});
$(document).ready(function(){

    $(document).on('change', ".surveys-selection" , function() {
        $(".surveys-statistics-form").submit();
    });

});
$(document).ready(function(){
    
    $(document).on('change', '.user-resource-select, #user-resource-types', function(){
        $('.resource-before-form').yiiActiveForm('remove', 'collection-name');
        $(".resource-before-form").submit();

        // alert($(".resource-before-form").serialize());
    });

    $('button[name="discard-collection"]').click(function(e){
        e.preventDefault();
        $('button[name="discard-collection"]').val("discard");
        $(".resource-before-form").submit();
    });

});
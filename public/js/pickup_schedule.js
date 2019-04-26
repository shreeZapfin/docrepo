//Validation for Form
$("#question").keyup(function(){
    $('#question_span').hide();
});
$("#sequence").keyup(function(){
    $('#sequence_span').hide();
});
$('#customer_id').change(function(){
    if($('#customer_id').val() != -1){
        $('#customer_span').hide();
    }else{

        $('#customer_span').show();
    }
});
$('#product_id').change(function(){
    if($('#product_id').val() != -1){
        $('#product_span').hide();
    }else{
        $('#product_span').show();
    }
});


$('#QuestionForm').on('submit', function(e) {
    if ($('#product_id').val() != '-1' && $('#customer_id').val() != '-1' && $('#question').val() != '' && $('#sequence').val() != '') {
        $('#QuestionForm').submit();
    }else{

        if ($('#product_id').val() == -1) {
            if ($("#product_id").parent().next(".validation").length == 0) // only add if not added
            {
                $('#product_span').show();
            }
            $('#product_id').focus();
            focusSet = true;

        } else {
            $("#product_id").parent().next(".validation").remove(); // remove it
        }
        if ($('#customer_id').val() == -1) {
            if ($("#customer_id").parent().next(".validation").length == 0) // only add if not added
            {
                $('#customer_span').show();
            }
            $('#customer_id').focus();
            focusSet = true;

        } else {
            $("#customer_id").parent().next(".validation").remove(); // remove it
        }
        if (!$('#question').val()) {
            if ($("#question").parent().next(".validation").length == 0) // only add if not added
            {
                $('#question_span').show();
            }
            $('#question').focus();
            focusSet = true;

        } else {
            $("#question").parent().next(".validation").remove(); // remove it
        }
        if (!$('#sequence').val()) {
            if ($("#sequence").parent().next(".validation").length == 0) // only add if not added
            {
                $('#sequence_span').show();
            }
            $('#sequence').focus();
            focusSet = true;

        } else {
            $("#sequence").parent().next(".validation").remove(); // remove it
        }
        e.preventDefault();
    }
});
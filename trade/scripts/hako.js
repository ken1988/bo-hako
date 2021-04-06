$(function(){

$('#new_name').exValidation({
            firstValidate: true,
            rules: {
                name: "chkrequired chkzenkaku"
            },
            stepValidation: true
        });

});

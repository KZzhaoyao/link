define(function(require, exports, module) {   
    require("jquery.bootstrap-datetimepicker");
    var Validator = require('../common/validator');
    exports.run = function() {
        var $form = $('#add-link-form');

                var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }

                // $form.find('[type=submit]').button('loading').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(html) {
                    // 
                    // console.log($form.attr('action'));
//                     return;
                    if (html.status=="false") {
                    window.location.reload();
                    }else{
                    $("#ajax-data").html(html.html);
                    // console.log($form);
                    //     $form.find('[id=field-url]').addClass('disabled');
                    $('#field-url').attr('readonly',true);

                       validator.addItem({
                        element: '[name="title"]',
                        required: true,
                        });

                        validator.addItem({
                        element: '[name="categoryId"]',
                        required: true,
        });
}   
                    
                }).error(function(e){
                    console.log(e);
                    alert('分享链接添加失败');
                });
            }
        });

        validator.addItem({
            element: '[name="url"]',
            required: true,
        });
     

    }
});
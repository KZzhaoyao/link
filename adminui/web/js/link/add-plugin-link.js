define(function(require, exports, module) {   
    require("jquery.bootstrap-datetimepicker");
    var Validator = require('../common/validator');
    exports.run = function() {
        var $form = $('#add-plugin-link-form');
        console.log(111);
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
                        window.location.href="/";
                    }
                       }).error(function(e){
                    console.log(e);
                    alert('分享链接添加失败');
                });
            }
        });

        validator.addItem({
            element: '[name="categoryId"]',
            required: true,
        });
 
        validator.addItem({
            element: '[name="title"]',
            required: true,
        });    

    }
});
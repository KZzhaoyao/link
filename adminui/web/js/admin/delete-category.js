define(function(require, exports, module) {
require("jquery.bootstrap-datetimepicker");
    exports.run = function() {

       $(".modal-delete-btn").on('click',  function() {
            if (!confirm('您真的要删除?')) {
                return ;
            }
            $.post($(this).data('url'), function(){
                window.location.reload();
            });
        });
    };

});
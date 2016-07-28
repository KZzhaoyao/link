define(function(require, exports, module) {
    window.jQuery = window.$ = require('jquery');
    require('bootstrap');
    require('../adminlte/js/app.js');

    seajs.on('fetch', function(data) {
        if (data.uri) {
            if (/\:\/\/.*?\/js\//.test(data.uri)) {
                data.requestUri = data.uri + '?v' + app.asset_version;
            }
        }
    });

    exports.load = function(name) {
        name = '/js/' + name;
        seajs.use(name, function(controller) {
            if (controller && $.isFunction(controller.run)) {
                controller.run();
            }
        });

    };
    
    window.app.load = exports.load;

    if (app.controller) {
        exports.load(app.controller);
    }

    $(document).on('click.modal.data-api', '[data-toggle="modal"]', function(e) {
        var $this = $(this),
            href = $this.attr('href'),
            url = $(this).data('url');
        if (url) {
            var $target = $($this.data('target') || (href && href.replace(/.*(?=#[^\s]+$)/, '')));
            $target.load(url);
        }
    });

    $('.modal').on('click', '[data-toggle=form-submit]', function(e) {
        e.preventDefault();
        $($(this).data('target')).submit();
    });

    $(".modal").on('click', '.pagination a', function(e){
        e.preventDefault();
        var $modal = $(e.delegateTarget);
        $.get($(this).attr('href'), function(html){
            $modal.html(html);
        });
    });


});
(function ($) {
    "use strict";

    $(".wpjb-dropdown").click(function () {
        $(this).toggleClass("is-active");
    });

    $(".wpjb-dropdown ul").click(function (e) {
        e.stopPropagation();
    });
})(jQuery);

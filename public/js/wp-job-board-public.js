(function ($) {
    "use strict";

    // Form Modal

  function showForm() {
    var formOverlay = document.querySelector(".wpjb-form__modal-overlay");
    formOverlay.style.display = "block";
  }

  function closeForm() {
    var formOverlay = document.querySelector(".wpjb-form__modal-overlay");
    formOverlay.style.display = "none";
  }

    // dropdown menu function

    $(".wpjb-dropdown").click(function () {
        $(this).toggleClass("is-active");
    });

    $(".wpjb-dropdown ul").click(function (e) {
        e.stopPropagation();
    });

    // print function

    function printContent() {
        console.log("print btn clicked!");
        const content = $("#wpjb-card").html();
        const printWindow = window.open("", "_blank");
        printWindow.document.open();
        printWindow.document.write(
            "<html><head><title>Print</title></head><body>" +
                content +
                "</body></html>"
        );
        printWindow.document.close();
        printWindow.print();
        printWindow.onafterprint = function () {
            printWindow.close();
        };
    }
    window.closeForm = closeForm;
    window.showForm = showForm;
    window.printContent = printContent;
})(jQuery);

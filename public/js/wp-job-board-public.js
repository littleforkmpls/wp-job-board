(($) => {
    "use strict";

    console.log("wp-job-board-public.js loaded");

    /** ******************* */
    /** MicroModal          */
    /** ******************* */

    const $modalTriggers = $("[data-micromodal-trigger]");

    if ($modalTriggers.length) {
        MicroModal.init({
            onShow: (modal) => console.info(`${modal.id} is shown`),
            onClose: (modal) => console.info(`${modal.id} is hidden`),
            openTrigger: "data-micromodal-trigger",
            closeTrigger: "data-micromodal-close",
            openClass: "wpjb-modal--isOpen",
            disableScroll: true,
            disableFocus: false,
            awaitOpenAnimation: false,
            awaitCloseAnimation: false,
            debugMode: true,
        });
    }

    /** ******************* */
    /** Search Functions    */
    /** ******************* */

    const $clearSearchBtn = $(".wpjb-btn__clearSettings");
    const $searchSubmit = $(".wpjb-search__submit");
    const $searchInput = $("#wpjbSearchTextInput");

    settingsBtnOpacity();

    function settingsBtnOpacity() {
        if ($searchInput.val().trim() !== "") {
            $clearSearchBtn.css("opacity", "1");
        } else {
            $clearSearchBtn.css("opacity", "0");
        }
    }

    function resetSearch() {
        const $url = new URL(window.location.href);
        $url.searchParams.delete("s");
        window.history.replaceState({}, "", $url);
    }

    $searchSubmit.on("click", function () {
        console.log("search submit clicked");
        settingsBtnOpacity();
    });

    $clearSearchBtn.on("click", function () {
        console.log("clear search clicked");
        $searchInput.val("");
        $clearSearchBtn.css("opacity", "0");
        resetSearch();
        window.location.reload();
        //add more clear functionality here
    });

    /** ******************* */
    /** Show Filters        */
    /** ******************* */

    $(".btn__filter").on("click", function () {
        const facetSections = $(".wpjb-facet__section");
        const hiddenSections = facetSections
            .toArray()
            .some(
                (section) =>
                    $(section).css("display") === "none" ||
                    $(section).css("display") === ""
            );
        facetSections.css("display", hiddenSections ? "grid" : "none");
        $(this).text(hiddenSections ? "Filters -" : "Filters +");
    });

    /** ******************* */
    /** Print Job Post      */
    /** ******************* */

    const printButton = $(".wpjb-utilityNav__btn");

    printButton.on("click", function () {
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
    });

    /** ******************* */
    /** Form Label Opacity  */
    /** ******************* */

    $("[id^='wpjb-contact__']").on("input", function () {
        const inputId = $(this).attr("id");
        const labelId = $("label[for='" + inputId + "']").attr("id");
        console.log("input ID:", inputId);
        console.log("Associated label ID:", labelId);
        showLabel(labelId, this);
    });

    function showLabel(labelId, input) {
        const label = $("#" + labelId);
        const inputVal = $(input).val().trim();

        if (inputVal !== "") {
            label.css("opacity", "1");
        } else {
            label.css("opacity", "0");
        }
    }

    /** ******************* */
    /** Upload Resume       */
    /** ******************* */

    const $dragArea = $(".wpjb-drag__fieldset");
    const $dragDropText = $(".wpjb-drag__field-txt");
    let $browseInput = $("#wpjb-contact__resume");
    let $file;
    const $fileErrorSpan = $(".wpjb-drag__file-error");

    // confirm resume is attached on browse option

    $browseInput.on("change", function () {
        console.log("file selected");
        if ($(this).prop("files").length > 0) {
            $dragArea.html = `✓ ${this.files[0].name} attached!`;
        }
    });

    // Drag and drop resume functionality

    $dragArea.on("dragover", (event) => {
        console.log("file dragged over $dragArea");
        event.preventDefault();
        $dragDropText.text("Release to Upload File");
        $dragArea.addClass("active");
    });

    $dragArea.on("dragleave", (event) => {
        console.log("file dragged left $dragArea");
        event.preventDefault();
        $dragDropText.text("Drag & Drop");
        $dragArea.removeClass("active");
    });

    $dragArea.on("drop", (event) => {
        console.log("file dropped");
        event.preventDefault();
        //$file = event.dataTransfer.files[0];
        $file = event.originalEvent.dataTransfer.files[0];
        console.log($file);

        let $fileType = $file.type;
        console.log($fileType);

        let $validExtensions = [
            "application/pdf",
            "application/doc",
            "application/docx",
            "application/txt",
            "application/rtf",
            "application/odt",
            "application/html",
            "application/text",
        ];

        if ($validExtensions.includes($fileType)) {
            console.log("valid file type");
            $fileErrorSpan.css("opacity", "0");
            $dragArea.html(`✓ ${$file.name} attached!`);
        } else {
            console.log("invalid file type");
            $fileErrorSpan.css("opacity", "1");
        }
    });
})(jQuery);

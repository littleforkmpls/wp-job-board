(($) => {
    "use strict";

    /** ******************* */
    /** MicroModal    */
    /** ******************* */

    // const modalOpen = document.querySelector("data-micromodal-trigger");
    // const modalClose = document.querySelector("data-micromodal-close");

    // if (!modalOpen) {
    //     return;
    // }

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

    $(document).on("DOMContentLoaded", function () {
        /** ******************* */
        /** Upload Resume       */
        /** ******************* */

        const dragArea = document.querySelector(".wpjb-drag__fieldset");
        const dragDropText = document.querySelector(".wpjb-drag__field-txt");

        let browseInput = document.getElementById("wpjb-contact__resume");
        let file;

        const fileErrorSpan = document.querySelector(".wpjb-drag__file-error");

        if (!browseInput || !dragDropText || !dragArea || !fileErrorSpan) {
            return;
        }

        // confirm resume is attached on browse option

        browseInput.addEventListener("change", function () {
            console.log("file selected");
            if (this.files.length > 0) {
                dragArea.innerHTML = `✓ ${this.files[0].name} attached!`;
            }
        });

        // Drag and drop resume functionality

        dragArea.addEventListener("dragover", (event) => {
            // console.log("file dragged over dragArea");
            event.preventDefault();
            dragDropText.textContent = "Release to Upload File";
            dragArea.classList.add("active");
        });

        dragArea.addEventListener("dragleave", (event) => {
            // console.log("file dragged left dragArea");
            event.preventDefault();
            dragDropText.textContent = "Drag & Drop";
            dragArea.classList.remove("active");
        });

        dragArea.addEventListener("drop", (event) => {
            console.log("file dropped");
            event.preventDefault();
            file = event.dataTransfer.files[0];
            console.log(file);

            let fileType = file.type;
            console.log(fileType);

            let validExtensions = [
                "application/pdf",
                "application/doc",
                "application/docx",
                "application/txt",
                "application/rtf",
                "application/odt",
                "application/html",
                "application/text",
            ];

            if (validExtensions.includes(fileType)) {
                console.log("valid file type");
                fileErrorSpan.style.opacity = "0";
                dragArea.innerHTML = `✓ ${file.name} attached!`;
            } else {
                console.log("invalid file type");
                fileErrorSpan.style.opacity = "1";
            }
        });

        /** ******************* */
        /** Search Functions    */
        /** ******************* */

        const clearSearch = document.querySelector(".wpjb-btn__clearSettings");
        const searchSubmit = document.querySelector(".wpjb-search__submit");

        if (!clearSearch || !searchSubmit) {
            return;
        }

        searchSubmit.addEventListener("click", function () {
            console.log("search submit clicked");
            clearSearch.style.opacity = "1";
        });

        clearSearch.addEventListener("click", function () {
            console.log("clear search clicked");
            clearSearch.style.opacity = "0";
            //add more clear functionality here
        });

        /** ******************* */
        /** Print Job Post      */
        /** ******************* */

        const printButton = document.querySelector(".wpjb-utilityNav__btn");

        if (printButton) {
            printButton.addEventListener("click", function () {
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
        }

        /** ******************* */
        /** Form Labels         */
        /** ******************* */

        const fieldsets = document.querySelectorAll(".wpjb-fieldset");

        function showLabel(input) {
            const labelId = input.getAttribute("aria-labelledby");
            const label = document.getElementById(labelId);

            if (!label) {
                console.error("Label not found for input:", input);
                return;
            }

            if (input.value.trim() !== "") {
                label.classList.remove("hidden-label");
                label.classList.add("visible-label");
            } else {
                label.classList.remove("visible-label");
                label.classList.add("hidden-label");
            }
        }

        fieldsets.forEach((fieldset) => {
            const input = fieldset.querySelector(".wpjb-field");

            // Initial check on page load
            showLabel(input);

            // Add event listener
            input.addEventListener("input", function () {
                console.log("input changed", this.value);
                showLabel(this);
            });
        });
    });

    /** ******************* */
    /** Form Labels         */
    /** ******************* */

    // function showLabel(labelId, input) {
    //     const label = document.getElementById(labelId);
    //     if (input.value.trim() !== "") {
    //         label.classList.remove("hidden-label");
    //         label.classList.add("visible-label");
    //     } else {
    //         label.classList.remove("visible-label");
    //         label.classList.add("hidden-label");
    //     }
    // }

    /** ******************* */
    /** Show Filters    */
    /** ******************* */

    function toggleFilters() {
        const facetSections = document.querySelectorAll(".wpjb-facet__section");
        const button = document.querySelector(".btn__filter");

        const hiddenSections = Array.from(facetSections).some(
            (section) =>
                section.style.display === "none" || section.style.display === ""
        );
        facetSections.forEach((section) => {
            section.style.display = hiddenSections ? "grid" : "none";
        });
        button.textContent = hiddenSections ? "Filters -" : "Filters +";
    }

    /** ******************* */
    /** Print Job Post      */
    /** ******************* */

    // function printContent() {
    //     console.log("print btn clicked!");
    //     const content = $("#wpjb-card").html();
    //     const printWindow = window.open("", "_blank");
    //     printWindow.document.open();
    //     printWindow.document.write(
    //         "<html><head><title>Print</title></head><body>" +
    //             content +
    //             "</body></html>"
    //     );
    //     printWindow.document.close();
    //     printWindow.print();
    //     printWindow.onafterprint = function () {
    //         printWindow.close();
    //     };
    // }

    // public functions
    window.toggleFilters = toggleFilters;
    // window.showLabel = showLabel;
    // window.printContent = printContent;
})(jQuery);

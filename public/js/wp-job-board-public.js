(($) => {
    'use strict';

    MicroModal.init({
        onShow: modal => console.info(`${modal.id} is shown`),
        onClose: modal => console.info(`${modal.id} is hidden`),
        openTrigger: 'data-micromodal-trigger',
        closeTrigger: 'data-micromodal-close',
        openClass: 'wpjb-modal--isOpen',
        disableScroll: true,
        disableFocus: false,
        awaitOpenAnimation: false,
        awaitCloseAnimation: false,
        debugMode: true
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

    // Resume upload
    $(document).on("DOMContentLoaded", function () {


        const dragArea = document.querySelector(".wpjb-drag__fieldset");
        const dragDropText = document.querySelector(".wpjb-drag__field-text");

        let browseInput = document.getElementById("wpjb-contact__resume");
        let file;

        const fileErrorSpan = document.querySelector(".file-error");

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
            // console.log("file dropped");
            event.preventDefault();
            file = event.dataTransfer.files[0];
            // console.log(file);

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
    });

    // Move input label up when input is filled
    function showLabel(labelId, input) {
        const label = document.getElementById(labelId);
        if (input.value.trim() !== '') {
            label.classList.remove('hidden-label');
            label.classList.add('visible-label');
          } else {
            label.classList.remove('visible-label');
            label.classList.add('hidden-label');
          }
      }

    //Open and close dropdown by clicking anywhere on the details section
    document.getElementById('wpjbFacetSection').addEventListener('click', function () {
        var details = this.querySelector('details');
        details.open = !details.open;
    });

    // public functions
    window.showLabel = showLabel;
    window.printContent = printContent;
})(jQuery);

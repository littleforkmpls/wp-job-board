(function ($) {
    "use strict";

    // Form Modal

    function showForm() {
        var formOverlay = document.querySelector(".wpjb-form__modal-overlay");
        formOverlay.style.display = "flex";
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

    // Resume upload
    $(document).on("DOMContentLoaded", function () {
        const dragArea = document.querySelector(".wpjb-form__resume-drag");
        const dragDropText = document.querySelector(".wpjb-form__resume-title");

        let browse = document.querySelector(".wpjb-form__browse-link");
        let browseInput = document.getElementById("wpjb-form__resume-browse");

        browse.onclick = () => {
            browseInput.click();
        }

        browseInput.addEventListener("change", function () {
            console.log("file selected");
            let fileType = browseInput.files[0].type;
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
                // Display "file uploaded" and the file name
                dragArea.innerHTML = `${browseInput.files[0].name} upload successful!`;
            } else {
                console.log("invalid file type");
                dragArea.innerHTML = `Invalid file type. <button class="wpjb-form__retry-btn" onclick="retryUpload()">Retry</button>`;
            }
        });

        let file;

        // when file is dragged over dragArea
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
                // Display "file uploaded" and the file name
                dragArea.innerHTML = `File uploaded: ${file.name}`;
            } else {
                console.log("invalid file type");
                dragArea.innerHTML = `Invalid file type. <button class="wpjb-form__retry-btn" onclick="retryUpload()">Retry</button>`;
            }
        });

        function retryUpload() {
            // Implement retry logic here
            console.log("Retry button clicked");
            dragArea.innerHTML = `Drag & Drop`;

            // You can add code to reset the upload area or perform other actions.
        }
    });
    // public functions

    window.closeForm = closeForm;
    window.showForm = showForm;
    window.printContent = printContent;
})(jQuery);

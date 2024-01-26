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

    const $clearSettingsBtn = $(".wpjb-btn__clearSettings");
    const $searchInput = $("#wpjbSearchTextInput");
    const $searchForm = $(".wpjb-search__form");

    if ($searchInput.length !== 0) {
        clearSettingsBtnOpacity();

        function clearSettingsBtnOpacity() {
            if ($searchInput.val().trim() !== "") {
                $clearSettingsBtn.addClass("wpjb-btn__clearSettings--visible");
            } else {
                $clearSettingsBtn.removeClass("wpjb-btn__clearSettings--visible");
            }
        }

        function resetSearch() {
            const $url = new URL(window.location.href);
            $url.searchParams.delete("s");
            // window.history.replaceState({}, "", $url);
            filterJobs();
        }

        $searchForm.on("submit", function (e) {
            console.log("search form submit clicked");
            e.preventDefault();
            filterJobs();
            clearSettingsBtnOpacity();
        });

        $clearSettingsBtn.on("click", function () {
            console.log("clear search clicked");
            $searchInput.val("");
            $clearSettingsBtn.removeClass("wpjb-btn__clearSettings--visible");
            resetSearch();
            window.location.href = "/jobs";
            filterJobs();
        });
    }

    /** ******************* */
    /** Show Filters        */
    /** ******************* */

    $(".btn__filter").on("click", function () {
        const $facetSections = $(".wpjb-facet__section");
        const $hiddenSections = $facetSections
            .toArray()
            .some(
                (section) =>
                    $(section).css("display") === "none" ||
                    $(section).css("display") === ""
            );
        $facetSections.css("display", $hiddenSections ? "grid" : "none");
        $(this).html($hiddenSections ? `Filters <span class="btn__filter--minus">-</span>` : `Filters <span class="btn__filter--plus">+</span>`);
    });

    /** ******************* */
    /** Print Job Post      */
    /** ******************* */

    const $printButton = $(".wpjb-utilityNav__btn");

    $printButton.on("click", function () {
        console.log("print btn clicked!");

        const $content = $("#wpjb-card").html();
        const $printWindow = window.open("", "_blank");
        $printWindow.document.open();
        $printWindow.document.write(
            "<html><head><title>Print</title></head><body>" +
                $content +
                "</body></html>"
        );
        $printWindow.document.close();
        $printWindow.print();

        $printWindow.onafterprint = function () {
            $printWindow.close();
        };
    });

    /** ******************* */
    /** Form Label Opacity  */
    /** ******************* */

    $("[id^='wpjb-contact__']").on("input", function () {
        // const currentInputValue = $(this).val();
        // console.log("Current input value:", currentInputValue);

        const $inputId = $(this).attr("id");
        const $labelId = $("label[for='" + $inputId + "']").attr("id");
        // console.log("input ID:", $inputId);
        // console.log("Associated $label ID:", $labelId);
        showLabel($labelId, this);
    });

    function showLabel($labelId, $input) {
        const $label = $("#" + $labelId);
        const $inputVal = $($input).val().trim();

        // console.log("Label element found:", $label.length > 0);
        // console.log("Input value:", $inputVal);

        if ($inputVal !== "") {
            $label.css("opacity", "1");
        } else {
            $label.css("opacity", "0");
        }
    }

    /** ******************* */
    /** Upload Resume       */
    /** ******************* */

    const $dragArea = $(".wpjb-drag__fieldset");
    const $dragDropText = $(".wpjb-drag__field-txt");
    let $browseInput = $("#wpjb-contact__resume");
    let $droppedFile = null;
    //let $droppedFile;
    const $droppedFileErrorSpan = $(".wpjb-drag__file-error");

    // confirm resume is attached on browse option

    $browseInput.on("change", function () {
        console.log("file selected with browse:", this.files[0]);
        if ($(this).prop("files").length > 0) {
            $dragArea.html( `✓ ${this.files[0].name} attached!`);
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
        //$droppedFile = event.dataTransfer.files[0];
        $droppedFile = event.originalEvent.dataTransfer.files[0];
        console.log("droppedFile in drop event:", $droppedFile);

        let $droppedFileType = $droppedFile.type;
        console.log("droppedFileType:", $droppedFileType);

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

        if ($validExtensions.includes($droppedFileType)) {
            console.log("valid file type");
            $droppedFileErrorSpan.css("opacity", "0");
            $dragArea.html(`✓ ${$droppedFile.name} attached!`);
        } else {
            console.log("invalid file type");
            $droppedFileErrorSpan.css("opacity", "1");
        }
    });

    /** ******************* */
    /** Submit Resume       */
    /** ******************* */

    $("#wpjb-form__resume").submit(function (e) {
        e.preventDefault();

        console.log("Submit resume clicked!");
        console.log("$droppedFile in submit:", $droppedFile);

        const $formData = new FormData(this);
        $formData.append("first_name", $("#wpjb-contact__firstName").val());
        $formData.append("last_name", $("#wpjb-contact__lastName").val());
        $formData.append("email", $("#wpjb-contact__email").val());
        $formData.append("phone", $("#wpjb-contact__phone").val());
        $formData.append("job_order_id", $("#job_order_id").val());
        $formData.append("wp_post_id", $("#wp_post_id").val());

        if ($browseInput[0].files.length > 0) {
            $formData.append("resume", $browseInput[0].files[0]);
        }

        if ($droppedFile) {
            $formData.append("resume", $droppedFile);
            //console.log("droppedFile in if statement:", $droppedFile);
        } else if ($browseInput[0].files.length > 0) {
            $formData.append("resume", $browseInput[0].files[0]);
        }

        console.log("droppedFile:", $droppedFile);

        sendResumeData($formData);
    });

    function sendResumeData($formData) {
        $.ajax({
            url: "/wp-json/wp-job-board/v1/submit-resume",
            type: "POST",
            data: $formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log("Success:", data);
                alert("Resume submitted successfully!");
                MicroModal.close("modal-apply");
            },
            error: function (error) {
                console.error("Error on submit resume:", error);
                alert("Error submitting resume. Please try again.");
            },
        });
    }

    /** ******************* */
    /** Filter Pagination   */
    /** ******************* */

    let $currentPage = 1;

    const updatePagination = ($maxNumPages, $currentPage) => {
        const $paginationContainer = $(".wpjb-pagination__filtered");
        $paginationContainer.empty();

        // Previous Link
        if ($currentPage > 1) {
            $paginationContainer.append(
                '<li><a href="#" data-page="' +
                    ($currentPage - 1) +
                    '">Previous</a></li>'
            );
        }

        // First page
        let $firstPage = $('<li><a href="#" data-page="1">1</a></li>');
        if ($currentPage === 1) {
            $firstPage.addClass("current-index");
        }
        $paginationContainer.append($firstPage);

        // Ellipsis and pages logic
        if ($currentPage > 3) {
            $paginationContainer.append("<li><span>...</span></li>");
        }
        for (
            let i = Math.max(2, $currentPage - 1);
            i <= Math.min($currentPage + 1, $maxNumPages - 1);
            i++
        ) {
            let li = $(
                '<li><a href="#" data-page="' + i + '">' + i + "</a></li>"
            );
            if ($currentPage === i) {
                li.addClass("current-index");
            }
            $paginationContainer.append(li);
        }
        if ($currentPage < $maxNumPages - 2) {
            $paginationContainer.append("<li><span>...</span></li>");
        }

        // Last page
        if ($maxNumPages > 1) {
            $paginationContainer.append(
                '<li><a href="#" data-page="' +
                    $maxNumPages +
                    '">' +
                    $maxNumPages +
                    "</a></li>"
            );
        }

        // Next Link
        if ($currentPage < $maxNumPages) {
            $paginationContainer.append(
                '<li><a href="#" data-page="' +
                    ($currentPage + 1) +
                    '">Next</a></li>'
            );
        }
    };

    /** ******************* */
    /** Filter Jobs         */
    /** ******************* */

    const filterJobs = () => {
        console.log("filterJobs fired");
        let industry = [];
        let location = [];
        let type = [];
        let category = [];
        let search = $("#wpjbSearchTextInput").val();

        //add a check for search and filters
        $clearSettingsBtn.addClass("wpjb-btn__clearSettings--visible");

        $(".wpjb-pagination").css("display", "none");

        $('input[name="wjb_bh_job_industry_tax[]"]:checked').each(function () {
            industry.push($(this).val());
        });

        $('input[name="wjb_bh_job_location_tax[]"]:checked').each(function () {
            location.push($(this).val());
        });

        $('input[name="wjb_bh_job_type_tax[]"]:checked').each(function () {
            type.push($(this).val());
        });

        $('input[name="wjb_bh_job_category_tax[]"]:checked').each(function () {
            category.push($(this).val());
        });

        // screen loading classes
        $(".wpjb-card").addClass("loader");
        $(".wpjb-archive").addClass("disabled");

        console.log("$currentPage in Ajax:", $currentPage);

        $.ajax({
            url: wpjb_ajax.ajax_url,
            type: "POST",
            data: {
                action: "filter_jobs",
                industry: industry,
                location: location,
                type: type,
                category: category,
                search: search,
                page: $currentPage,
            },

            success: function (res) {
                console.log("response data is:", res);
                if (res && res.data && res.data.html !== undefined) {
                    //timeout for testing purposes
                    setTimeout(function () {
                        $(".wpjb-card").removeClass("loader");
                        $(".wpjb-archive").removeClass("disabled");
                        $(".wpjb-results__bd").html(res.data.html);

                        if (
                            res.data &&
                            res.data.max_num_pages &&
                            res.data.current_page
                        ) {
                            updatePagination(
                                res.data.max_num_pages,
                                res.data.current_page
                            );
                        }

                        if (res.data.count !== undefined) {
                            $(".wpjb-results__title, .wpjb-results__title--small").text(
                                res.data.count + " Open Positions"
                            );
                        }
                    }, 700);
                } else {
                    $(".wpjb-results__bd").html(
                        "<p>No jobs found or error loading jobs.</p>"
                    );
                }
            },
        });
    };

    $(".wpjb-facet__section__list").on(
        "click",
        'input[type="checkbox"]',
        function () {
            filterJobs();
        }
    );

    $(".wpjb-pagination__filtered").on("click", "a", function (e) {
        e.preventDefault();
        const page = $(this).data("page");
        $currentPage = page;
        filterJobs();
    });

    /** ********************** */
    /** Store filtered results */
    /** ********************** */

    // loadFilters();

    // function saveFilteredResults() {
    //     console.log("saving filtered results");
    //     const $filteredResults = {
    //     search: $("#wpjbSearchTextInput").val(),
    //     industry: $('input[name="wjb_bh_job_industry_tax[]"]:checked').map(function() { return this.value; }).get(),
    //     location: $('input[name="wjb_bh_job_location_tax[]"]:checked').map(function() { return this.value; }).get(),
    //     type: $('input[name="wjb_bh_job_type_tax[]"]:checked').map(function() { return this.value; }).get(),
    //     category: $('input[name="wjb_bh_job_category_tax[]"]:checked').map(function() { return this.value; }).get(),
    //     };
    //     console.log("filtered results:", $filteredResults);
    //     localStorage.setItem("filteredResults", JSON.stringify($filteredResults));
    // }

    // $('input[type="checkbox"], input[type="text"]').on('change', function() {
    //     saveFilteredResults();
    // });

    // function applyCheckboxFilters(filterName, values) {
    //     if (values && values.length > 0) {
    //         values.forEach((value) => {
    //             $(
    //                 'input[name="wjb_bh_job_' +
    //                     filterName +
    //                     '_tax[]"][value="' +
    //                     value +
    //                     '"]'
    //             ).prop("checked", true);
    //         });
    //     }
    // }

    // function loadFilters() {
    //     console.log("loading filters fired");
    // if (localStorage.getItem("filteredResults")) {
    //     const $savedResults = JSON.parse(localStorage.getItem("filteredResults"));
    //     console.log("saved results:", $savedResults);

    //     $("#wpjbSearchTextInput").val($savedResults.search);
    //     applyCheckboxFilters("industry", $savedResults.industry);
    //     applyCheckboxFilters("location", $savedResults.location);
    //     applyCheckboxFilters("type", $savedResults.type);
    //     applyCheckboxFilters("category", $savedResults.category);

    //     if ($savedResults) {
    //         console.log("filtering jobs");
    //         filterJobs();
    //     }
    // }
    // }

    // //save filtered results all the time?
    // window.onbeforeunload = function() {
    //     saveFilteredResults();
    // };

    // $('.wpjb-back').on('click', function() {
    //     goBackToJobs();
    // });

    // // Rest of your code...

    // function goBackToJobs() {
    //     if (localStorage.getItem("filteredResults")) {
    //         window.location.href = '/jobs/';
    //     } else {
    //         // Fallback if no filters are saved
    //         window.location.href = 'javascript:history.back();';
    //     }
    // }

    //clear local storage??
})(jQuery);

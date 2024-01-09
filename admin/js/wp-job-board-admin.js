(($) => {
    'use strict';

    const ajaxTriggers  = document.documentElement.querySelectorAll('[data-wpjb-ajax]');
    const messageNode   = document.documentElement.querySelector('[data-wpjba-message-node="true"]');

    if (ajaxTriggers && messageNode) {
        ajaxTriggers.forEach((element, index) => {

            // determine the ajax action & options
            let ajaxAction = element.getAttribute('data-wpjb-ajax');
            let ajaxOptions = element.getAttribute('data-wpjb-ajax-options');
            let ajaxForm = element.getAttribute('data-wpjb-ajax-form')

            // setup data to be sent with ajax request
            let ajaxDataObject = {
                url: ajaxurl,
                data: {
                    action: ajaxAction
                },
                method: 'POST',
            }

            // if options are present
            if (ajaxOptions && ajaxOptions !== '') {

                // split options to array
                let ajaxOptionsArray = ajaxOptions.split(',');

                // loop array and assign to data object
                ajaxOptionsArray.forEach((element) => {
                    let elementKey = element.split(':')[0].trim();
                    let elementValue = element.split(':')[1].trim();
                    ajaxDataObject[elementKey] = elementValue;
                });
            }

            // listen for click
            element.addEventListener('click', (event) => {

                // prevent element from doing any normal click behaviors
                event.preventDefault();

                // if the button was pressed and had previously shown a notice then hide it
                messageNode.innerHTML = '';

                // reveal spinner next to button so the user knows something is happening
                if (element.nextElementSibling.classList.contains('spinner')) {
                    element.nextElementSibling.classList.add('is-active');
                }

                // disable the button from being pressed again while the sync is running
                element.setAttribute('disabled', true);

                // Special handling for form submissions
                if (ajaxForm) {
                    const form = document.getElementById(ajaxForm);
                    const data = new FormData(form);
                    data.append('action', ajaxAction);
                    ajaxDataObject.data = data;
                    ajaxDataObject.processData = false;
                    ajaxDataObject.contentType = false;
                }

                // POST an ajax call to fire the sync
                $.ajax(
                    ajaxDataObject,
                ).then((response) => {
                    let messageClass = 'notice-success';

                    if (!response.success) {
                        messageClass = 'notice-error';
                    }

                    if (response.data?.message) {
                        messageNode.innerHTML = `
                                <div class="notice ${messageClass}">
                                    <p>${response.data.message}</p>
                                </div>
                            `;
                    }
                }).fail((response) => {
                    messageNode.innerHTML = `
                        <div class="notice notice-error">
                            <p>An error occured. Please try again.</p>
                        </div>
                    `;
                }).always((response) => {

                    // re-enable the button after ajax is complete
                    element.removeAttribute('disabled');

                    // hide spinner after ajax is complete
                    if (element.nextElementSibling.classList.contains('spinner')) {
                        element.nextElementSibling.classList.remove('is-active');
                    }

                })
            });
        });
    }

})(jQuery);

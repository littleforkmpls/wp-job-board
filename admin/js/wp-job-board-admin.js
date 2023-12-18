(($) => {
    'use strict';

    const forceSyncTriggers  = document.documentElement.querySelectorAll('[data-wpjb-sync="true"]');
    const messageNode   = document.documentElement.querySelector('[data-wpjba-message-node="true"]');

    if (forceSyncTriggers && messageNode) {
        forceSyncTriggers.forEach((element, index) => {
            element.addEventListener('click', (event) => {

                // prevent element from doing any normal click behaviors
                event.preventDefault();

                // determine if this is a forced reset operation
                let isForce = (element.getAttribute('data-wpjb-sync-force') == 'true') ? true : false;

                // if the button was pressed and had previously shown a notice then hide it
                messageNode.innerHTML = '';

                // reveal spinner next to button so the user knows something is happening
                if (element.nextElementSibling.classList.contains('spinner')) {
                    element.nextElementSibling.classList.add('is-active');
                }

                // disable the button from being pressed again while the sync is running
                element.setAttribute('disabled', true);

                // POST an ajax call to fire the sync
                $.post(
                    ajaxurl,
                    {
                        action: 'trigger_sync',
                        force: isForce,
                    },
                    (response) => {
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
                    }
                ).fail((response) => {
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

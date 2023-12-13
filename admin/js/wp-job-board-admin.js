(($) => {
    'use strict';

    $(() => {
        $('#wp_job_board_trigger_sync').click((event) => {
            event.preventDefault();

            $('.wp_job_board_sync_error').remove();

            let $_spinner = $('.spinner').addClass('is-active');

            $.post(
                ajaxurl,
                {
                    action: 'trigger_sync',
                },
                (response) => {
                    let messageClass = 'notice-success';
                    if (!response.success) {
                        //TODO do we want to do something other than just message here?
                        messageClass = 'notice-error';
                    }
                    if (response.data?.message) {
                        let $messageBlock = $(`<div id="setting-error-settings_updated" class="notice ${messageClass} settings-error is-dismissible">
<p><strong>${response.data.message}</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>`);
                        $('#wp_job_board_admin').before($messageBlock);
                        $messageBlock.find('.notice-dismiss').on('click', (event)=> {
                            event.preventDefault();
                            $messageBlock.fadeTo(100, 0, () => {
                                $messageBlock.slideUp(100, () => {
                                    $messageBlock.remove();
                                })
                            })
                        });
                    }
                }
            ).fail((response) => {
                //TODO do something if we fail?
            }).always((response) => {
                $_spinner.removeClass('is-active');
            })
        });
    })
})(jQuery);

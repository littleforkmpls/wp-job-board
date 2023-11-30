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
                    if (!response.success) {
                        //TODO do we want to do something other than just message here?
                    }
                    if (response.data?.message) {
                        $_spinner.after('<span class="wp_job_board_sync_error">' + response.data.message + '</span>');
                    }
                }
            ).fail((response) => {
                //TODO do something if we fail?
            }).always((response) => {
                $_spinner.removeClass('is-active');
                $.get(
                    ajaxurl,
                    {
                        action: 'refresh_log'
                    },
                    (response) => {
                        $('#wp_job_board_activity_log').html(response);
                    }
                )
            })
        });
    })
})(jQuery);

<?php
$post_meta_single = get_post_meta(get_the_ID(), 'wp_job_board_bh_data', true);
$post_meta_single_decode = json_decode($post_meta_single);
$post_publish_date = $post_meta_single_decode->dateLastPublished;
$post_modified_date = $post_meta_single_decode->dateLastModified;
$post_employment_type = $post_meta_single_decode->employmentType;
$post_city_name = $post_meta_single_decode->address->city;
$post_state_name = $post_meta_single_decode->address->state;
$post_job_description = $post_meta_single_decode->publicDescription;

// Convert publish date to milliseconds
$post_modified_date = floor($post_modified_date / 1000);

//DateTime objects for the post modified date and current time
$modifiedDateTime = new DateTime("@$post_modified_date");
$currentDateTime = new DateTime();

// Calculate the difference with the DateTime::diff method :: is for static methods
$interval = $modifiedDateTime->diff($currentDateTime);

// Access the components of the DateInterval
$daysDifference = $interval->days;
$hoursDifference = $interval->h;
$minutesDifference = $interval->i;
$secondsDifference = $interval->s;

//variable for formatted time difference
$formattedDifference = '';

// Check the time difference and format accordingly
if ($daysDifference > 0) {
    $formattedDifference = ($daysDifference == 1) ? '1 day' : "$daysDifference days";
} elseif ($hoursDifference > 0) {
    $formattedDifference = ($hoursDifference == 1) ? '1 hour' : "$hoursDifference hours";
} elseif ($minutesDifference > 0) {
    $formattedDifference = ($minutesDifference == 1) ? '1 minute' : "$minutesDifference minutes";
} else {
    $formattedDifference = ($secondsDifference == 1) ? '1 second' : "$secondsDifference seconds";
}

// style="opacity: 0; position: absolute; z-index: -1;"
?>

<div id="wpjb">
    <div class="wpjb-btn__container">
        <button class="wpjb-btn__back">
            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-arrow-left.svg'); ?>
            <span>All Jobs</span>
        </button>
    </div>
    <div class="wpjb-card" id="wpjb-card">
        <div class="wpjb-card__hd">
            <h1><?php the_title(); ?></h1>
        </div>
        <div class="wpjb-card__meta">
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-calendar-days.svg'); ?>
                <span> Posted <?php echo date('m.d.y', (int) $post_publish_date); ?></span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-clock.svg'); ?>
                <span style="font-weight: 500;">Updated <?php echo $formattedDifference; ?> ago</span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-location-dot.svg'); ?>
                <span><?php echo $post_city_name; ?>, <?php echo $post_state_name; ?></span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-briefcase-blank.svg'); ?>
                <span> <?php echo $post_employment_type; ?></span>
            </span>
        </div>
        <div class="wbjb-card__sub-hd">
            <h3>About the job</h3>
        </div>
        <div class="wpjb-card__bd">
            <p class="txt txt-balance"><?php echo $post_job_description; ?></p>
        </div>
        <div class="wpjb-card__ft">
            <div class="wpjb-utilityNav">
                <button class="wpjb-utilityNav__btn">
                    <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-envelope.svg'); ?>
                </button>
                <button class="wpjb-utilityNav__btn" onclick="printContent()">
                    <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-printer.svg'); ?>
                </button>
            </div>
        </div>
        <div class="wpjb-btn__container">
            <button data-micromodal-trigger="modal-apply" class="btn">Apply</button>
        </div>
    </div>


    <div class="wpjb-modal" id="modal-apply" aria-hidden="true">
        <div class="wpjb-modal__overlay" tabindex="-1" data-micromodal-close></div>
        <div class="wpjb-modal__container" role="dialog" aria-modal="true" aria-labelledby="wpjb-modalTitle">
            <div class="wpjb-modal__container__close">
                <button class="wpjb-modal__container__close__btn" type="button" aria-label="Close modal" data-micromodal-close></button>
            </div>
            <div class="wpjb-modal__container__content">
                <div class="wpjb-form">
                    <header class="wpjb-form__hd">
                        <h2 class="wpjb-form__title" id="wpjb-modalTitle">
                            <?php the_title(); ?>
                        </h2>
                    </header>
                    <span class="txt-xxs txt-left"><?php echo $post_city_name; ?>, <?php echo $post_state_name; ?> | <?php echo $post_employment_type; ?></span>
                    <form>
                        <main class="wpjb-form__bd" id="modal-1-content">
                            <div class="wpjb-fieldset">
                                <label id="firstNameLabel" for="wpjb-contact__firstName" aria-label="First Name" class="label-txt-left hidden-label">First Name</label>
                                <input
                                    type="text"
                                    id="wpjb-contact__firstName"
                                    class="wpjb-field"
                                    placeholder="First Name"
                                    oninput="showLabel('firstNameLabel', this)"
                                    required
                                    inputmode="text"
                                    autocomplete="on"
                                    autocapitalize="off"
                                    autocorrect="off"
                                    spellcheck="false"
                                />
                            </div>
                            <div class="wpjb-fieldset">
                                <label id="lastNameLabel" for="wpjb-contact__lastName" aria-label="Last Name" class="label-txt-left hidden-label">Last Name</label>
                                <input
                                    type="text"
                                    id="wpjb-contact__lastName"
                                    class="wpjb-field"
                                    placeholder="Last Name"
                                    oninput="showLabel('lastNameLabel', this)"
                                    required
                                    inputmode="text"
                                    autocomplete="on"
                                    autocapitalize="off"
                                    autocorrect="off"
                                    spellcheck="false"
                                />
                            </div>
                            <div class="wpjb-fieldset">
                                <label id="emailLabel" for="wpjb-contact__email" aria-label="Email" class="label-txt-left hidden-label">Email</label>
                                <input
                                    type="email"
                                    id="wpjb-contact__email"
                                    class="wpjb-field"
                                    placeholder="Email"
                                    oninput="showLabel('emailLabel', this)"
                                    required
                                    inputmode="email"
                                    autocomplete="on"
                                    autocapitalize="off"
                                    autocorrect="off"
                                    spellcheck="false"
                                />
                            </div>
                            <div class="wpjb-fieldset">
                                <label id="phoneLabel" for="wpjb-contact__phone" aria-label="Mobile Phone" class="label-txt-left hidden-label">Mobile Phone</label>
                                <input
                                    type="tel"
                                    id="wpjb-contact__phone"
                                    class="wpjb-field"
                                    placeholder="Mobile Phone"
                                    oninput="showLabel('phoneLabel', this)"
                                    required
                                    inputmode="tel"
                                    autocomplete="on"
                                />
                            </div>

                            <div class="wpjb-drag">
                                <span class="label-txt-left">Upload your resume</span>
                                <div class="wpjb-drag__fieldset">
                                    <span>
                                        <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-file-arrow-up.svg'); ?>
                                    </span>
                                    <span class="wpjb-drag__field-txt">Drag & Drop</span>
                                    <span>or</span>
                                    <div class="browse-label">
                                        <label for="wpjb-contact__resume" aria-label="Resume Upload">
                                            <span class="wpjb-drag__browse-btn">
                                                browse
                                            </span>
                                            <input
                                                type="file"
                                                id="wpjb-contact__resume"
                                                required accept=".html, .text, .txt, .pdf, .doc, .docx, .rft, .odt"
                                                aria-describedby="file-upload-instructions"
                                                style="opacity: 0; position: absolute; z-index: -1;"
                                            />
                                        </label>
                                    </div>
                                    <span class="file-error">Invalid file type, please try again!</span>
                                    <span class="txt-xxxs">Supported file types: html,text,txt,pdf,doc,docx,rtf,odt </span>
                                </div>
                            </div>
                        </main>
                        <footer class="wpjb-form__ft">
                            <input type="submit" class="btn btn__submit" value="Submit" />
                        </footer>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

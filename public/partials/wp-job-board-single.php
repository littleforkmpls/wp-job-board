<?php

$post_id  = get_the_ID();
$job_meta = get_job_meta($post_id);

$job_title              = $job_meta->title;
$job_description        = $job_meta->publicDescription;
$job_employment_type    = $job_meta->employmentType;
$job_location_city      = $job_meta->address->city;
$job_location_state     = $job_meta->address->state;
$job_date_published     = get_formatted_date($job_meta->dateLastPublished);
$job_date_modified      = get_relative_date($job_meta->dateLastModified);

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
            <h1 class="wpjb-card__title">
                <?php echo $job_title; ?>
            </h1>
        </div>
        <div class="wpjb-card__meta">
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-calendar-days.svg'); ?>
                <span>Posted <?php echo $job_date_published; ?></span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-clock.svg'); ?>
                <span style="font-weight: 500;">Updated <?php echo $job_date_modified; ?></span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-location-dot.svg'); ?>
                <span><?php echo $job_location_city; ?>, <?php echo $job_location_state; ?></span>
            </span>
            <span class="wpjb-card__meta-item">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-briefcase-blank.svg'); ?>
                <span> <?php echo $job_employment_type; ?></span>
            </span>
        </div>
        <div>
            <h3 class="wpjb-card__sub-title">About the job</h3>
        </div>
        <div class="wpjb-card__bd">
            <p class="wpjb-card__description"><?php echo $job_description; ?></p>
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
                    <span class="wpjb-form__meta-data"><?php echo $job_location_city; ?>, <?php echo $job_location_state; ?> | <?php echo $job_employment_type; ?></span>
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
                                <span class="wpjb-drag__title">Upload your resume</span>
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
                                    <span class="wpjb-drag__file-error">Invalid file type, please try again!</span>
                                    <span class="wpjb-drag__file-type">Supported file types: html,text,txt,pdf,doc,docx,rtf,odt </span>
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

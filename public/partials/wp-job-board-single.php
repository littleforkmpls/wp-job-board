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
?>

<div id="wpjb">
  <div class="wpjb-btn__container">
    <button class="wpjb-btn__back">
      <svg xmlns="http://www.w3.org/2000/svg" height="14" width="12" viewBox="0 0 448 512">
        <path opacity="1" fill="currentcolor" d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
      </svg>
      <span>All Jobs</span>
    </button>
  </div>
  <div class="wpjb-card" id="wpjb-card">
    <div class="wpjb-card__hd">
      <h1><?php the_title(); ?></h1>
    </div>
    <div class="wpjb-card__meta">
      <span class="wpjb-card__meta-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="16" viewBox="0 0 448 512">
          <path opacity="1" fill="currentcolor" d="M128 0c17.7 0 32 14.3 32 32V64H288V32c0-17.7 14.3-32 32-32s32 14.3 32 32V64h48c26.5 0 48 21.5 48 48v48H0V112C0 85.5 21.5 64 48 64H96V32c0-17.7 14.3-32 32-32zM0 192H448V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V192zm64 80v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm128 0v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H208c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H336zM64 400v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H208zm112 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H336c-8.8 0-16 7.2-16 16z" />
        </svg>
        <span> Posted <?php echo date('m.d.y', (int) $post_publish_date); ?></span>
      </span>
      <span class="wpjb-card__meta-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="16" viewBox="0 0 512 512">
          <path opacity="1" fill="currentcolor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
        </svg>
        <span style="font-weight: 500;">Updated <?php echo $formattedDifference; ?> ago</span>
      </span>
      <span class="wpjb-card__meta-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="16" viewBox="0 0 384 512">
          <path opacity="1" fill="currentcolor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
        </svg>
        <span><?php echo $post_city_name; ?>, <?php echo $post_state_name; ?></span>
      </span>
      <span class="wpjb-card__meta-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="16" viewBox="0 0 448 512">
          <path opacity="1" fill="currentcolor" d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zM337 209L209 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L303 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
        </svg>
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
          <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512">
            <path opacity="1" fill="currentcolor" d="M352 224c53 0 96-43 96-96s-43-96-96-96s-96 43-96 96c0 4 .2 8 .7 11.9l-94.1 47C145.4 170.2 121.9 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.9 0 49.4-10.2 66.6-26.9l94.1 47c-.5 3.9-.7 7.8-.7 11.9c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-25.9 0-49.4 10.2-66.6 26.9l-94.1-47c.5-3.9 .7-7.8 .7-11.9s-.2-8-.7-11.9l94.1-47C302.6 213.8 326.1 224 352 224z" />
          </svg>
        </button>
        <button class="wpjb-utilityNav__btn" onclick="printContent()">
          <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512">
            <path fill="currentcolor" d="M128 0C92.7 0 64 28.7 64 64v96h64V64H354.7L384 93.3V160h64V93.3c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0H128zM384 352v32 64H128V384 368 352H384zm64 32h32c17.7 0 32-14.3 32-32V256c0-35.3-28.7-64-64-64H64c-35.3 0-64 28.7-64 64v96c0 17.7 14.3 32 32 32H64v64c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V384zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
          </svg>
        </button>
        <button class="wpjb-utilityNav__btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="16" width="12" viewBox="0 0 384 512">
            <path opacity="1" fill="currentcolor" d="M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z" />
          </svg>
        </button>
      </div>

    </div>
    <div class="wpjb-btn__container">
      <button data-micromodal-trigger="modal-1">Apply</button>
    </div>
  </div>

  <!-- <a href="#" data-micromodal-trigger="modal-1">Apply</a> -->
  <div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
        <header class="modal__header">
          <h1 class="modal__title" id="modal-1-title">
            <?php the_title(); ?>
          </h1>
          <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <span class="txt-xxs txt-left"><?php echo $post_city_name; ?>, <?php echo $post_state_name; ?> | <?php echo $post_employment_type; ?></span>
        <main class="modal__content" id="modal-1-content">
          <div class="wpjb-fieldset">
            <label id="firstNameLabel" for="wpjb-contact__firstName" aria-label="First Name" class="label-txt-left hidden-label">First Name</label>
            <input type="text" id="wpjb-contact__firstName" class="wpjb-field" placeholder="First Name" oninput="showLabel('firstNameLabel', this)" value required inputmode="text" autocomplete="on" autocapitalize="off" autocorrect="off" spellcheck="false" />
          </div>
          <div class="wpjb-fieldset">
            <label id="lastNameLabel" for="wpjb-contact__lastName" aria-label="Last Name" class="label-txt-left hidden-label">Last Name</label>
            <input type="text" id="wpjb-contact__lastName" class="wpjb-field" placeholder="Last Name" oninput="showLabel('lastNameLabel')" value required inputmode="text" autocomplete="on" autocapitalize="off" autocorrect="off" spellcheck="false" />
          </div>
          <div class="wpjb-fieldset">
            <label id="emailLabel" for="wpjb-contact__email" aria-label="Email" class="label-txt-left hidden-label">Email</label>
            <input type="email" id="wpjb-contact__email" class="wpjb-field" placeholder="Email" oninput="showLabel('emailLabel')" value required inputmode="email" autocomplete="on" autocapitalize="off" autocorrect="off" spellcheck="false" />
          </div>
          <div class="wpjb-fieldset">
            <label  id="phoneLabel" for="wpjb-contact__phone" aria-label="Mobile Phone" class="label-txt-left hidden-label">Mobile Phone</label>
            <input type="tel" id="wpjb-contact__phone" class="wpjb-field" placeholder="Mobile Phone" oninput="showLabel('phoneLabel')" required required inputmode="tel" autocomplete="on"/>
          </div>

          <div class="wpjb-fieldset__dragAndDrop">
            <span class="label-txt-left">Upload your resume</span>
            <div class="wpjb-form__resume-drag">
              <span class="wpjb-form__resume-icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" width="20" viewBox="0 0 384 512">
                  <path opacity="1" fill="currentcolor" d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM216 408c0 13.3-10.7 24-24 24s-24-10.7-24-24V305.9l-31 31c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l72-72c9.4-9.4 24.6-9.4 33.9 0l72 72c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-31-31V408z" />
                </svg>
              </span>
              <span class="wpjb-form__resume-title">Drag & Drop</span>
              <span class="wpjb-form__resume-description">or</span>
                <div class="browse-label">
                <label  for="wpjb-form__resume-browse" aria-label="Resume Upload">
                  <span class="wpjb-form__browse-link">
                    browse
                  </span>
                  <input 
                    type="file" 
                    id="wpjb-form__resume-browse"  
                    required 
                    accept=".html, .text, .txt, .pdf, .doc, .docx, .rft, .odt"
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
        <footer class="modal__footer">
          <button class="modal__btn modal__btn-primary">Continue</button>
          <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
        </footer>
      </div>
    </div>
  </div>


</div>
</div>
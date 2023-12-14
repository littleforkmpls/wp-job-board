<h1>WPJB Plugin Single</h1>
<h2><?php the_title(); ?></h2>

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
  // Decode HTML entities in job description (not working) 
  $decoded_job_description = html_entity_decode($post_job_description);

?>


<div>Employment Type: <?php echo $post_employment_type; ?></div>
<div>Publish Date: <?php echo $post_publish_date; ?></div>
<div>Modified Date: <?php echo $post_modified_date; ?></div>
<div>City: <?php echo $post_city_name; ?></div>
<div>State: <?php echo $post_state_name; ?></div>
<div>Time since modified:<?php echo $formattedDifference;?></div>
<div>Job Description:</div>


<!-- <div class="wpjb-card__btn-container">
      <button class="wpjb-card__back-btn"><a href="./index.html">˂ Back</a></button>
    </div> -->
    <div class="wpjb-card">
      <div class="wpjb-card__hd">
        <h2 class="txt txt--h3"><?php the_title(); ?></h2>
      </div>
      <div class="wpjb-card__meta">
        <p>⚖ Posted <?php echo date('m.d.y', (int) $post_publish_date); ?></p>
        <p>⏲ Updated <?php echo $formattedDifference;?> ago</p>
        <p>📍 <?php echo $post_city_name; ?>, <?php echo $post_state_name; ?></p>
        <p>☑️ <?php echo $post_employment_type; ?></p>
      </div>
      <!-- <div class="wpjb-card__btn-container">
        <button class="wpjb-card__apply-btn">Apply</button>
      </div> -->
      <div class="wpjb-card__bd">
        <p class="txt"><?php echo $decoded_job_description; ?></p>
      </div>
      <div class="wpjb-card__ft">
        <div class="wpjb-utilityNav">
          <button class="wpjb-utilityNav__btn">📩</button>
          <button class="wpjb-utilityNav__btn">⎙</button>
          <button class="wpjb-utilityNav__btn">🔖</button>
        </div>
      </div>
    </div>
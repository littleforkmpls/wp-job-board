<!-- <h1>WPJB Plugin Single</h1>
<h2><?php the_title(); ?></h2> -->

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
  //$decoded_job_description = html_entity_decode($post_job_description);

  // $post_job_description = htmlentities($post_job_description, null, 'utf-8'); 
  // $post_job_description = str_replace(" ", "", $post_job_description);
  // $post_job_description = preg_replace('/[\s]+/mu', '', $post_job_description);
  // $post_job_description = str_replace('\u00a0','',$post_job_description);
  //  echo $post_job_description;

  // Replace UTF-8 encoded non-breaking space with a regular space
  //$cleaned_job_description = preg_replace('/\xc2\xa0/', ' ', $post_job_description);



  

$cleaned_job_description = str_replace('u00a0', ' ', $post_job_description);





// Remove the unwanted "n" characters
//$cleaned_job_description = str_replace("\n", '', $cleaned_job_description);

// Remove the unwanted "n" characters and "nu" elements with various representations of newline characters
//$cleaned_job_description = preg_replace('/\b(?:n|nu|\n)\b/', '', $cleaned_job_description);

// $cleaned_job_description = str_replace('nnttt', "\n", $cleaned_job_description);

// $cleaned_job_description = str_replace('ntttt', "\n", $cleaned_job_description);

// $cleaned_job_description = str_replace('nttt', "\n", $cleaned_job_description);

// Remove the unwanted elements
// $cleaned_job_description = str_replace('ntnttnttntn', '', $cleaned_job_description);
// $cleaned_job_description = ltrim($cleaned_job_description, 'n');
// $cleaned_job_description = preg_replace('/^ntnttnttntn/', '', $cleaned_job_description);

// $cleaned_job_description = rtrim($cleaned_job_description, 'n');

// Remove the unwanted elements
//$cleaned_job_description = preg_replace('/\bntnttnttntn\b/', '', $cleaned_job_description);

 
// Decode HTML entities
$decoded_job_description = html_entity_decode($cleaned_job_description);
?>


<!-- <div class="wpjb-card__btn-container">
      <button class="wpjb-card__back-btn"><a href="./index.html">˂ Back</a></button>
    </div> -->
    <div class="wpjb-card">
      <div class="wpjb-card__hd">
        <h2 class="txt txt--h3"><?php the_title(); ?></h2>
      </div>
      <div class="wpjb-card__meta">
        <p class="txt txt--xs">⚖ Posted <?php echo date('m.d.y', (int) $post_publish_date); ?></p>
        <p class="txt txt--xs">⏲ Updated <?php echo $formattedDifference;?> ago</p>
        <p class="txt txt--xs">📍 <?php echo $post_city_name; ?>, <?php echo $post_state_name; ?></p>
        <p class="txt txt--xs">☑️ <?php echo $post_employment_type; ?></p>
      </div>
      <div class="wpjb-card__bd">
        <p class="txt txt--balance"><?php echo $decoded_job_description; ?></p>
      </div>
      <div class="wpjb-card__ft">
        <div class="wpjb-utilityNav">
          <button class="wpjb-utilityNav__btn">📩</button>
          <button class="wpjb-utilityNav__btn">⎙</button>
          <button class="wpjb-utilityNav__btn">🔖</button>
        </div>
      </div>
    </div>
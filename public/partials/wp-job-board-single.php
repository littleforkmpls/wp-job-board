<h1>WPJB Plugin Single</h1>
<h2><?php the_title(); ?></h2>

<?php
    $post_meta_single = get_post_meta(get_the_ID(), 'wp_job_board_bh_data', true);
    $post_meta_single_decode = json_decode($post_meta_single);

    $post_publish_date = $post_meta_single_decode->dateLastPublished;
    $post_employment_type = $post_meta_single_decode->employmentType;
?>

<div>Employment Type: <?php echo $post_employment_type; ?></div>
<div>Publish Date: <?php echo $post_publish_date; ?></div>



<div class="wpjb-fullCard__btn-container">
      <button class="wpjb-fullCard__back-btn"><a href="./index.html">˂ Back</a></button>
    </div>
    <div class="wpjb-fullCard">
      <div class="wpjb-fullCard__hd">
        <h2 class="txt txt--h3">Boulder Mover</h2>
        <div>
          <button class="wpjb-fullCard__utility-btn">📩</button>
          <button class="wpjb-fullCard__utility-btn">⎙</button>
          <button class="wpjb-fullCard__utility-btn">🔖</button>
        </div>
      </div>
      <div class="wpjb-fullCard__job-info">
        <p>⚖ Posted Nov 11th 2023</p>
        <p>⏲ Updated 42 minutes ago</p>
        <p>📍 Milwaukee, WI</p>
        <p>☑️ Fulltime</p>
      </div>
      <div class="wpjb-fullCard__btn-container">
        <button class="wpjb-fullCard__apply-btn">Apply</button>
      </div>
      <div class="wpjb-fullCard__bd">
        <p class="txt">
          Pick stuff up and put it in another location. After some time has
          passed move it to another location. Repeat. Pick stuff up and put it
          in another location. After some time has passed move it to another
          location. Repeat. Pick stuff up and put it in another location. After
          some time has passed move it to another location. Repeat.Pick stuff up
          and put it in another location. After some time has passed move it to
          another location. Repeat. Pick stuff up and put it in another
          location. After some time has passed move it to another location.
          Repeat. Pick stuff up and put it in another location. After some time
          has passed move it to another location. Repeat. Pick stuff up and put
          it in another location. After some time has passed move it to another
          location. Repeat. Pick stuff up and put it in another location. After
          some time has passed move it to another location. Repeat. Pick stuff
          up and put it in another location. After some time has passed move it
          to another location. Repeat.
        </p>
      </div>
      <div class="wpjb-fullCard__btn-container">
        <button class="wpjb-fullCard__apply-btn">Apply</button>
      </div>
    </div>
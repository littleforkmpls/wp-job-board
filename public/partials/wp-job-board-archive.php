<h1>WPJB Plugin Archive</h1>

<div class="wpjb-grid">
  <div class="wpjb-grid__item">
    <div class="wpjb-search">
      <button class="wpjb-search__btn">Reset</button>

      <input type="search" class="wpjb-search__text-input" id="wpjbSearchTextInput" placeholder="🔍" />

      <div class="wpjb-dropdown">
        Job Type
        <ul class="wpjb-dropdown__list">
          <li>
            <label>
              <input type="checkbox" value="Vejle" name="city" />Milwaukee</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Horsens" name="city" />Denver</label>
          </li>
          <li>
            <label>
              <input />Boston</label type="checkbox" value="Kolding" name="city">
          </li>
          <li>
            <label>
              <input type="checkbox" value="Kolding" name="city" />LA</label>
          </li>
        </ul>
      </div>

      <div class="wpjb-dropdown">
        Location
        <ul class="wpjb-dropdown__list">
          <li>
            <label>
              <input type="checkbox" value="Vejle" name="city" />Milwaukee</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Horsens" name="city" />Denver</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Kolding" name="city" />Boston</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Kolding" name="city" />LA</label>
          </li>
        </ul>
      </div>

      <div class="wpjb-dropdown">
        Category
        <ul class="wpjb-dropdown__list">
          <li>
            <label>
              <input type="checkbox" value="Vejle" name="city" />Milwaukee</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Horsens" name="city" />Denver</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Kolding" name="city" />Boston</label>
          </li>
          <li>
            <label>
              <input type="checkbox" value="Kolding" name="city" />LA</label>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="wpjb-grid__item">
    <div class="wpjb-quickSort">
      <div class="wpjb-quickSort__match-number">
        <h3>666 jobs match your search!</h3>
      </div>

      <div class="wpjb-quickSort__quickSort-dropdown">
        <div class="wpjb-dropdown">
          Sort
          <ul class="wpjb-dropdown__list">
            <li>
              <label>
                <input type="checkbox" value="Vejle" name="city" />Relevant</label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="Horsens" name="city" />Recent</label>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="wpjb-grid__item">
    <div class="wpjb-card">
      <div class="wpjb-card__hd">
        <h2 class="txt txt--h3"><?php the_title(); ?></h2>
      </div>
      <div class="wpjb-card__meta">
        <p class="txt txt--xs">⚖ Posted <?php echo date('m.d.y', (int) $post_publish_date); ?></p>
        <p class="txt txt--xs">⏲ Updated <?php echo $formattedDifference; ?> ago</p>
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

  </div>
</div>
<div class="wpjb-grid__item">
  <button class="wpjb-btn">Prev</button>
  <button class="wpjb-btn">Next</button>
</div>
</div>
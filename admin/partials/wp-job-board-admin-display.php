<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin/partials
 */

add_settings_section(
	WP_Job_Board_Admin::SETTINGS_SECTION,
	'Bullhorn Settings',
	false,
	WP_Job_Board_Admin::PAGE_SLUG
);

add_settings_field(
	WP_Job_Board_Admin::SETTING_CLIENT_ID,
	__( 'Client ID', 'wp_job_board' ),
	'render_input_field',
	WP_Job_Board_Admin::PAGE_SLUG,
	WP_Job_Board_Admin::SETTINGS_SECTION,
	array(
		'name' => WP_Job_Board_Admin::SETTING_CLIENT_ID,
	)
);

add_settings_field(
	WP_Job_Board_Admin::SETTING_CLIENT_SECRET,
	__( 'Client Secret', 'wp_job_board' ),
	'render_input_field',
	WP_Job_Board_Admin::PAGE_SLUG,
	WP_Job_Board_Admin::SETTINGS_SECTION,
	array(
		'name' => WP_Job_Board_Admin::SETTING_CLIENT_SECRET,
	)
);

add_settings_field(
	WP_Job_Board_Admin::SETTING_API_USERNAME,
	__( 'API Username', 'wp_job_board' ),
	'render_input_field',
	WP_Job_Board_Admin::PAGE_SLUG,
	WP_Job_Board_Admin::SETTINGS_SECTION,
	array(
		'name' => WP_Job_Board_Admin::SETTING_API_USERNAME,
	)
);

add_settings_field(
	WP_Job_Board_Admin::SETTING_API_PASSWORD,
	__( 'API Password', 'wp_job_board' ),
	'render_input_field',
	WP_Job_Board_Admin::PAGE_SLUG,
	WP_Job_Board_Admin::SETTINGS_SECTION,
	array(
		'name' => WP_Job_Board_Admin::SETTING_API_PASSWORD,
	)
);

function render_input_field( $args ) {
	$defaults = array(
		'type' => 'text',
		'name' => '',
	);
	$args     = array_merge( $defaults, $args );
	if ( empty( $args['name'] ) ) {
		return;
	}
	$value = get_option( $args['name'] );
	?>
	<input type="<?php echo esc_attr( $args['type'] ); ?>"
			name="<?php echo esc_attr( $args['name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
	/>
	<?php if ( ! empty( $args['description'] ) ) : ?>
		<p class="description"><?php echo wp_kses_post( $args['description'] ); ?></p>
		<?php
	endif;
}
?>
	<div class="wrap">
		<h2><?php esc_html_e( 'WP Job Board Settings', 'wp_job_board' ); ?></h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( WP_Job_Board_Admin::SETTINGS_GROUP );
			do_settings_sections( WP_Job_Board_Admin::PAGE_SLUG );
			?>
			<?php submit_button(); ?>
		</form>
		<form method="post" action="admin-post.php">
			<input type="hidden" name="action" value="trigger_sync">
			<input type="hidden" name="value" value="1">
			<?php submit_button( 'Trigger Sync' ); ?>
		</form>
	</div>
<?php
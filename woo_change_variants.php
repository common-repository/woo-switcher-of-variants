<?php
/*
Plugin Name: WooCommerce - Switcher of variants
Plugin URI: https://codevly.com/changevariants
Description: A plugin that adding a connection between pictures and variants.
Version: 1.0.0
Author: Damian Orzoł
Author URI: https://codevly.pl/
License: GPLv2 or later
*/

function switcher_of_variants_add_submenu() {
	add_submenu_page( 'woocommerce', 'Switcher of variants', 'Switcher of variants', 'manage_woocommerce', 'switcher_of_variants_page', 'switcher_of_variants_page' );
}
add_action( 'admin_menu' , 'switcher_of_variants_add_submenu', 99 );



function switcher_of_variants_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __('You do not have sufficient permissions to access this page') );
	}

	if ( isset( $_REQUEST['action'] ) ) {
		if ( 'save' == $_REQUEST['action'] ) {
			if ( ! isset( $_POST['check_true_field'] ) || ! wp_verify_nonce( $_POST['check_true_field'], 'check_true' )
			) {
				print 'Something went wrong.';
				exit();
			}
			update_option( 'switcher_of_variants_off_variants' , sanitize_text_field($_POST['switcher_of_variants_off_variants'] ) );
		}
	}

	?>

	<div class="sv-options-wrapper">

		<form  method="post" class="wrap sv-options sv-card">
			<h1><b>WooCommerce - Switcher of variants</b><a class="donate" target="_blank" href="https://www.paypal.com/pools/c/84hOC4TDOW">DONATE</a></h1>
			<?php				if ( isset( $_REQUEST['action'] ) ) {
				if ( 'save' == $_REQUEST['action'] ) {
					?>
					<p class="welcome sv-notice">The changes have been saved.</p>
					<?php
				}
			}
			?>
			<div class="sv-container a">
				<h2>Disabling unwanted variants</h2>
				<p>
					In the box below, enter the names of variants that you want to exclude from the changes through the gallery. Usually, these are sizes.
					Prefix them with a hashtag #. Place a comma between elements, you can also add spaces. If the name is two-part, use a hyphen (minus).
					Properly prepared variant names should look like this:
					<span>#first, #second-size, #third</span>
				</p>
				<div class="sv-wrap">
					<input placeholder="#first" type="text" name="switcher_of_variants_off_variants" value="<?php echo get_option("switcher_of_variants_off_variants", "" ); ?>" class="sv-field" />
					<div>
						<?php wp_nonce_field( 'check_true', 'check_true_field' ); ?>
						<input type="hidden" name="action" value="save" />
						<input type="submit" class="btn" value="SAVE" />
					</div>
				</div>
			</div>
			<div class="sv-container b">
				<h2>More informations</h2>
				<p>
					If you want to learn how to properly configure variants in the products, please visit the plugin's website:
				</p>
				<a target="_blank" href="https://codevly.com/changevariants">Plugin Page</a>
			</div>
		</form>

	</div>

	<?php
}

add_action( 'admin_head', 'switcher_of_variants_admin_styles' );
function switcher_of_variants_admin_styles() {
	wp_register_style( 'sv-styles-admin', plugins_url('/addons/admin-styles.min.css', __FILE__) );
	wp_enqueue_style(  'sv-styles-admin' );
}

add_filter( 'woocommerce_ajax_variation_threshold', 'switcher_of_variants_ajaxpower' );
function switcher_of_variants_ajaxpower() {
	return 500;
}

function switcher_of_variants_script() {  if ( is_product('variable')) {
	wp_enqueue_script( 'script-switcher-of-variants', plugins_url ('/addons/script.js', __FILE__), array(), '1.0.0', true );
	$switcher_of_variants_variable =   wp_strip_all_tags( get_option( 'switcher_of_variants_off_variants', '' ));
	if ( $switcher_of_variants_variable == "" ) { $switcher_of_variants_variable = ""; }
	$showoff_params = array(  'variable' => $switcher_of_variants_variable );
	wp_localize_script( 'script-switcher-of-variants', 'parameters', $showoff_params );
}}

add_action( 'wp_enqueue_scripts', 'switcher_of_variants_script' );

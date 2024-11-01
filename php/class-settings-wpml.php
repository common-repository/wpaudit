<?php
/**
 * Class Settings WPML
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_WPML {

	private $ajax;
	private $background_processing;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( AJAX $ajax, Background_Processing $background_processing ) {
		$this->ajax                  = $ajax;
		$this->background_processing = $background_processing;
	}


	/**
	 * Run all of the plugin functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'init_settings_page' ) );
		add_action( 'init', array( $this, 'convert' ) );
	}

	public function init_settings_page() {
		add_settings_section(
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_wpml',
			esc_html__( 'WPML', 'wp-audit' ),
			function () {
				?>
				<form method="POST">
					<p><?php esc_html_e( 'Sometimes when you import taxonomy items into WPML the terms are attached to the posts in the original langauge.', 'wp-audit' ); ?></p>
					<p><?php esc_html_e( 'Using the button below, we will loop through the posts and ensure they are set to the correct language.', 'wp-audit' ); ?></p>
					<p><strong><?php esc_html_e( 'Note:', 'wp-audit' ); ?></strong> <?php esc_html_e( 'You must go to your taxonomy settings first and ensure that all the terms to wish to convert have been translated, otherwise they will remain in their existing language.', 'wp-audit' ); ?></p>
					<p><strong><?php esc_html_e( 'Note:', 'wp-audit' ); ?></strong> <?php esc_html_e( 'Currently limited to posts only.', 'wp-audit' ); ?></p>

					<p>
						<label>
							<?php esc_html_e( 'Select Taxonomy:', 'wp-audit' ); ?>
							<select name="wpml_taxonomy">
							<?php
								$taxonomies = get_taxonomies(
									array(
										'public' => 'true'
									),
									OBJECT
								);
								foreach ( $taxonomies as $key => $tax ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $tax->labels->singular_name ); ?></option>
									<?php
								}
							?>
							</select>
						</label>
					</p>

					<?php submit_button( 'Convert Terms' ); ?>

					<?php wp_nonce_field( 'run_wpml_check_action', 'run_wpml_check_nonce' ); ?>
				</form>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_wpml_settings'
		);
	}

	function convert() {
		if ( isset( $_POST['run_wpml_check_nonce'] ) && wp_verify_nonce( $_POST['run_wpml_check_nonce'], 'run_wpml_check_action' ) ) {
			$taxonomy        = sanitize_text_field( $_POST[ 'wpml_taxonomy' ] );
			$languages       = icl_get_languages( 'skip_missing=0&orderby=code' );
			$total           = 0;
			$converted_terms = array();
			foreach ( $languages as $key => $language ) {
				global $sitepress;
				$sitepress->switch_lang( $key );
				$posts = get_posts(
					array(
						'posts_per_page'   => -1,
						'suppress_filters' => false,
					)
				);

				$terms = get_terms(
					$taxonomy,
					array(
						'hide_empty' => false,
					)
				);

				$term_check = array();
				foreach ( $terms as $term ) {
					$term_check[ $term->name ] = $term->term_id;
				}
				foreach ( $posts as $post ) {
					$terms    = wp_get_object_terms( '' . $post->ID, $taxonomy );
					$term_ids = array();
					$change   = false;
					foreach ( $terms as $term ) {
						if ( $term_check[ $term->name ] !== $term->term_id ) {
							if ( ! empty( $term_check[ $term->name ] ) ) {
								$term_ids[] = (int) $term_check[ $term->name ];
								$change = true;
								$total ++;
								if ( ! in_array( $term->name, $converted_terms ) ) {
									$converted_terms[] = $term->name;
								}
							} else {
								$term_ids[] = (int) $term->term_id;
							}
						} else {
							$term_ids[] = (int) $term->term_id;
						}
					}
					if ( $change ) {
						$test = wp_set_object_terms( $post->ID, $term_ids, $taxonomy );
					}
				}
			}
			if ( $total > 0 ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo $total ?> <?php esc_html_e( 'Post Term Languages Converted', 'wp-audit' ); ?></p>
					<ul>
					<?php
					foreach ( $converted_terms as $term ) {
						?>
						<li><?php echo esc_html( $term ); ?></li>
						<?php
					}
					?>
					</ul>
				</div>
				<?php
			}
		}
	}
}

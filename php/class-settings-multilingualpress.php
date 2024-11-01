<?php
/**
 * Class Settings MultilingualPress
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_MultilingualPress {

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
		add_action( 'init', array( $this, 'download' ) );
	}

	public function init_settings_page() {
		add_settings_section(
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_multilingualpress',
			esc_html__( 'MultilingualPress', 'wp-audit' ),
			function () {
				?>
				<p>WordPress MultilingualPress allows you to translate contenet to multiple languages on your site.</p>

				<p>Use the button below to identify which content has and has not been translated.</p>
				<form method="POST">
					<?php submit_button( 'Show Multilingual Content' ); ?>
					<?php wp_nonce_field( 'run_multilingual_check_action', 'run_multilingual_check_nonce' ); ?>
				</form>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_multilingualpress_settings'
		);
	}

	function download() {
		if ( isset( $_POST['run_multilingual_check_nonce'] ) && wp_verify_nonce( $_POST['run_multilingual_check_nonce'], 'run_multilingual_check_action' ) ) {

			header( "Content-type: text/csv" );
			header( "Content-Disposition: attachment; filename=multilingualpress.csv" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );

			$sites = get_sites();

			$string  = '';
			$string .= '"Post ID",';
			$string .= '"Title",';
			$string .= '"URL",';
			$string .= '"Post Type",';
			$string .= '"Edit Link",';
			foreach ( $sites as $site ) {
				$string      .= '"Site ID ' . $site->id . ' (' . $site->blogname . ') - Post ID",';
				$string      .= '"Site ID ' . $site->id . ' (' . $site->blogname . ') - URL",';
				$string      .= '"Site ID ' . $site->id . ' (' . $site->blogname . ') - Edit Link",';
			}
			$string .= "\n";

			echo $string;

			$posts = get_posts(
				array(
					'posts_per_page' => -1,
					'post_type'      => get_post_types(),
				)
			);

			if ( class_exists( '\Mlp_Helpers' ) ) {
				foreach ( $posts as $post ) {
					$string  = '';
					$string .= '"' . $post->ID . '",';
					$string .= '"' . $post->post_title . '",';
					$string .= '"' . get_permalink( $post->ID ) . '",';
					$string .= '"' . get_edit_post_link( $post->ID ) . '",';
					$links   = \Mlp_Helpers::load_linked_elements( $post->ID );
					foreach ( $sites as $site ) {
						switch_to_blog( $site->id );
						$string      .= '"' . ( isset( $links[ $site->id ] ) ? $links[ $site->id ] : ''  ) . '",';
						$string      .= '"' . ( isset( $links[ $site->id ]) ? get_permalink( $links[ $site->id ] ) : ''  ) . '",';
						$string      .= '"' . ( isset( $links[ $site->id ] ) ? get_edit_post_link( $links[ $site->id ] ) : ''  ) . '",';
						restore_current_blog();
					}
					$string .= "\n";

					echo $string;
				}
			}
			exit;
		}
	}
}

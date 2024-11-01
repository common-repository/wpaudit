<?php
/**
 * Class Settings General
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_General {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_general',
			esc_html__( 'General', 'wp-audit' ),
			function () {
				$emojis_enabled = false;

					if ( 
						has_action( 'admin_print_styles', 'print_emoji_styles' ) ||
						has_action( 'wp_head', 'print_emoji_detection_script' ) ||
						has_action( 'admin_print_scripts', 'print_emoji_detection_script' ) ||
						has_action( 'wp_print_styles', 'print_emoji_styles' ) ||
						has_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ) ||
						has_filter( 'the_content_feed', 'wp_staticize_emoji' ) ||
						has_filter( 'comment_text_rss', 'wp_staticize_emoji' )
					) {
						$emojis_enabled = true;
					}				
					?>
					<form method="POST">
						<?php wp_nonce_field( 'run_general_check_action', 'run_general_check_nonce' ); ?>
						<p>Since WordPress 4.2 support for emojis was added into core for older browsers. This generates an additional HTTP request on your WordPress site to load the wp-emoji-release.min.js file, and this loads on every single page.</p>

						<ul>
							<li><?php $emojis_enabled ? esc_html_e( 'Emojis enabled', 'wp-audit' ) : esc_html_e( 'Emojis disabled', 'wp-audit' ); ?></li>
						</ul>

						<h3>Archive Pages</h3>

						<p>Download the CSV to show the archive pages which you may want to remove and redirect e.g. if you only have 1 or 2 site authors, you likely do not require author archives.</p>

						<h4>Date Archives</h4>
						<?php submit_button( 'View Date Archives' ); ?>
						
						<h4>Author Archives</h4>
						<?php submit_button( 'View Author Archives' ); ?>

						<h4>Tag Archives</h4>
						<?php submit_button( 'View Tag Archives' ); ?>

						<h3>Comments</h3>

						<p>If not used you should completely disable WordPress comments overall or for selected post types.</p>
						
						<ul>
							<li>
							Remove Comment Support on Post Types
							<?php
								$post_types         = get_post_types(); 
								$post_type_comments = array();
								foreach ( $post_types as $key => $post_type ) {
									if ( post_type_supports( $key, 'comments' ) ) {
										$post_type_comments[] = $key;
									}
								}
								if ( ! empty( $post_type_comments ) ) {
									?>
									<ul>
									<?php
									foreach ( $post_type_comments as $post_type ) {
										echo '<li>' . $post_type . '</li>';
									}
									?>
									</ul>
									<?php
								} else {
									echo ' - none';
								}
							?>
							</li>
						</ul>

						<h3>Export</h3>
						<p>Export all posts to csv:</p>
						<?php submit_button( 'Export Posts' ); ?>
						<p>Export all pages to csv:</p>
						<?php submit_button( 'Export Pages' ); ?>
					</form>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_general_settings'
		);
	}

	function download() {
		if ( isset( $_POST['run_general_check_nonce'] ) && wp_verify_nonce( $_POST['run_general_check_nonce'], 'run_general_check_action' ) ) {

			$filename = 'general';
			// $options['http'] = array(
			// 	'method'        => "HEAD",
			// 	'ignore_errors' => 1,
			// 	'max_redirects' => 0
			// );

			if ( 'Export Posts' === $_POST['submit'] || 'Export Pages' === $_POST['submit'] ) {

				if ( 'Export Posts' === $_POST['submit'] ) {
					$filename = 'general-post-export';
				} else {
					$filename = 'general-page-export';
				}

				header( "Content-type: text/csv" );
				header( "Content-Disposition: attachment; filename=" . $filename . ".csv" );
				header( "Pragma: no-cache" );
				header( "Expires: 0" );

				$string  = '';
				$string .= '"Post ID",';
				$string .= '"Post Create Date",';
				$string .= '"Post Last Modified Date",';
				$string .= '"Post Link",';
				$string .= '"Post Title",';
				$string .= '"Post Categories",';
				$string .= "\n";

				echo $string;

				$posts_query = new \WP_Query(
					array(
						'posts_per_page' => -1,
						'post_type'      => ( 'Export Posts' === $_POST['submit'] ) ? 'post' : 'page',
					)
				); 

				foreach ( $posts_query->posts as $post ) {
					$string  = '';
					$string .= '"' . esc_html( $post->ID ) . '",';
					$string .= '"' . esc_html( get_the_date( '', $post ) ) . '",';
					$string .= '"' . esc_html( get_the_modified_date( '', $post ) ) . '",';
					$string .= '"' . esc_url( get_permalink( $post->ID ) ) . '",';
					$string .= '"' . esc_html( $post->post_title ) . '",';
					$string .= '"';

					$categories        = wp_get_post_categories( $post->ID );
					$categories_string = '';
					foreach ( $categories as $category ) {
						if ( ! empty( $categories_string ) ) {
							$categories_string .= ', ';
						}
						$category           = get_category( $category );
						$categories_string .= esc_html( $category->name );
					}
					$string .= $categories_string;
					$string .= '"';
					$string .= ',';
					$string .= "\n";
					echo $string;
				}

			} else {

				if ( 'View Date Archives' === $_POST['submit'] ) {
					$filename = 'general-date-archives';
				} elseif( 'View Author Archives' === $_POST['submit'] ) {
					$filename = 'general-author-archives';
				} elseif ( 'View Tag Archives' === $_POST['submit'] ) {
					$filename = 'general-tag-archives';
				}

				header( "Content-type: text/csv" );
				header( "Content-Disposition: attachment; filename=" . $filename . ".csv" );
				header( "Pragma: no-cache" );
				header( "Expires: 0" );

				$string  = '';
				$string .= '"URL",';
				$string .= "\n";

				echo $string;

				if ( 'View Date Archives' === $_POST['submit'] ) {

					$dates = wp_get_archives( 
						array( 
							'type'   => 'daily', 
							'echo'   => false,
							'format' => 'custom',
						)
					);
					$dates = explode( '</a>', $dates );
					foreach ( $dates as $date ) {
						$date = trim( $date );
						if ( ! empty( $date ) ) {
							$date = $date . '</a>';
							$date = trim( $date );
							$url  = new \SimpleXMLElement( $date );
							$url  = $url['href'] . '';
							// $body = file_get_contents( $url, null, stream_context_create( $options ) );
							// sscanf( $http_response_header[0], 'HTTP/%*d.%*d %d', $code );
							// if ( 404 !== $code ) {
								$string  = '';
								$string      .= '"' . esc_url( $url ) . '",';
								$string .= "\n";
								echo $string;
							// }
						}
					}

				} elseif ( 'View Author Archives' === $_POST['submit'] ) {
					$authors = get_users();
					foreach ( $authors as $author ) {
						$url  = get_author_posts_url( $author->ID );
						// $body = file_get_contents( $url, null, stream_context_create( $options ) );
						// sscanf( $http_response_header[0], 'HTTP/%*d.%*d %d', $code );
						// if ( 404 !== $code ) {
							$string  = '';
							$string      .= '"' . esc_url( $url ) . '",';
							$string .= "\n";
							echo $string;
						// }
					}
				} elseif ( 'View Tag Archives' === $_POST['submit'] ) {
					$tags = get_tags();			
					foreach ( $tags as $tag ) {
						$url  = get_tag_link( $tag->term_id );
						// $body = file_get_contents( $url, null, stream_context_create( $options ) );
						// sscanf( $http_response_header[0], 'HTTP/%*d.%*d %d', $code );
						// if ( 404 !== $code ) {
							$string  = '';
							$string .= '"' . esc_url( $url ) . '",';
							$string .= "\n";
							echo $string;
						// }
					}
				}
			}
			exit;
		}
	}
}

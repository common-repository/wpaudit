<?php
/**
 * Class Settings Unattached Images
 *
 * @since 1.0.0
 *
 * @package digital_elite\wp_audit
 */

namespace digital_elite\wp_audit;

/**
 * Partial settings page
 */
class Settings_Unattached_Images {

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
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_unattached_images',
			esc_html__( 'Unattached Images', 'wp-audit' ),
			function () {
				$args = array(
					'post_type'      => 'attachment',
					'posts_per_page' => -1,
					'post_status'    => null,
					'post_parent'    => 0
				);
				$attachments = get_posts( $args );

				?>
				<form method="POST">
					<p><?php esc_html_e( 'When you delete posts or pages in WordPress, the images and videos you have inserted into the WordPress articles will not be deleted at the same time. As a result you can easily end up with lots of unused images, videos and other files clogging up your media library (known as "orphaned" files).:', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'You can view your unattached media items here::', 'wp-audit' ); ?> <a href="<?php echo get_site_url();?>/wp-admin/upload.php?attachment-filter=detached"><?php echo get_site_url();?>/wp-admin/upload.php?attachment-filter=detached</a> <?php esc_html_e( 'and download a CSV list below.:', 'wp-audit' ); ?></p>

					<?php submit_button( 'View Unattached Images' ); ?>
					
					<p><?php esc_html_e( 'The number of unattached items on your site is:', 'wp-audit' ); ?> <?php echo count( $attachments ); ?><br/>
					<?php $file_cache_size = $this->get_dir_size( wp_upload_dir()['basedir'] ); ?>
					<?php esc_html_e( 'Size of your /wp-content/uploads/ directory is:', 'wp-audit' ); ?> <?php echo $this->format_bytes( $file_cache_size ); ?></p>

					<p><?php esc_html_e( 'Note: Unattached does not mean unused. We do not recommend you bulk delete these files as although files can be unattached to posts, they may be used within your theme or referenced in a page or post. Background images, theme images, features images, images used in galleries, sliders and page builder elements may all show as unattached but still be in use.', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'You should take the following steps:', 'wp-audit' ); ?></p>

					<ol>
						<li><?php esc_html_e( 'Backup your website and wp-content directory', 'wp-audit' ); ?></li>
						<li><?php esc_html_e( 'Manually review and delete all unattached files', 'wp-audit' ); ?></li>
						<li><?php esc_html_e( 'Add items from the media library by inserting them directly into posts or pages, not by uploading to the media library first', 'wp-audit' ); ?></li>
						<li><?php esc_html_e( 'Consider offloading your media library using WP Offload Media (', 'wp-audit' ); ?><a href="https://deliciousbrains.com/wp-offload-media/" target="_blank">https://deliciousbrains.com/wp-offload-media/</a><?php esc_html_e( ')', 'wp-audit' ); ?></li>
					</ol>
					
					<p><?php esc_html_e( 'Download Media Attachments (this should download a csv file of all media with title, url, uploaded or modified date (or both if possible), attached/unattached.', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'For large and complex media libraries we recommend using the Media Cleaner plugin', 'wp-audit' ); ?> <a href="https://en-gb.wordpress.org/plugins/media-cleaner/" target="_blank">https://en-gb.wordpress.org/plugins/media-cleaner/</a><?php esc_html_e( '. Media Cleaner checks through both registered media in the your Media Library, as well as looking through all of the files in your /uploads directory. For technical users, Media Cleaner Pro checks in Posts, Custom Post Types, Post Meta, Widget, Logs and more. If using a page builder you should ensure support for your specific page builder and use the paid for/commercial version.', 'wp-audit' ); ?></p>

					<p><?php esc_html_e( 'Ensure you backup your site, take a copy of your entire media library folder, test in a staging environment and read all documentation carefully.', 'wp-audit' ); ?></p>
					
					<?php wp_nonce_field( 'run_unattached_images_check_action', 'run_unattached_images_check_nonce' ); ?>
				</form>
				<?php
			},
			DIGITAL_ELITE_WP_AUDIT_PREFIX . '_unattached_images_settings'
		);
	}

	function download() {
		if ( isset( $_POST['run_unattached_images_check_nonce'] ) && wp_verify_nonce( $_POST['run_unattached_images_check_nonce'], 'run_unattached_images_check_action' ) ) {
			
			$args = array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_status'    => null,
				'post_parent'    => 0,
			);
			$attachments = get_posts( $args );

			header( "Content-type: text/csv" );
			header( "Content-Disposition: attachment; filename=unattached-images-posts.csv" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );

			$string  = '';
			$string .= '"Image ID",';
			$string .= '"Title",';
			$string .= '"URL",';
			$string .= '"Edit Link",';
			$string .= "\n";

			echo $string;

			foreach ( $attachments as $post ) {
				$string  = '';
				$string .= '"' . $post->ID . '",';
				$string .= '"' . $post->post_title . '",';
				$string .= '"' . get_permalink( $post->ID ) . '",';
				$string .= '"' . get_edit_post_link( $post->ID ) . '",';
				$string .= "\n";

				echo $string;
			}
			exit;
		}
	}
	
	function get_dir_size( $directory ) {

		$size = 0;
		if ( ! is_dir( $directory ) ) {
			return $size;
		}
		foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $directory ) ) as $file ) {
			$size += $file->getSize();
		}

		return $size;
	}

	function format_bytes( $bytes, $precision = 2 ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		$bytes /= pow( 1024, $pow );

		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
	}
}

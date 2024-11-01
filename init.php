<?php if (!defined('ABSPATH')) die();

if (!class_exists('SOCKO_Init')) {

	class SOCKO_Init
	{

		/**
		 *  Register activation / deactivation hooks
		 */
		public static function registerPluginHooks()
		{
			register_activation_hook(SOCKO_SLUG, array('SOCKO_Init', 'activate'));
			register_deactivation_hook(SOCKO_SLUG, array('SOCKO_Init', 'deactivate'));
		}


		/**
		 *  On plugin activation
		 */
		public static function activate()
		{
			add_rewrite_rule('^social-link/(.+)/?', '?social_link=1', 'top');
			flush_rewrite_rules();
		}

		/**
		 *  On plugin deactivation
		 */
		public static function deactivate()
		{
			flush_rewrite_rules();
		}


		/**
		 *  Initialize main plugin hooks
		 */
		public static function hooks()
		{
			add_action( 'init', array('SOCKO_Init', 'addRewrites'));
			add_action( 'template_redirect', array('SOCKO_Init', 'detectSocialLinks') );
			add_action( 'admin_menu', array('SOCKO_Init', 'addMenu') );

			add_filter( 'attachment_fields_to_edit', array( 'SOCKO_Init', 'applyFilter' ), 11, 2 );
			add_filter( 'attachment_fields_to_save', array( 'SOCKO_Init', 'saveFields' ), 11, 2 );
		}

		/**
		 *  Save custom attachment fields
		 */
		public static function saveFields( $post, $attachment ) {
			// If our field is set, update the post meta
			if ( isset( $attachment['social_linkz_redirection'] ) ) {
				update_post_meta( $post['ID'], 'social_linkz_redirection', esc_url($attachment['social_linkz_redirection']) );
			}
			return $post;
		}

		/**
		 *  Apply custom attachment fields
		 */
		public static function applyFilter( $form_fields, $post = null ) {

			// Check if the mime type matches images
			if (
				preg_match( "/image/", $post->post_mime_type) &&
				!in_array( $post->post_mime_type, array('audio', 'video') )
			) {

				// We add our fields into the $form_fields array
				$form_fields['social_linkz_redirection'] = array(
					'label'       => 'Socko Redirection URL',
					'input'       => 'text',
					'value'       => get_post_meta( $post->ID, 'social_linkz_redirection', true )
				);

				$form_fields['social_linkz_generated'] = array(
					'label'       => 'Socko Sharing URL',
					'input'       => 'text',
					'value'       => str_replace('wp-content', 'social-link', wp_get_attachment_url( $post->ID ))
				);
			}

			// We return the completed $form_fields array
			return $form_fields;
		}

		/**
		 *  Add an Admin Menu page
		 */
		public static function addMenu() {
			add_menu_page(
				"Socko",
				'Socko',
				'manage_options',
				'socko/socko.php',
				array('SOCKO_Init', 'renderPage'),
				plugins_url( 'socko/img/icon.png' )
			);
		}

		/**
		 *  Render Admin Menu Page
		 */
		public static function renderPage() {
			include_once ( SOCKO_PATH . "/page.php" );
		}

		/**
		 *  Detect any links that have /social-link/ in them
		 */
		public static function detectSocialLinks() {
			if (isset($_GET['social_link'])) {

				// Get the requested image
				$SCRIPT_URL = $_SERVER['SCRIPT_URL'];
				$SCRIPT_URL = str_replace('/social-link', '', $SCRIPT_URL);
				$IMAGE_PATH = ABSPATH . 'wp-content' . $SCRIPT_URL;

				// Check if image exists and if not, redirect back to homepage
				if (!file_exists($IMAGE_PATH)) {
					wp_redirect('/');
					exit;
				}

				// If image exists, check if request came from Facebook or Skype
				if (
					strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'facebook') !== false ||
	    	        strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'skype') !== false
				) {
					status_header( 200 );
					header( 'Cache-Control: public' );
					header( 'Content-Description: File Transfer' );
					header( 'Content-Type: ' . mime_content_type( $IMAGE_PATH ) );
					header( 'Content-Disposition: attachment; filename="' . basename( $IMAGE_PATH ) . '"' );
					header( 'Expires: 0' );
					header( 'Content-Length: ' . filesize( $IMAGE_PATH ) );
					readfile( $IMAGE_PATH );
					die();
				} else {
					// If request came from a regular browser, do a specified redirect
					$attachment_ID = self::getAttachmentFromURL(str_replace('social-link', 'wp-content', $_SERVER['SCRIPT_URI']));

					// Check if attachment exists
					if (!empty($attachment_ID)) {

						// Check if redirection URL exists
						$redirection = get_post_meta( $attachment_ID, 'social_linkz_redirection', true );
						if (!empty($redirection)) {
							// Do the Redirect!
							wp_redirect($redirection);
							exit;
						} else {
							// Redirect back to homepage
							wp_redirect('/');
							exit;
						}

					} else {
						// Redirect back to homepage
						wp_redirect('/');
						exit;
					}
				}

			}
		}

		private static function getAttachmentFromURL($image_url) {
			global $wpdb;
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
			return $attachment[0];
		}

		/**
		 *  Add the necessary rewrites so this plugin can work
		 */
		public static function addRewrites() {
			add_rewrite_rule('^social-link/(.+)/?', '?social_link=1', 'top');
		}


		/**
		 *  Initialize the plugin
		 */
		public static function init()
		{
			// Register activation / deactivation hooks
			self::registerPluginHooks();

			// Initialize the main plugin hooks
			self::hooks();
		}
	}

}
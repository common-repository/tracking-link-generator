<?php 

class Alti_tlg_Admin {

	private $plugin_real_name;
	private $plugin_name;
	private $version;
	private $messages;

	/**
	 * constructor
	 */
	public function __construct( $plugin_real_name, $plugin_name, $version ) {
		$this->plugin_real_name = $plugin_real_name;
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		global $wpdb;
        $this->db = $wpdb;

        $this->messages = array();

        $_POST = stripslashes_deep( $_POST );
        $this->campaign_page = (empty($_POST['campaign_page'])?'':self::get_cleaned($_POST['campaign_page'], 'url'));
        $this->campaign_source = (empty($_POST['campaign_source'])?'':self::get_cleaned($_POST['campaign_source'], 'text'));
        $this->campaign_medium = (empty($_POST['campaign_medium'])?'':self::get_cleaned($_POST['campaign_medium'], 'text'));
        $this->campaign_name = (empty($_POST['campaign_name'])?'':self::get_cleaned($_POST['campaign_name'], 'text'));
        $this->custom_key = (empty($_POST['custom_key'])?'':self::get_cleaned($_POST['custom_key'], 'text'));
        $this->custom_value = (empty($_POST['custom_value'])?'':self::get_cleaned($_POST['custom_value'], 'text'));
        $this->submit_manage_links = (empty($_POST['submit_manage_links'])?'':1);

        $this->new_campaign_medium = (empty($_POST['new_campaign_medium'])?'':self::get_cleaned($_POST['new_campaign_medium'], 'text'));
        $this->new_campaign_source = (empty($_POST['new_campaign_source'])?'':self::get_cleaned($_POST['new_campaign_source'], 'text'));
        $this->remove_campaign_medium = (empty($_POST['remove_campaign_medium'])?'':self::get_cleaned($_POST['remove_campaign_medium'], 'text'));
        $this->remove_campaign_source = (empty($_POST['remove_campaign_source'])?'':self::get_cleaned($_POST['remove_campaign_source'], 'text'));
        $this->bitly_token = (empty($_POST['bitly_token'])?'':self::get_cleaned($_POST['bitly_token'], 'text'));
        $this->remove_bitly_token = (empty($_POST['remove_bitly_token'])?'':self::get_cleaned($_POST['remove_bitly_token'], 'number'));
        $this->submit_settings = (empty($_POST['submit_settings'])?'':1);

    }

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Add submenu to left page in admin
	 */
	public function add_submenu_page() {
		add_menu_page( 'Tracking Link Gen', 'Tracking Link Gen', 'edit_posts', $this->plugin_name . '-settings-page', array($this, 'render_settings_page'), 'dashicons-controls-repeat' );
	}

	/**
	 * Render settings page for plugin
	 */
	public function render_settings_page() {
		require plugin_dir_path( __FILE__ ) . 'views/' . $this->plugin_name . '-admin-settings-page.php';
	}

	/**
	 * prepare enqueue styles for wordpress hook
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/alti_tlg-admin.css?v=' . rand(), array(), $this->version, 'all' );
	}

	/**
	 * prepare enqueue scripts for wordpress hook
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/alti_tlg-admin.js?v=' . rand(), array( 'jquery' ), $this->version, false );
	}

	/**
	 * prepare enqueue scripts for wordpress hook
	 */
	public function enqueue_notices() {

		foreach( $this->messages as $message ) {

			// error warning info success
			echo '<div class="notice notice-' . $message[1] . ' is-dismissible"><p>' . $message[0] . '</p></div>';
		}

	}

	/**
	 * add a settings link to plugin page.
	 * @param string $links array of links
	 */
	public function add_settings_link( $links ) {
	    $settings_link = '<a href="admin.php?page=' . $this->plugin_name . '-settings-page">' . __( 'Settings' ) . '</a>';
	    array_unshift($links, $settings_link);
	  	return $links;
	}

	/**
	 * clean query string like POST or GET
	 * @param  string $string POST OR GET
	 * @param  string $type   text, number or url...
	 * @param  string $length limit length
	 */
	private function get_cleaned( $string, $type = 'text', $length = '' ) {
		if( empty( $string ) ) return false;
		if( $type == 'text' ) {
			$string = sanitize_text_field( $string );
		}
		if( $type == 'number' ) {
			$string = intval( $string );
		}
		if( $type == 'url' ) {
			if( substr($string, 0, 4) == 'http' ) {
				// $string = urlencode( $string );
			}
			else {
				array_push( $this->messages, array( 'Page to link is not a valid url. It has to start with http.', 'warning' ) );
				return false;
			}
		}
		return $string;
	}

	public function check_manage_links() {
		if( 
			$this->campaign_page && 
			$this->campaign_source && 
			$this->campaign_medium && 
			$this->campaign_name
		) {
			self::add_link();
		}
		else {
			if( !empty( $this->submit_manage_links ) ) {
				array_push( $this->messages, array('Page to link, Source, Medium or Campaign Name ar missing', 'error') );
			}
		}
	}

	public function get_full_link() {
		$custom_pair = '';
		if( $this->custom_key && $this->custom_value ) $custom_pair = '&' . urlencode($this->custom_key) . '=' . urlencode($this->custom_value);

		return $this->campaign_page . '?utm_source=' . urlencode($this->campaign_source) . '&utm_medium=' . urlencode($this->campaign_medium) . '&utm_campaign=' . urlencode($this->campaign_name) . $custom_pair;
	}

	private function add_link() {
			
			$campaign_full_link = self::get_full_link();

			$campaign_short_link = self::get_bitly( $campaign_full_link );

			$this->db->insert(
				$this->db->prefix . $this->plugin_name . '_links',
				array(
					'campaign_name' => $this->campaign_name,
					'campaign_full_link' => $campaign_full_link,
					'campaign_short_link' => $campaign_short_link,
					'user_id' => get_current_user_id(),
					'date' => time()
				)
			);
			array_push( $this->messages, array('A new Campaign Link has been created successfully.', 'success') );

			$this->campaign_page = '';
			$this->campaign_source = '';
			$this->campaign_medium = '';
			$this->campaign_name = '';
			$this->custom_key = '';
			$this->custom_value = '';
	}

	private function get_bitly($full_link) {
		if( get_option( $this->plugin_name . '_bitly_token' ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/assets/vendors/bitly.php';
			$params_bitly = array();
			$params_bitly['access_token'] = get_option( $this->plugin_name . '_bitly_token' );

			$params_bitly['longUrl'] = $full_link;
			$bitly = bitly_get('shorten', $params_bitly);
			if( !empty($bitly['data']['url']) )  $short_link = $bitly['data']['url'];
			if( empty($short_link) ) $short_link = 'Bitly Error';
			return $short_link;
		}
		else {
			return 'n/a';
		}
	}

	public function get_links() {

			return $this->db->get_results( "SELECT * FROM " . $this->db->prefix . $this->plugin_name . "_links ORDER by date DESC" );

	}

	public function get_sources() {

			return $this->db->get_results( "SELECT * FROM " . $this->db->prefix . $this->plugin_name . "_sources ORDER by source_name ASC" );

	}

	public function get_mediums() {

			return $this->db->get_results( "SELECT * FROM " . $this->db->prefix . $this->plugin_name . "_mediums ORDER by medium_name ASC" );

	}

	public function check_settings() {

		if( !empty($this->submit_settings) ) {

			// bitly
			if( !empty($this->bitly_token) && $this->bitly_token != get_option( $this->plugin_name . '_bitly_token' ) ) { 
				update_option( $this->plugin_name . '_bitly_token', $this->bitly_token );
				array_push( $this->messages, array( 'The Bitly Token has been updated.', 'success' ) );
			}
			if( !empty($this->remove_bitly_token) &&  $this->remove_bitly_token == 1 ) {
				update_option( $this->plugin_name . '_bitly_token', '' );
				array_push( $this->messages, array( 'Bitly Token is empty now.', 'success' ) );

			}

			// add new source / medium
			if( !empty($this->new_campaign_medium) ) { 
				$this->db->insert( $this->db->prefix . $this->plugin_name . '_mediums',array('medium_name' => $this->new_campaign_medium ));
				array_push( $this->messages, array( 'New Campaign Medium has been added', 'success' ) );

			}
			if( !empty($this->new_campaign_source) ) { 
				$this->db->insert( $this->db->prefix . $this->plugin_name . '_sources',array('source_name' => $this->new_campaign_source ));
				array_push( $this->messages, array( 'New Campaign Source has been added', 'success' ) );

			}

			// remove source / medium
			if( !empty($this->remove_campaign_medium) ) {
				$this->db->delete( $this->db->prefix . $this->plugin_name . '_mediums',array('medium_name' => $this->remove_campaign_medium));
				array_push( $this->messages, array( 'A Campaign Medium has been removed', 'success' ) );

			}
			if( !empty($this->remove_campaign_source) ) {
				$this->db->delete( $this->db->prefix . $this->plugin_name . '_sources',array('source_name' => $this->remove_campaign_source));
				array_push( $this->messages, array( 'A Campaign Source has been removed', 'success' ) );

			}

		}

	}

	public function get_promote_content( $from ) {

		

	}

}

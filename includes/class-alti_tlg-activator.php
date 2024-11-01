<?php 
/**
 * fired on activation
 */
class Alti_tlg_Activator extends Alti_tlg {

	public $db;

	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		global $wpdb;
        $this->db = $wpdb;
	}

	/**
	 * set option for plugin
	 */
	public function run() {

		// set version
		if( !get_option('alti_tlg_version') ) {
			add_option( 'alti_tlg_version', $this->get_version(), '', 'yes' );
		}

		// update steps
		if( get_option('alti_tlg_version') && get_option('alti_tlg_version') != $this->get_version() ) {
			// 
		}
		
		// update version
		if( get_option('alti_tlg_version') != $this->get_version() ) {
			update_option( 'alti_tlg_version', $this->get_version() );
		}

		// create table
		$table_name_links = $this->db->prefix . $this->plugin_name . '_links';
		if($this->db->get_var("SHOW TABLES LIKE '" . $table_name_links . "'") != $table_name_links) {
			$charset_collate = $this->db->get_charset_collate();

			$sql = "CREATE TABLE " . $table_name_links . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  campaign_name text NOT NULL,
			  campaign_full_link text NOT NULL,
			  campaign_short_link text NOT NULL,
			  user_id int NOT NULL,
			  date int NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name_source = $this->db->prefix . $this->plugin_name . '_sources';
		if($this->db->get_var("SHOW TABLES LIKE '" . $table_name_source . "'") != $table_name_source) {
			$charset_collate = $this->db->get_charset_collate();

			$sql = "CREATE TABLE " . $table_name_source . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  source_name text NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			$prepopulated_sources = array('newsletter', 'twitter', 'affiliate', 'facebook', 'linkedin', 'pinterest');

			foreach ($prepopulated_sources as $prepopulated_source) {
				$this->db->insert($table_name_source,array('source_name' => $prepopulated_source));
			}


		}

		$table_name_medium = $this->db->prefix . $this->plugin_name . '_mediums';
		if($this->db->get_var("SHOW TABLES LIKE '" . $table_name_medium . "'") != $table_name_medium) {
			$charset_collate = $this->db->get_charset_collate();

			$sql = "CREATE TABLE " . $table_name_medium . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  medium_name text NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			$prepopulated_mediums = array('cpc', 'banner', 'email', 'post');

			foreach ($prepopulated_mediums as $prepopulated_medium) {
				$this->db->insert($table_name_medium,array('medium_name' => $prepopulated_medium));
			}

		}

		add_option( $this->plugin_name . '_bitly_token', '' );
		
	}



}
<?php
if (! defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

/**
 * Main Plugin Class
 *
 * @since      1.0.0
 * @package    btn-dr
 * @subpackage btn-dr/classes
 * @author     Augustus Villanueva <augustus@businesstechninjas.com>
 */
final class btn_sm {

	function init(){
			// Text Domain / Localization
			$this->load_text_domain();
			// Init Hooks
			add_action('init',[$this,'init_hooks']);
    }

    // Init
    function init_hooks(){

		   add_filter('gettext', [$this, 'change_shipping_address_text'], 20, 3);
		   
		   // Add Cron Job for boost_650_cron_job
		   add_action('boost_650_cron_job', [$this, 'boost_650_cron_job_func'], 10, 1);
		   // Add Cron Job for boost_650_turn_off_cron_job
		   add_action('boost_650_turn_off_cron_job', [$this, 'boost_650_turn_off_cron_job_func'], 10, 1);

		   // Add Cron Job for boost_850_cron_job
		   add_action('boost_850_cron_job', [$this, 'boost_850_cron_job_func'], 10, 1);
		   // Add Cron Job for boost_850_turn_off_cron_job
		   add_action('boost_850_turn_off_cron_job', [$this, 'boost_850_turn_off_cron_job_func'], 10, 1);

		   // Add Cron Job for boost_650_cron_job_add_on
		   add_action('boost_650_cron_job_add_on', [$this, 'boost_650_cron_job_add_on_func'], 10, 1);
		   // Add Cron Job for boost_650_turn_off_cron_job_add_on
		   add_action('boost_650_turn_off_cron_job_add_on', [$this, 'boost_650_turn_off_cron_job_add_on_func'], 10, 1);
		   // Add Cron Job for boost_850_cron_job_add_on
		   add_action('boost_850_cron_job_add_on', [$this, 'boost_850_cron_job_add_on_func'], 10, 1);
		   // Add Cron Job for boost_850_turn_off_cron_job_add_on
		   add_action('boost_850_turn_off_cron_job_add_on', [$this, 'boost_850_turn_off_cron_job_add_on_func'], 10, 1);

			if (is_admin()) {
				$this->admin()->add_wp_hooks();
			}

			// AJAX Hooks
			if ( wp_doing_ajax() ) {
				$this->ajax()->add_wp_hooks();
			}

			if (!is_admin()) {
				$this->frontend()->add_wp_hooks();
			}



	}

	// Set to Schedule the boost_850_turn_off_cron_job after the boost_850_cron_job was executed
	function boost_850_cron_job_func($subscription_id) {
		$time = get_post_meta($subscription_id, "boost_850_time", true);
		if($subscription_id){
			$subscription = wcs_get_subscription($subscription_id);
			if($subscription){
				$product_id = '';
				$order_id = $subscription->get_parent_id();
				$order = wc_get_order($order_id);
				if ($order) {
					  $items = $order->get_items();
					  foreach ($items as $item_id => $item) {
						  $product_id = $item->get_product_id();
					  }
				}
				$time_duration = get_post_meta($product_id,'_boost_850',true);
				if($time_duration){
					$timestamp = date('Y-m-d H:i',strtotime("{$time} + {$time_duration} minutes"));
				}
			}
		}

		$timestamp = strtotime($timestamp);
		if($timestamp){
			as_schedule_single_action($timestamp, 'boost_850_turn_off_cron_job', array('post_id' => $subscription_id));
		}
		update_post_meta($subscription_id,'boost_850_status',"1");
		update_post_meta($subscription_id,'boost_850_schedule_status','active');
	}

	// Trigger function to update the boost_850 post meta after the boost_850_turn_off_cron_job is triggered
	function boost_850_turn_off_cron_job_func($subscription_id) {
		update_post_meta($subscription_id,'boost_850_status',"0");
		delete_post_meta($subscription_id,'boost_850_schedule_status');
	}

	// Set to Schedule the boost_650_turn_off_cron_job after the boost_650_cron_job was executed
	function boost_650_cron_job_func($subscription_id) {
		$time = get_post_meta($subscription_id, "boost_650_time", true);
		if($subscription_id){
			$subscription = wcs_get_subscription($subscription_id);
			if($subscription){
				$product_id = '';
				$order_id = $subscription->get_parent_id();
				$order = wc_get_order($order_id);
				if ($order) {
					  $items = $order->get_items();
					  foreach ($items as $item_id => $item) {
						  $product_id = $item->get_product_id();
					  }
				}
				$time_duration = get_post_meta($product_id,'_boost_600_650',true);
				if($time_duration){
					$timestamp = date('Y-m-d H:i',strtotime("{$time} + {$time_duration} minutes"));
				}
			}
		}

		$timestamp = strtotime($timestamp);
		if($timestamp){
			as_schedule_single_action($timestamp, 'boost_650_turn_off_cron_job', array('post_id' => $subscription_id));
		}
		update_post_meta($subscription_id,'boost_650_status',"1");
		update_post_meta($subscription_id,'boost_650_schedule_status','active');
	}

	// Trigger function to update the boost_650 post meta after the boost_650_turn_off_cron_job is triggered
	function boost_650_turn_off_cron_job_func($subscription_id) {
		// Now you have access to the post ID and the scheduled time
		// You can update post meta or perform other actions here
		update_post_meta($subscription_id,'boost_650_status',"0");
		delete_post_meta($subscription_id,'boost_650_schedule_status');
	}


	//Add on for 850 Boost
	function boost_850_cron_job_add_on_func($subscription_id) {
		$time = get_post_meta($subscription_id, "boost_850_time_add_on", true);
		if($subscription_id){
			$subscription = wcs_get_subscription($subscription_id);
			if($subscription){
				$product_id = '';
				$order_id = $subscription->get_parent_id();
				$order = wc_get_order($order_id);
				if ($order) {
					  $items = $order->get_items();
					  foreach ($items as $item_id => $item) {
						  $product_id = $item->get_product_id();
					  }
				}
				$time_duration = get_post_meta($product_id,'_boost_850',true);
				if($time_duration){
					$timestamp = date('Y-m-d H:i',strtotime("{$time} + {$time_duration} minutes"));
				}
			}
		}

		$timestamp = strtotime($timestamp);
		if($timestamp){
			as_schedule_single_action($timestamp, 'boost_850_turn_off_cron_job_add_on', array('post_id' => $subscription_id));
		}
		update_post_meta($subscription_id,'boost_850_status_add_on',"1");
		update_post_meta($subscription_id,'boost_850_schedule_status_add_on','active');
	}

	// Trigger function to update the boost_850_turn_off_cron_job_add_on post meta after the boost_850_turn_off_cron_job_add_on is triggered
	function boost_850_turn_off_cron_job_add_on_func($subscription_id) {
		// Now you have access to the post ID and the scheduled time
		// You can update post meta or perform other actions here
		update_post_meta($subscription_id,'boost_850_status_add_on',"0");
		delete_post_meta($subscription_id,'boost_850_schedule_status_add_on');
	}

	//Add on for 650 Boost
	function boost_650_cron_job_add_on_func($subscription_id) {
		// Now you have access to the post ID and the scheduled time
		// You can update post meta or perform other actions here

		$time = get_post_meta($subscription_id, "boost_650_time_add_on", true);
		if($subscription_id){
			$subscription = wcs_get_subscription($subscription_id);
			if($subscription){
				$product_id = '';
				$order_id = $subscription->get_parent_id();
				$order = wc_get_order($order_id);
				if ($order) {
					  $items = $order->get_items();
					  foreach ($items as $item_id => $item) {
						  $product_id = $item->get_product_id();
					  }
				}
				$time_duration = get_post_meta($product_id,'_boost_600_650',true);
				if($time_duration){
					$timestamp = date('Y-m-d H:i',strtotime("{$time} + {$time_duration} minutes"));
				}
			}
		}

		$timestamp = strtotime($timestamp);
		if($timestamp){
			as_schedule_single_action($timestamp, 'boost_650_turn_off_cron_job_add_on', array('post_id' => $subscription_id));
		}
		update_post_meta($subscription_id,'boost_650_status_add_on',"1");
		update_post_meta($subscription_id,'boost_650_schedule_status_add_on','active');
	}

	// Trigger function to update the boost_650_turn_off_cron_job_add_on post meta after the boost_650_turn_off_cron_job_add_on is triggered
	function boost_650_turn_off_cron_job_add_on_func($subscription_id) {
		update_post_meta($subscription_id,'boost_650_status_add_on',"0");
		delete_post_meta($subscription_id,'boost_650_schedule_status_add_on');
	}





	// Get Frontend  Class
	function frontend(){
	static $frontend = null;
			if( is_null($frontend) ){
				$frontend = new btn_sm_frontend;
			}
			return $frontend;
	}

	function ajax(){
		static $ajax = null;
			if( is_null($ajax) ){
				$ajax = new btn_sm_ajax;
			}
			return $ajax;
		}

	// Get Admin Class
	function admin(){
	static $admin = null;
			if( is_null($admin) ){
				$admin = new btn_sm_admin;
			}
			return $admin;
	}



	/**
	 * Return templates path checks child theme first
	 *
	 * @param string $filename
	 * @return string template path admin error or false
	*/
	function template_part_path( $filename, $directory_name = '' ){

		$not_found = [];
		$directory_name = $directory_name > '' ? trailingslashit($directory_name) : '';
		$theme_template = "{$directory_name}{$filename}";

		// Locate Template in Themes
		$template = locate_template($theme_template, false);
		// Get Plugin Defaults
		if( ! is_file($template) ){
			$not_found['theme'] = $theme_template;
			$template = BTN_SM_DIR . 'templates/' . $filename;
			if( ! is_file($template) ){
				$not_found['extension'] = $template;
				$template = false;
			}
		}

		$template = apply_filters('btn/post/template/path', $template, $filename, $directory_name);
		if ( ! is_file($template) )	{
			if ( is_admin() ) {
				$notice = __('File not found in any of the following locations :', 'btn-dr');
				$notice .= '<ul>';
				foreach ($not_found as $path) {
					$notice .= "<li>{$path}</li>";
				}
				$notice .= '</ul>';
				return $this->admin_error_msg($notice);
			}
			else{
				return false;
			}
		}
		else{
			return $template;
		}
	}

	// Text Domain
	function load_text_domain(){
		load_plugin_textdomain('btn-dr', false, BTN_SM_DIR . '/languages' );
	}

  // Write Log
  function write_log( $log, $print = false ){
      $error_log = ( is_array( $log ) || is_object( $log ) ) ? print_r( $log, true ) : $log;
      if($print){
          return '<pre>'.$error_log.'</pre>';
      }
      else{
          error_log($error_log);
      }
  }


	function change_shipping_address_text($translated_text, $text, $domain) {

	    if ($text === 'Shipping') {
	        $translated_text = 'FLFE Address';
	    }
		if ($text === 'Customer shipping address' && $domain === 'woocommerce') {
		   $translated_text = 'FLFE Address'; // Change this to your desired text
	   }
	    return $translated_text;
	}





    // Singleton Instance
  private function __construct(){}
	public static function get_instance() {
        static $instance = null;
        if ( is_null( $instance ) ) {
            $instance = new self;
        }
        return $instance;
    }

}

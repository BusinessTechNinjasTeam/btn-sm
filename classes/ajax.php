<?php
if (! defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

/**
 * Frontend Ajax Functions
 *
 * @since      1.0.0
 * @package    btn-sm
 * @subpackage btn-sm/classes
 * @author     Augustus Villanueva <augustus@businesstechninjas.com>
 */
final class btn_sm_ajax{


    // Wordpress Hooks ( Actions & Filters )
    function add_wp_hooks() {

		add_action('wp_ajax_btn_sm_on_off_action',  [$this,'btn_sm_on_off_action']);
		add_action('wp_ajax_nopriv_btn_sm_on_off_action',  [$this,'btn_sm_on_off_action']);

		add_action('wp_ajax_boost_action',  [$this,'boost_action']);
		add_action('wp_ajax_nopriv_boost_action',  [$this,'boost_action']);
    }

	//Convert Time and Date
	function time_date($time_value){
		$today = new DateTime();
		$time = $time_value;
		// Parse the time and set it to today's date
		$dateTime = DateTime::createFromFormat('g:i A', $time);
		$today->setTime((int)$dateTime->format('H'), (int)$dateTime->format('i'));
		return $today->format('Y-m-d H:i');
	}


	// Trigger Schedule event for boost_650_cron_job
	function schedule_boost_650_event($post_id, $scheduled_time) {
	    // Convert scheduled time to a Unix timestamp
	    $timestamp = strtotime($scheduled_time);
	    // Schedule the event if it's not already scheduled
	    // Include post ID and scheduled time as arguments
		as_schedule_single_action($timestamp, 'boost_650_cron_job', array('post_id' => $post_id));
	}

	// Trigger Schedule event for boost_650_cron_job_add_on
	function schedule_boost_650_event_add_on($post_id, $scheduled_time) {
	    // Convert scheduled time to a Unix timestamp
	    $timestamp = strtotime($scheduled_time);
	    // Schedule the event if it's not already scheduled
	    // Include post ID and scheduled time as arguments
		as_schedule_single_action($timestamp, 'boost_650_cron_job_add_on', array('post_id' => $post_id));
	}

	// Trigger Schedule event for boost_850_cron_job_add_on
	function schedule_boost_850_event_add_on($post_id, $scheduled_time) {
	    // Convert scheduled time to a Unix timestamp
	    $timestamp = strtotime($scheduled_time);
	    // Schedule the event if it's not already scheduled
	    // Include post ID and scheduled time as arguments
		as_schedule_single_action($timestamp, 'boost_850_cron_job_add_on', array('post_id' => $post_id));
	}

	// Trigger Schedule event for boost_850_cron_job
	function schedule_boost_850_event($post_id, $scheduled_time) {
	    // Convert scheduled time to a Unix timestamp
	    $timestamp = strtotime($scheduled_time);
	    // Schedule the event if it's not already scheduled
	    // Include post ID and scheduled time as arguments
		as_schedule_single_action($timestamp, 'boost_850_cron_job', array('post_id' => $post_id));
	}

	// Ajax Call for Boost Action
	function boost_action(){
		$value = isset($_POST['value'] )? $_POST['value']  : false;
		$boost = isset($_POST['boost'] )? $_POST['boost']  : false;
		$subscription_id = isset($_POST['id'] )? $_POST['id']  : false;
		if($boost == 'boost_650'){
			$date_time = $this->time_date($value);
			$this->schedule_boost_650_event($subscription_id, $date_time);
			update_post_meta($subscription_id,'boost_650_time',$date_time);
			update_post_meta($subscription_id,'boost_650_schedule_status','schedule');
			wp_send_json_success("Event scheduled for post ID $subscription_id at $date_time");
		}
		if($boost == 'boost_850'){
			$date_time = $this->time_date($value);
			$this->schedule_boost_850_event($subscription_id, $date_time);
			update_post_meta($subscription_id,'boost_850_time',$date_time);
			update_post_meta($subscription_id,'boost_850_schedule_status','schedule');
			wp_send_json_success("Event scheduled for post ID $subscription_id at $date_time");
		}

		if($boost == 'boost_650_add_on'){
			$date_time = $this->time_date($value);
			$this->schedule_boost_650_event_add_on($subscription_id, $date_time);
			update_post_meta($subscription_id,'boost_650_time_add_on',$date_time);
			update_post_meta($subscription_id,'boost_650_schedule_status_add_on','schedule');
			wp_send_json_success("Event scheduled for post ID $subscription_id at $date_time");
		}

		if($boost == 'boost_850_add_on'){
			$date_time = $this->time_date($value);
			$this->schedule_boost_850_event_add_on($subscription_id, $date_time);
			update_post_meta($subscription_id,'boost_850_time_add_on',$date_time);
			update_post_meta($subscription_id,'boost_850_schedule_status_add_on','schedule');
			wp_send_json_success("Event scheduled for post ID $subscription_id at $date_time");
		}
	}

	// Ajax Call for On and Off functionality found in subscription management page
	function btn_sm_on_off_action(){
		$checked = isset($_POST['checked'] )? $_POST['checked']  : false;
		$subscription_id = isset($_POST['data_id']) ? $_POST['data_id'] : false;

		if($checked == 1){
			 update_post_meta($subscription_id,"subscription_on_off",1);
		}
		else{
			 update_post_meta($subscription_id,"subscription_on_off",0);
		}
		 wp_send_json_success();
	}



	    function __construct(){}
	}

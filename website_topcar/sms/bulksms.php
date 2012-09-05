<?php
/************************************************************\
* php-bulksms version 1.0, modified 28-Aug-05
* By Liam Hatton
* liam@hatton.name http://dl.liam.hatton.name/
/************************************************************\
* This library is free software; you can redistribute it
* and/or modify it under the terms of the GNU Lesser General
* Public License as published by the Free Software
* Foundation; either version 2.1 of the  License, or (at your
* option) any later version.
*
* This library is distributed in the hope that it will be
* useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
* PURPOSE.  See the GNU Lesser General Public License for
* more details.
*
* You should have received a copy of the GNU Lesser General
* Public License along with this library; if not, write to the
* Free Software Foundation, Inc., 59 Temple Place, Suite 330,
* Boston, MA  02111-1307 USA
/************************************************************\
* Please see readme.htm for more information and documentation.
* If your copy did not come with this file, please download the
* original at: http://dl.liam.hatton.name
/************************************************************\
* PLEASE CHANGE THE FOLLOWING BEFORE RUNNING THIS SCRIPT.
\************************************************************/

// BulkSMS account details
define('BULKSMS_USERNAME', 'username');
define('BULKSMS_PASSWORD', 'password');

// You need to uncomment the relevant line for the country
// your BulkSMS account is registered in. If you select
// the wrong one, your username and password will not
// work.

// International (for all other countries):
// define('BULKSMS_HOST','bulksms.vsms.net');

// UK:
// define('BULKSMS_HOST','www.bulksms.co.uk');

// USA:
// define('BULKSMS_HOST','usa.bulksms.com');

// South Africa:
// define('BULKSMS_HOST','bulksms.2way.co.za');

// Spain:
// define('BULKSMS_HOST','bulksms.com.es');

/************************************************************\
* Optional parameters, do not need to be changed.
\************************************************************/

// Your country code (for number formatting functions), leave
// as 0 if you do not want to specify this.
define('COUNTRY_CODE', '0');

// Set this option to true if you want to send requests
// to the EAPI using port 80 instead of 5567/7512. You may
// find it necessary to set this if you are behind a firewall
// that blocks outgoing connections using non-standard ports.
// It is best to leave this alone unless you absolutely need
// this feature, because non-standard ports are used to avoid
// transparent proxies (which can cause lots of problems with
// the EAPI).
define('USE_PORT_80', false);








/************************************************************\
* CODE STARTS HERE
* NOTHING CAN BE EDITED BEYOND HERE.
\************************************************************/
// Error code consonants
define('SUCCESS', 1);
define('FATAL', -1);
define('RETRY', -2);
define('INPUT_ERR', -3);
define('NO_MATCH', -66);

// Incoming message type code consonants
define('AUTO', 0);
define('STATUS', 1);
define('INBOX', 2);

// EAPI status codes
define('EAPI_IN_PROGRESS', 0);
define('EAPI_SUCCESS', 0);
define('EAPI_SCHEDULED', 1);
define('EAPI_DELIVERED_UPSTREAM', 10);
define('EAPI_DELIVERED_TO_MOBILE', 11);
define('EAPI_UPSTREAM_UNACK', 12);
define('ERR_EAPI_FATAL', 22);
define('ERR_EAPI_AUTH_ERR', 23);
define('ERR_EAPI_INPUT_ERR', 24);
define('ERR_EAPI_NO_CREDITS', 25);
define('ERR_EAPI_NO_UP_CREDITS', 26);
define('ERR_EAPI_EXCEEDED_QUOTA', 27);
define('ERR_EAPI_EXCEEDED_UP_QUOTA', 28);
define('ERR_EAPI_SENDING_CANCELLED', 29);
define('ERR_EAPI_UNAVAIL', 40);
define('ERR_EAPI_DELIVERY_FAIL', 50);
define('ERR_EAPI_DELIVERY_PHONE_FAIL', 51);
define('ERR_EAPI_DELIVERY_NET_FAIL', 52);
define('ERR_EAPI_MSG_EXPIRED', 53);
define('ERR_EAPI_TRANSIENT_UP_FAIL', 60);
define('EAPI_UPSTREAM_STATUS_UPDATE', 61);
define('ERR_EAPI_UPSTREAM_STATUS_CANCEL', 62);
define('ERR_EAPI_MSG_EXPIRED', 70);
define('ERR_EAPI_UNKNOWN', 70);
define('NO_EAPI_STATUS_CODE', -99);

class bulksms {
	var $_handler;
	var $_response;
	var $_eapi_status_code;
	var $_eapi_status_msg;
	var $_batch_id;
	var $_debug;
	var $_quotation;
	var $_num_list = array();
	var $_queue = array();
	var $_queue_id = 0;
	var $_incoming = array();

	/************************************************************\
	* SEND_SMS: Send a short text message via BulkSMS's eapi.
	\************************************************************/
	function send_sms($vars, $quote = false) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$this->_push_debug_msg("called send_sms");
		$host = BULKSMS_HOST.":5567";
		if($quote == true) {
			$this->_push_debug_msg("instructed to quote_sms");
			$uri = "/eapi/submission/quote_sms/2/2.0";
		} else
		$uri = "/eapi/submission/send_sms/2/2.0";
		unset($vars["username"]); unset($vars["password"]);
		$vars2 = array("username" => BULKSMS_USERNAME,
		"password" => BULKSMS_PASSWORD);
		$vars = array_merge($vars2, $vars);
		unset($vars2);
		if($vars["message"] == NULL || ($vars["msisdn"] == NULL && $vars["dest_group_id"] == NULL)) {
			$this->_push_debug_msg("missing required fields");
			$this->_handler = INPUT_ERR;
			return $this->get_status();
		}
		$response = $this->_post_eapi($host, $uri, $vars);
		if(!$response) return $this->get_status();
		$this->_push_debug_msg("sent to _post_eapi without errors");
		$this->_parse_eapi_status($response);
		return $this->get_status();
	}
	/************************************************************\
	* GET_CREDITS: Get number of credits remaining in your
	* account.
	\************************************************************/
	function get_credits() {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$host = BULKSMS_HOST.":7512";
		$uri = "/eapi/1.0/get_credits.mc";
		$this->_push_debug_msg("called get_credits");
		$vars = array("username" => BULKSMS_USERNAME,
		"password" => BULKSMS_PASSWORD);
		$response = $this->_post_eapi($host, $uri, $vars);
		if(!$response) return $this->get_status();
		$this->_push_debug_msg("sent to _post_eapi without errors");
		if(count($response) == 1){
			$this->_push_debug_msg("we got a response: ".$response[0]);
			$this->_response = $response[0];
			$this->_handler = SUCCESS;
			} else {
			$this->_push_debug_msg("we got something else, send to _parse_eapi_status");
			$this->_parse_eapi_status($response);
		}
		return $this->get_status();
	}
	/************************************************************\
	* GET_STATUS_REPORT: Get status information for a particular
	* message from BulkSMS.
	\************************************************************/
	function get_status_report($vars) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$host = BULKSMS_HOST.":5567";
		$uri = "/eapi/status_reports/get_report/2/2.0";
		$this->_push_debug_msg("called get_status_report");
		unset($vars["username"]); unset($vars["password"]);
		$vars2 = array("username" => BULKSMS_USERNAME,
		"password" => BULKSMS_PASSWORD);
		$vars = array_merge($vars2, $vars);
		unset($vars2);
		$retr = $this->_post_eapi($host, $uri, $vars);
		if(count($retr) == 0) return $this->get_status();
		if(!$retr[0][0] == 0) {
			$this->_push_debug_msg("we got an error, send to _parse_eapi_status");
			$this->_parse_eapi_status($retr);
			return $this->get_status();
		};
		$this->_parse_eapi_status($retr[0]);
		unset($retr[0]);
		$this->_response = $retr;
		return $this->get_status();
	}
	/************************************************************\
	* GET_INBOX: Get your BulkSMS inbox.
	\************************************************************/
	function get_inbox($vars = array("last_retrieved_id" => 0)) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$host = BULKSMS_HOST.":5567";
		$uri = "/eapi/reception/get_inbox/1/1.0";
		$this->_push_debug_msg("called get_inbox");
		unset($vars["username"]); unset($vars["password"]);
		$vars2 = array("username" => BULKSMS_USERNAME,
		"password" => BULKSMS_PASSWORD);
		$vars = array_merge($vars2, $vars);
		unset($vars2);
		$retr = $this->_post_eapi($host, $uri, $vars);
		if(count($retr) == 0) return $this->get_status();
		if((!$retr[0][0] == 0)) {
			$this->_push_debug_msg("we got an error, send to _parse_eapi_status");
			$this->_parse_eapi_status($retr);
			return $this->get_status();
		};
		$this->_parse_eapi_status($retr[0]);
		if($retr[0][2] == "0") {
			$this->_push_debug_msg("we got no records");
			} else {
			unset($retr[0]);
			$this->_response = $retr;
		}
		return $this->get_status();
	}
	/************************************************************\
	* GET_NUMBER_LIST: Returns your phone number list in an
	* array.
	\************************************************************/
	function get_number_list() {
		$this->_push_debug_msg("called get_number_list");
		return $this->_num_list;
	}
	/************************************************************\
	* ADD_NUMBER_TO_LIST: Add number to the list.
	\************************************************************/
	function add_number_to_list($num) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$this->_push_debug_msg("called add_number_to_list with num: ".$num);
		array_push($this->_num_list, $num);
		$this->_handler = SUCCESS;
		return $this->get_status();
	}
	/************************************************************\
	* DEL_NUMBER_FROM_LIST: Delete number from the list.
	\************************************************************/
	function del_number_from_list($num) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$this->_push_debug_msg("called del_number_from_list with num: ".$num);
		$key = array_search($num, $this->_num_list);
		if($key === false) {
			$this->_push_debug_msg("this number is not in the list");
			$this->_handler = INPUT_ERR;
			return $this->get_status();
		}
		unset($this->_num_list[$key]);
		$this->_handler = SUCCESS;
		return $this->get_status();
	}
	/************************************************************\
	* CLEAR_LIST: Clears the list.
	\************************************************************/
	function clear_list() {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$this->_push_debug_msg("called clear_list");
		unset($this->_num_list);
		$this->_num_list = array();
		$this->_handler = SUCCESS;
		return $this->get_status();
	}
	/************************************************************\
	* SEND_TO_LIST: Send an SMS to the list.
	\************************************************************/
	function send_to_list($vars, $quote = false, $remove_dups = false, $split = NULL) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$this->_push_debug_msg("called send_to_list");
		unset($vars["username"]); unset($vars["password"]);
		if($remove_dups == true) {
			$this->_push_debug_msg("removing duplicate numbers");
			$this->_num_list = array_unique($this->_num_list);
		}
		if(count($this->_num_list) > 5000) {
			$this->_push_debug_msg("list is too big (above 5000), so we need to split it");
			$split = 5000;
		}
		if(!$split == NULL) {
			$this->_push_debug_msg("splitting arrays into chunks of ".$split);
			$number_lists = array_chunk($this->_num_list, $split);
			$status = array();
			$eapi_status_code = array();
			$eapi_status_msg = array();
			$quotation = array();
			$batch_id = array();
			$this->_push_debug_msg("processing number of chunks: ".count($number_lists));
			foreach($number_lists as $number_list) {
				$var = $vars;
				$var["msisdn"] = implode(',',$number_list);
				$this->send_sms($var, $quote);
				array_push($status, $this->_handler);
				array_push($eapi_status_code, $this->_eapi_status_code);
				array_push($eapi_status_msg, $this->_eapi_status_msg);
				array_push($quotation, $this->_quotation);
				array_push($batch_id, $this->_batch_id);
			}
			$this->_handler = $status;
			$this->_eapi_status_code = $eapi_status_code;
			$this->_eapi_status_msg = $eapi_status_msg;
			$this->_quotation = $quotation;
			$this->_batch_id = $batch_id;
			return $this->get_status();
			} else {
			$this->_push_debug_msg("processing number list of: ".count($this->_num_list));
			$vars["msisdn"] = implode(',',$this->_num_list);
			return $this->send_sms($vars, $quote);
		}
	}
	/************************************************************\
	* ADD_TO_PUBLIC_GROUP: Adds a phone number to a public group.
	\************************************************************/
	function add_to_public_group($group_id, $msisdn, $firstname = NULL, $lastname = NULL, $fixnumber = true) {
		$this->_push_debug_msg("called add_to_public_group");
		$hack_success_url = "http://success-add-to-group.localhost/";
		$hack_failure_url = "http://failure-add-to-group.localhost/";
		$host = BULKSMS_HOST.":5567";
		$uri = "/eapi/1.0/phonebook/public_add_member";
		if($group_id == "" || $msisdn == "") {
			$this->_push_debug_msg("missing required fields, returning input_err");
			return INPUT_ERR;
		}
		if($fixnumber == true) {
			$this->_push_debug_msg("fixnumber set to true");
			$msisdn = $this->fix_number($msisdn);
			} else {
			$this->_push_debug_msg("fixnumber set to false");
			if($this->check_number($msisdn) == false) {
				$this->_push_debug_msg("number invalid, returning input_err");
				return INPUT_ERR;
			} else $this->_push_debug_msg("number okay");
		}
		$vars = array("group_id" => $group_id,
		"msisdn" => $msisdn,
		"given_name" => $firstname,
		"surname" => $lastname,
		"success_url" => $hack_success_url,
		"fail_url" => $hack_failure_url);
		$this->_post_public_group($host,$uri,$vars);
		return $this->get_status();
	}
	/************************************************************\
	* DEL_FROM_PUBLIC_GROUP: Removes a phone number from a public
	* group.
	\************************************************************/
	function del_from_public_group($group_id, $msisdn, $fixnumber = true) {
		$this->_push_debug_msg("called del_from_public_group");
		$hack_success_url = "http://success-add-to-group.localhost/";
		$hack_failure_url = "http://failure-add-to-group.localhost/";
		$host = BULKSMS_HOST.":5567";
		$uri = "/eapi/1.0/phonebook/public_remove_member";
		if($group_id == "" || $msisdn == "") {
			$this->_push_debug_msg("missing required fields, returning input_err");
			return INPUT_ERR;
		}
		if($fixnumber == true) {
			$this->_push_debug_msg("fixnumber set to true");
			$msisdn = $this->fix_number($msisdn);
			} else {
			$this->_push_debug_msg("fixnumber set to false");
			if($this->check_number($msisdn) == false) {
				$this->_push_debug_msg("number invalid, returning input_err");
				return INPUT_ERR;
			} else $this->_push_debug_msg("number okay");
		}
		$vars = array("group_id" => $group_id,
		"msisdn" => $msisdn,
		"success_url" => $hack_success_url,
		"fail_url" => $hack_failure_url);
		$this->_post_public_group($host,$uri,$vars);
		return $this->get_status();
	}
	/************************************************************\
	* LIST2GROUP: Exports number list to a public group.
	\************************************************************/
	function list2group($group_id) {
		$this->_push_debug_msg("called list2group with: ".$group_id);
		if(count($this->_num_list) == 0) {
			$this->_push_debug_msg("empty list, returning INPUT_ERR");
			return INPUT_ERR;
		}
		$list = $this->_num_list;
		$this->_push_debug_msg("removing duplicate numbers");
		$this->_num_list = array_unique($list);
		$status = array();
		foreach($list as $i) {
			$this->_push_debug_msg("running iteration of add_to_public_group for: ".$i);
			$this->add_to_public_group($group_id, $i, NULL, NULL, false);
			array_push($status,$this->_handler);
		}
		$this->_handler = $status;
		return $this->get_status();
	}
	/************************************************************\
	* PUSH_SMS_TO_QUEUE: Add a message to the queue. Returns a
	* unique ID.
	\************************************************************/
	function push_sms_to_queue($vars) {
		$this->_push_debug_msg("called push_sms_to_queue");
		unset($vars["username"]); unset($vars["password"]);
		$this->_queue_id++;
		$vars["id"] = $this->_queue_id;
		array_push($this->_queue, $vars);
		$this->_handler = SUCCESS;
		$this->_push_debug_msg("returning id: ".$vars["id"]);
		return $vars["id"];
	}
	/************************************************************\
	* DEL_SMS_FROM_QUEUE: Deletes a message from the queue,
	* defined by the ID returned by push_sms_to_queue.
	\************************************************************/
	function del_sms_from_queue($id) {
		$this->_push_debug_msg("called del_sms_from_queue with id: ".$id);
		for($i = 0; $i < count($this->_queue); $i++) {
			if($this->_queue[$i]["id"] == $id) {
				$key = $i;
				break;
			}
		}
		if(isset($key) == false) {
			$this->_push_debug_msg("this id is not in the list");
			$this->_handler = INPUT_ERR;
			return $this->get_status();
		}
		unset($this->_queue[$key]);
		$this->_handler = SUCCESS;
		return $this->get_status();

	}
	/************************************************************\
	* CLEAR_QUEUE: Erases SMS queue.
	\************************************************************/
	function clear_queue() {
		$this->_push_debug_msg("called clear_queue");
		$this->_queue = array();
		$this->_handler = SUCCESS;
		return $this->get_status();
	}
	/************************************************************\
	* PROCESS_QUEUE: Processes SMS queue.
	\************************************************************/
	function process_queue($vars = NULL) {
		$this->_push_debug_msg("called process_queue");
		$csv = $this->get_queue();
		$csv = $this->_build_queue_csv($csv);
		if(strlen($csv) == 0) {
			$this->_push_debug_msg("too short, perhaps no messages in queue");
			$this->_handler = INPUT_ERR;
			return $this->get_status();
		}
		$this->_push_debug_msg("generated csv: ".$csv);
		if($vars != NULL) {
			$this->_push_debug_msg("extra vars: ".count($vars));
		}
		return $this->_process_csv_batch_sms($csv, $vars);
	}
	/************************************************************\
	* GET_QUEUE: Returns SMS queue.
	\************************************************************/
	function get_queue() {
		$this->_push_debug_msg("called get_queue");
		$this->_handler = SUCCESS;
		return $this->_queue;
	}
	/************************************************************\
	* LOAD_INCOMING_VARS: Load an incoming object pushed to your
	* server by BulkSMS. When you are using this in a script,
	* you should not output anything.
	\************************************************************/
	function load_incoming_vars($password, $type = AUTO) {
		$this->_push_debug_msg("called load_incoming_vars");

		// --- Found at http://au.php.net/header ---
		// Date in the past
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);

		// HTTP/1.0
		header("Pragma: no-cache");
		// ------

		if(count($_GET) == 0) {
			$this->_push_debug_msg("there are no HTTP get vars");
			$this->_handler = FATAL;
			echo 0;
			return $this->get_status();
		}
		if($password != $_GET['pass']) {
			$this->_push_debug_msg("the password does not match");
			$this->_handler = FATAL;
			echo 0;
			return $this->get_status();
		}
		if($type == AUTO) {
			$this->_push_debug_msg("auto type guessing");
			if($_GET['status'] == "") {
				if(!$_GET['msisdn'] == "") {
					$this->_push_debug_msg("appears to be inbox: ".INBOX);
					$type = INBOX;
				} else {
					$this->_push_debug_msg("cannot recognise this message type");
					$this->_handler = FATAL;
					echo 0;
					return $this->get_status();
				}
			} else {
				$this->_push_debug_msg("appears to be status msg: ".STATUS);
				$type = STATUS;
			}
		} else $this->_push_debug_msg("status provided as code: ".$type);
		$arr = $_GET;
		unset($arr['pass']);
		switch($type) {
			case INBOX:
				$arr['incoming_type'] = INBOX;
				break;
			case STATUS:
				$arr['incoming_type'] = STATUS;
				break;
		}
		$this->_incoming = $arr;
		$this->_handler = SUCCESS;
		echo 1;
		return $this->get_status();
	}
	/************************************************************\
	* GET_INCOMING_OBJ: Get incoming push object as array.
	\************************************************************/
	function get_incoming_obj() {
		$this->_push_debug_msg("called get_incoming_obj");
		return $this->_incoming;
	}
	/************************************************************\
	* GET_STATUS: Get script status information for an operation.
	\************************************************************/
	function get_status() {
		$this->_push_debug_msg("called get_status, returned: ".$this->_handler);
		return $this->_handler;
	}
	/************************************************************\
	* GET_EAPI_STATUS_CODE: Get EAPI status code.
	\************************************************************/
	function get_eapi_status_code() {
		$this->_push_debug_msg("called get_eapi_status_code, returned: ".$this->_eapi_status_code);
		return $this->_eapi_status_code;
	}
	/************************************************************\
	* GET_EAPI_STATUS_MSG: Get EAPI status message.
	\************************************************************/
	function get_eapi_status_msg() {
		$this->_push_debug_msg("called get_eapi_status_msg, returned: ".$this->_eapi_status_msg);
		return $this->_eapi_status_msg;
	}
	/************************************************************\
	* GET_BATCH_ID: Get batch id for a message (sent using
	* BulkSMS).
	\************************************************************/
	function get_batch_id() {
		$this->_push_debug_msg("called get_batch_id, returned: ".$this->_batch_id);
		return $this->_batch_id;
	}
	/************************************************************\
	* GET_RESPONSE: Get response from BulkSMS.
	\************************************************************/
	function get_response() {
		$this->_push_debug_msg("called get_response, returned: ".$this->_response);
		return $this->_response;
	}
	/************************************************************\
	* GET_QUOTATION: Get a quotation (price check) for sending
	* an SMS (must use SendSMS with $quote = true first).
	\************************************************************/
	function get_quotation() {
		$this->_push_debug_msg("called get_quote, returned: ".$this->_quotation);
		return $this->_quotation;
	}
	/************************************************************\
	* FIX_NUMBER: Formats a mobile phone number so it is in a
	* suitable format, although it is best to require the user
	* to provide the number in the correct format.
	\************************************************************/
	function fix_number($num, $tr_letters = true) {
		$this->_push_debug_msg("called fix_number with: ".$num);
		// List of common IDD codes used worldwide
		$idd = array('0011', '011', '00');
		// Start parsing now:
		// Strip out anything that isn't a letter or number
		$num = preg_replace('/[^\w]*/', '', $num);
		if($tr_letters == true) {
			// Set to translate letters to numbers (default setting)
			$this->_push_debug_msg("translating letters to numbers");
			// List of numbers associated to characters on a telephone keypad, we fix these for completeness.
			$letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
			'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
			'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
			$new_letters = array('2','2','2','3','3','3','4','4','4','5','5','5','6','6','6','7','7','7','7',
			'8','8','8','9','9','9','9',
			'2','2','2','3','3','3','4','4','4','5','5','5','6','6','6','7','7','7','7',
			'8','8','8','9','9','9','9');
			// Replace letters with number equivalents
			$num = str_replace($letters, $new_letters, $num);
			} else {
			// Not set to translate letters to numbers
			if(preg_match('/\D/', $num)) {
				$this->_push_debug_msg("found a letter in the input, returning false");
				return false;
				} else {
				$this->_push_debug_msg("no letters - looks okay");
			}
		}
		// Strip out all IDD access codes
		foreach($idd as $code) $num = preg_replace('/\A'.$code.'/','',$num);
		// fix leading 0
		if(!COUNTRY_CODE == 0) $num = preg_replace('/\A0/', COUNTRY_CODE, $num);
		// Return result
		$this->_push_debug_msg("fix_number returned: ".$num);
		return $num;
	}
	/************************************************************\
	* CHECK_NUMBER: Check a number to see if it is in the correct
	* format.
	\************************************************************/
	function check_number($num) {
		$this->_push_debug_msg("called check_number with: ".$num);
		if(preg_match('/\D/', $num)) return false;
		if(preg_match('/\A0/', $num)) return false;
		if(strlen($num) < 8) return false;
		$this->_push_debug_msg("number is in correct format, returning true");
		return true;
	}
	/************************************************************\
	* INTERNAL FUNCTIONS, NOT TO BE ACCESSED DIRECTLY.
	\************************************************************/
	function _post_eapi($host, $uri, $vars) {
		$this->_push_debug_msg("called _post_eapi");
		if($host == NULL || $uri == NULL || $vars == NULL || BULKSMS_HOST == "BULKSMS_HOST") {
			if(!BULKSMS_HOST == "BULKSMS_HOST") {
				$this->_push_debug_msg("empty vars sent");
			} else {
				$this->_push_debug_msg("please define country host at the top of this script");
			}
			$this->_handler = FATAL;
			return NULL;
		}
		include_once('http.inc');
		$this->_push_debug_msg("posting to http");
		$http = new http;
		preg_match("/(.*):(\d*)/", $host, $u);
		$http->host = $u[1];
		if(USE_PORT_80 == true) { $http->port = 80; } else { $http->port = $u[2]; }
		$this->_push_debug_msg("host: ".$http->host);
		$this->_push_debug_msg("port: ".$http->port);
		$status = $http->post($uri, $vars);
		if($status == HTTP_STATUS_OK) {
			$response = $http->get_response_body();
			$http->disconnect();
			unset($http);
			$this->_push_debug_msg("received from bulksms: ".$response);
			if(preg_match('/^0\|Results to follow\n\n/', $response)||preg_match('/^0\|records to follow\|\d*\n\n/', $response)) {
				$eapi1 = array();
				$eapi2 = array();
				preg_match('/^(.*)\n\n/', $response, $r);
				$eapi1[0] = explode('|', $r[1]);
				$response = preg_replace("/^.*\n\n/", "", $response);
				$eapi2 = explode('\n', $response);
				foreach($eapi2 as $e) {
					array_push($eapi1, explode('|', $e));
				}
				unset($eapi2);
				$this->_push_debug_msg("sent to first eapi status parse handler");
				return $eapi1;
				} else {
				$this->_push_debug_msg("sent to second eapi status parse handler");
				return explode('|', $response);
			}
			} else {
			$http->disconnect();
			unset($http);
			$this->_push_debug_msg("http error: ".$status);
			$this->_handler = RETRY;
			return NULL;
		}
	}
	function _parse_eapi_status($vars) {
		$this->_push_debug_msg("called _parse_eapi_status");
		if((count($vars) == 3 || count($vars) == 2) && $vars[0] < 99) {
			$this->_eapi_status_code = $vars[0];
			if($vars[0] == 40 || $vars[0] == 26 || $vars[0] == 28) {
				$this->_handler = RETRY;
				} elseif($vars[0] == 0 || $vars[0] == 1) {
				$this->_handler = SUCCESS;
			} else $this->_handler = FATAL;
			$this->_eapi_status_msg = trim($vars[1]);
			if($vars[1] == "Quotation issued") {
				$this->_quotation = $vars[2];
				} else {
				$this->_batch_id = $vars[2];
			}
			$this->_push_debug_msg("eapi said: ".$this->_eapi_status_msg);
			} else {
			$this->_push_debug_msg("eapi said something we could not understand");
			$this->_handler = FATAL;
			$this->_eapi_status_code = NO_EAPI_STATUS_CODE;
		}
	}
	function _get_debug_msgs() {
		$this->_push_debug_msg("called _get_debug_msgs");
		return $this->_debug;
	}
	function _push_debug_msg($msg) {
		$this->_debug .= $msg."\n";
	}

	function _post_public_group($host, $uri, $vars) {
		$this->_push_debug_msg("called _post_public_group");
		if($host == NULL || $uri == NULL || $vars == NULL || BULKSMS_HOST == "BULKSMS_HOST") {
			if(!BULKSMS_HOST == "BULKSMS_HOST") {
				$this->_push_debug_msg("empty vars sent");
			} else {
				$this->_push_debug_msg("please define country host at the top of this script");
			}
			$this->_handler = FATAL;
			return NULL;
		}
		include_once('http.inc');
		$this->_push_debug_msg("posting to http");
		$http = new http;
		preg_match("/(.*):(\d*)/", $host, $u);
		$http->host = $u[1];
		$http->port = $u[2];
		$this->_push_debug_msg("host: ".$http->host);
		$this->_push_debug_msg("port: ".$http->port);
		$status = $http->post($uri, $vars, false);
		if($status == HTTP_STATUS_FOUND) {
			$this->_push_debug_msg("http response: ".$status);
			$response = $http->get_response();
			$this->_push_debug_msg("location: ".$response->_headers['Location']);
			switch($response->_headers['Location']) {
				case $vars["success_url"]:
				$this->_handler = SUCCESS;
				$this->_push_debug_msg("operation returned success");
				break;
				case $vars["fail_url"]:
				$this->_handler = FATAL;
				$this->_push_debug_msg("operation returned fail");
				break;
				default:
				$this->_handler = FATAL;
				$this->_push_debug_msg("unexpected result");
				break;
			}
			$http->disconnect();
			unset($http);
			} else {
			$http->disconnect();
			unset($http);
			$this->_push_debug_msg("http error: ".$status);
			$this->_handler = RETRY;
		}
		return NULL;
	}
	function _build_queue_csv($queue) {
		$this->_push_debug_msg("called _build_queue_csv with ".count($queue)." records");
		$list = array();
		foreach($queue as $i) {
			$keys = array_keys($i);
			foreach($keys as $key) {
			array_push($list, $key);
			}
		}
		$this->_push_debug_msg("found ".count($list)." item names (including id column)");
		$list = array_unique($list);
		$key = array_search('id', $list);
		if($key !== false) unset($list[$key]);
		$this->_push_debug_msg("found ".count($list)." unique item names (excl. id column)");
		$csv = "";
		foreach($list as $i) {
			$csv .= addslashes($i).',';
			$cnt++;
		}
		$csv = rtrim($csv, ",");
		$csv .= "\n";
		foreach($queue as $i) {
			$line = "";
			foreach($list as $name) {
				if(strlen($i[$name]) == 1 || $i[$name] == "") {
				$line .= addslashes($i[$name]).',';
				} else {
				$line .= '"'.addslashes($i[$name]).'",';
				}
			}
			$line = rtrim($line, ",");
			$csv .= $line."\n";
		}
		$this->_push_debug_msg("returning csv of ".strlen($csv)." characters");
		return $csv;
	}
	function _process_csv_batch_sms($csv, $vars2 = NULL) {
		$this->_handler = NULL;
		$this->_eapi_status_code = NULL;
		$this->_response = NULL;
		$host = BULKSMS_HOST.":5567";
		$uri = "/eapi/submission/send_batch/1/1.0";
		$this->_push_debug_msg("called _process_csv_batch_sms");
		$vars = array("username" => BULKSMS_USERNAME,
					  "password" => BULKSMS_PASSWORD,
					  "batch_data" => $csv);
		$vars = array_merge($vars, $vars2);
		$response = $this->_post_eapi($host, $uri, $vars);
		if(!$response) return $this->get_status();
		$this->_push_debug_msg("sent to _post_eapi without errors");
		$this->_parse_eapi_status($response);
		return $this->get_status();
	}
}
?>
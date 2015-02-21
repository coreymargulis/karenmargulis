<?php

class Pugs_API_Endpoint {

	/** Hook WordPress
	* 	@return void
	*/
	public function __construct() {
		add_action('parse_request', array($this, 'sniff_requests'), 0);
	}
		
	/** Sniff Requests
	* 	This is where we hijack all API requests
	* 	@return die if API request
	*/
	public function sniff_requests($query) {
		if (isset($query->query_vars['shipment'])) {
			$this->handle_request();
			exit;
		}
	}
	
	/** Handle Requests
	* 	@return void
	*/
	protected function handle_request() {

		global $wp;
		global $wpdb;

		$pugs = $wp->query_vars['pugs'];
		$values = file_get_contents('php://input');
		$output = json_decode($values, true);
		
		//Store the values of shipped order which we are getting from ShippingEasy.
		$id = $output['shipment']['orders'][0]['external_order_identifier'];
		$shipping_id = $output['shipment']['id'];
		$tracking_number = $output['shipment']['tracking_number'];
		$carrier_key = $output['shipment']['carrier_key'];
		$carrier_service_key = $output['shipment']['carrier_service_key'];
		$shipment_cost_cents = $output['shipment']['shipment_cost'];
		$shipment_cost = ($shipment_cost_cents / 100);
		$line_subtotal = 0;
		$total_tax = 0;
		$cart_discount = 0;
		$order_discount = 0;
		
		$comment_update = 'Shipping Tracking Number: ' . $tracking_number . '<br/> Carrier Key: ' . $carrier_key . '<br/> Carrier Service Key: ' . $carrier_service_key . '<br/> Cost: ' . $shipment_cost;
		
		$order = new WC_Order($id);
		$order->update_status('completed');
		$order->add_order_note($comment_update);

		$this->send_response('Order has been updated successfully ' . $comment_update, json_decode($pugs));

	}
	
	/** Response Handler
	* 	This sends a JSON response to the browser
	*/
	protected function send_response($msg, $pugs = '') {
		$response['message'] = $msg;
		header('content-type: application/json; charset=utf-8');
		echo json_encode($response) . "\n";
		exit;
	}

}

?>
<?php

class MochilaFMS {
	
	private $url_base;
	private $apikey;
	private $client_id;
	public $last_error;
	
	function  __construct($url_base,$apikey,$client_id) {
		$this->url_base = $url_base;
		$this->apikey = $apikey;
		$this->client_id = $client_id;
	}
	
	function do_post($endpoint,$body) {
		$json_response =  file_get_contents(
			$this->url_base.$endpoint,
			false,
			stream_context_create(
				array(
					'http' => array(
						'method'  => 'POST',
						'content' => json_encode($body),
						'header'=>  "Content-Type: application/json\r\n"."Accept: application/json\r\n"
					)
				)
			)
		);
		
		if($json_response===false) {
			$this->last_error = var_export(error_get_last(),true);
			return false;
		}
		
		$response = json_decode($json_response,true);
		if(!$response['success']) {
			$this->last_error = $response['error'];
			return false;
		}
		unset($response['success']);
		if(count($response)==0) {
			return true;
		}
		return $response;
		
	}
	
	function simulate_create_item($item_data) {
		return $this->do_post("item/simulate_create",array(
			"apikey"=>$this->apikey,
			"client_id"=>$this->client_id,
			"item_data"=>$item_data
		));
	}
	
	function notify_inbound_inventory($inbound_container_id,$items) {
		return $this->do_post("inventory/notify_inbound",array(
			"apikey"=>$this->apikey,
			"client_id"=>$this->client_id,
			"truck_id"=>$inbound_container_id,
			"items"=>$items
		));
	}
	
	function simulate_inbound_receive($inbound_container_id) {
		return $this->do_post("inventory/simulate_inbound_receive",array(
			"apikey"=>$this->apikey,
			"client_id"=>$this->client_id,
			"truck_id"=>$inbound_container_id
		));
	}
	
	function simulate_ship($fr_id) {
		return $this->do_post("request/simulate_ship",array(
			"apikey"=>$this->apikey,
			"client_id"=>$this->client_id,
			"fr_id"=>$fr_id
		));
	}
	
	function request_fulfillment(MochilaFR $fulfillment_request) {
		$fulfillment_request->apikey = $this->apikey;
		$fulfillment_request->client_id = $this->client_id;
		return $this->do_post("request/create",$fulfillment_request);
	}
	
	function get_shipment_confirmations() {
		return $this->do_post("shipment/get_confirmed",array("apikey"=>$this->apikey,"client_id"=>$this->client_id));
	}
	
}

class MochilaLineItem {
	public $item_id;
	public $quantity;
	
	function __construct($item_id,$quantity)  {
			$this->item_id = $item_id;
			$this->quantity = $quantity;
	}
}

class MochilaSet {
	public $set_type;
	public $card_data;
	public $items;
	
	function __construct($set_type,$card_data=null) {
		$this->set_type = $set_type;
		$this->card_data = $card_data;
		$items = array();
	}
	
	function add_line_item(MochilaLineItem $item) {
		$this->items[] = $item;
	}
}


class MochilaFR {
	public $version = 3;
	public $fulfillment_request_id;
	public $request;
	
    function __construct($fulfillment_request_id,$request_data)  {	
		$this->fulfillment_request_id = $fulfillment_request_id;
		$this->request = $request_data;
		$this->request['sets'] = array();
	}
	
	function add_set(MochilaSet $set) {
		$this->request['sets'][] = $set;
	}	
}
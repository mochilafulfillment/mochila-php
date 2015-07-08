<?php

include 'mochila-php.php';
//this is a file where the real credentials are stored in the global variables 
//$HOST, $CLIENT_ID, and $APIKEY
//it is not included in version control
include 'credentials.php';

//not real credentials obviously
$fms = new MochilaFMS(
	"http://$HOST/api/v2/",
	$APIKEY,
	$CLIENT_ID
);

$test_item_upc = uniqid();
$item_response = $fms->simulate_create_item(array(
	"sku"=>"ABC-123",
	"upc"=>$test_item_upc,
	"item_name"=>"Test Item",
	"hazmat"=>0,
	"sioc"=>0,
	"width"=>6.1,
	"length"=>7.2,
	"height"=>8.3,
	"weight"=>5.4,
	"product_code"=>"ABC-123",
	"is_composite"=>0,
	"item_value"=>101.00,
	"category"=>"Toys",
	"subcategory"=>"Wooden",
	"short_name"=>"ABC",
	"is_package"=>0,
	"tariff_code"=>"1111.2222",
	"customs_description"=>"Handmade Wooden Childrens Toy",
	"customs_origin"=>"CN",
	"requires_expiration"=>0
));
if($item_response===false) {
	echo "Error in simulating item creation: ".$fms->last_error."\n";
	exit();
} else {
	echo "Simulate create item response: ".var_export($item_response,true)."\n";
}

sleep(3);

$truck_id = uniqid();
$inbound_response = $fms->notify_inbound_inventory($truck_id,array(
	array("upc"=>$test_item_upc,"quantity"=>2)
));
if($inbound_response===false) {
	echo "Error in inbound notification: ".$fms->last_error."\n";
	exit();
} else {
	echo "Inbound notification response: ".var_export($inbound_response,true)."\n";
}

sleep(3);
	
$receive_response = $fms->simulate_inbound_receive($truck_id);
if($receive_response===false) {
	echo "Error in receive: ".$fms->last_error."\n";
	exit();
} else {
	echo "Simulate inbound receive response: ".var_export($receive_response,true)."\n";
}	

sleep(3);

$item_available_response = $fms->item_available_quantity($test_item_upc);
if($item_available_response===false) {
	echo "Error in receive: ".$fms->last_error."\n";
	exit();
} else {
	echo "Item available quantity response: ".var_export($item_available_response,true)."\n";
}


$available_quantities_response = $fms->get_available_quantities();
if($available_quantities_response===false) {
	echo "Error in getting available quantities: ".$fms->last_error."\n";
	exit();
} else {
	echo "All available quantities response: ".var_export($available_quantities_response,true)."\n";
}


$test_fr_id = uniqid();
$fr = new MochilaFR(
	$test_fr_id,
	array(
        "order_id"=>$test_fr_id,         
        "ship_to_name"=>"JOHN DOE",
        "ship_to_company_name"=>"",
        "ship_to_address_1"=>"70 Sewell St",
        "ship_to_address_2"=>"Unit I",
        "ship_to_address_3"=>"",
        "ship_to_city"=>"Glassboro",
        "ship_to_state"=>"NJ",
        "ship_to_postalcode"=>"08028",
        "ship_to_country"=>"US",
        "ship_to_phone"=>"555-555-5555",
        "ship_to_email"=>"",
        "sat_delivery"=>0,
        "ship_profile_id"=>"MBH1",
        "transfer_out"=>0,
        "reference"=>"",
        "ship_method"=>"STANDARD_OVERNIGHT",
        "shipment_type"=>"default_shipment_type"
	)
);

$set = new MochilaSet('default_set_type');
$set->add_line_item(new MochilaLineItem($test_item_upc,2));
$fr->add_set($set);

$fr_response = $fms->request_fulfillment($fr);
if($fr_response===false) {
	echo "Error in making fulfillment request: ".$fms->last_error."\n";
	exit();
} else {
	echo "Fulfillment request response: ".var_export($fr_response,true)."\n";
}

sleep(7);

$ship_response = $fms->simulate_ship($test_fr_id);
if($ship_response===false) {
	echo "Error in ship: ".$fms->last_error."\n";
	exit();
} else {
	echo "Simulate ship response: ".var_export($ship_response,true)."\n";
}

$tracking_response = $fms->get_shipment_confirmations();
if($tracking_response===false) {
	echo "Error in making tracking request: ".$fms->last_error."\n";
	exit();
} else {
	echo "Get shipment confirmations response: ".var_export($tracking_response,true)."\n";
}

$status_response = $fms->get_request_status($test_fr_id);
if($status_response===false) {
	echo "Error in getting status: ".$fms->last_error."\n";
	exit();
} else {
	echo "Get status response: ".var_export($status_response,true)."\n";
}
<?php

include 'mochila-php.php';

//not real credentials obviously
$fms = new MochilaFMS("http://localhost/api/v2/","57af9013153ca42fe07a11eab3f57249","23276197821269171");

$fr = new MochilaFR(
	'20150410A',
	array(
        "order_id"=>"745321",         
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
$set->add_line_item(new MochilaLineItem('43219999999010114',2));
$set->add_line_item(new MochilaLineItem('43213333333010114',3));
$fr->add_set($set);

$fr_response = $fms->request_fulfillment($fr);
if($fr_response===false) {
	echo "Error in making fulfillment request: ".$fms->last_error."\n";
} else {
	var_dump($fr_response);
}

$tracking_response = $fms->get_shipment_confirmations();
if($tracking_response===false) {
	echo "Error in making tracking request: ".$fms->last_error."\n";
} else {
	var_dump($tracking_response);
}





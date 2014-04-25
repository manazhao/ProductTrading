<?php
   /* 
   * By Qi Zhao (manazhao@soe.ucsc.edu)
   * April 16th 2014
   */

   /**
   * IMPORTANT: this file is still under construction, please do NOT run it.
   */

   echo "no ready yet, please try later\n";
   exit(1);

   # parse the command line arguments to get the token
   if(count($argv) != 2){
      echo "please supply the client token through the command line\n";
      exit(1);
   }
   # include the TradingClient class
   require_once "./TradingClient.php";
   $localMode = true;
   $token = $argv[1];

   # create a TradingClient object and it will be used all through the trading
   # process
   $client = new TradingClient($token,$localMode);
   $aLotEqual = "=================";
   # dump the trading client information
   print "$aLotEqual client information $aLotEqual\n";
   $client->print_client_info();

   $trading_is_over = false;
   while(!$trading_is_over){
      #  check the trading clock
      $trading_clock = $client->query_trading_clock();
      #  whether it's a product offering or referring round
      $is_offer_round = $trading_clock["rnd.a"];
      if($is_offer_round){
	 $other_groups = $client->get_other_groups();
	 # retrieve my products for sale
	 $my_products = $client->query_my_product_info();
	 $products_for_sale = $my_products["to.sell"];
	 # offer products to 3 groups
	 for($i = 0; $i < 3; $i++){
	    $recipient = $other_groups[$i];
	    $product = $products_for_sale[$i];
	    $cost = $product["cost"];
	    $product_id = $product["id"];
	    $price = $cost * 1.1;
	    $first_ref_fee = ($price - $cost) * 0.2;
	    $second_ref_fee = ($price - $cost) * 0.1;
	    $response = $client->offer_product($recipient, $product_id, $price, $first_ref_fee, $second_ref_fee);
	    if($response["status"] == "fail"){
	       print_r($response);
	    }
	 }
	 # refer products if there are any
	 # check the incoming transactions
	 $in_transactions = $client->query_in_transactions();
	 $pending_offers = $in_transactions["offers"]["pending"];
	 $pending_referrals = $in_transactions["referrals"]["pending"];
	 # refer unneeded products to other group
	 # track number of referrals made
	 $num_referrals = 0;
	 foreach($pending_offers as $offer){
	    $product_id = $offer["product.id"];
	    $from_group = $offer["from.id"];
	    # check whether I need this product

	 }

      }

   }


   # get product information
   $product_info = $client->query_product_info();
   print "$aLotEqual product information $aLotEqual\n";
   print_r($product_info);

   # check trading clock
   $trading_clock = $client->query_trading_clock();
   print "$aLotEqual trading clock $aLotEqual\n";
   print_r($trading_clock);

   # query my product info
   $my_products = $client->query_my_product_info();
   print "$aLotEqual my product information $aLotEqual\n";
   print_r($my_products);

   # query my incoming transactions
   $my_in_transactions = $client->query_in_transactions();
   print "$aLotEqual in transactions $aLotEqual\n";
   print_r($my_in_transactions);

   # query my outgoing transactions
   $my_out_transactions = $client->query_out_transactions();
   print "$aLotEqual out transactions $aLotEqual\n";
   print_r($my_out_transactions);

   # offer a product to another group
   $my_to_sell_products = $my_products["to.sell"];
   $trading_clock = $client->query_trading_clock();
   # wait until product offering round
   while(!$trading_clock["rnd.a"]){
      sleep(5);
      $trading_clock = $client->query_trading_clock();
      print_r($trading_clock);
   }
   print "$aLotEqual start to offer/refer products $aLotEqual\n";
   $other_groups = $client->get_other_groups();
   $offer_recipient = $other_groups[0];
   for($i = 0; $i < 5; $i++){
      $product = $my_to_sell_products[$i];
      $product_id = $product["id"];
      $cost = $product["cost"];
      $price = $cost * 1.1;
      $first_ref_fee = ($price - $cost) * 0.2;
      $second_ref_fee = ($price - $cost) * 0.1;
      $response = $client->offer_product($offer_recipient,$product_id, $price, $first_ref_fee, $second_ref_fee);
      print_r($response);
   }

?>

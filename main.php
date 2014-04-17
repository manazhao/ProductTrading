<?php
   /* 
   * By Qi Zhao (manazhao@soe.ucsc.edu)
   * April 16th 2014
   */

   # include the TradingClient class
   require_once "./TradingClient.php";

   #########################################################################
   ###################### example queries to the server ####################

   # please obtain a valid token from TA and keep it secrete from other groups
   $token = "21119d6646e095287547854c15479a57";

   # create a TradingClient object and it will be used all through the trading
   # process
   $client = new TradingClient($token);

   # dump the trading client information
   $client->print_client_info();

   # get product information
   $product_info = $client->query_product_info();

   # dump product information
   print_r($product_info);
?>

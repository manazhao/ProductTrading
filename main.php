<?php
   # include the TradingClient class
   require_once "./TradingClient.php";

   #########################################################################
   ###################### start of service access ##########################

   # create a TradingClient object and it will be used all through the trading
   # process
   # please obtain a valid token from TA and keep it secrete from other groups
   $token = "c14fab9081a7ac1f903faebcab7d6310";
   $client = new TradingClient($token);
   # dump the trading client information
   $client->print_client_info();
   # get product information
   $product_info = $client->query_product_info();
   # dump product information
   print_r($product_info);
?>

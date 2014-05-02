<?php
   /* 
   * By Qi Zhao (manazhao@soe.ucsc.edu)
   * April 16th 2014
   */

   if(count($argv) < 2){
      print("token should be provided as the first command line eargument\n");
      exit(1);
   }

   # include the TradingClient class
   require_once "./TradingClient.php";
   #########################################################################
   ###################### example queries to the server ####################

   # please obtain a valid token from TA and keep it secrete from other groups
   $localMode = true;
   $token = $argv[1];

   # create a TradingClient object and it will be used all through the trading
   # process
   $client = new TradingClient($token,$localMode);
   $aLotEqual = "=================";
   $client->print_client_info();

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

?>

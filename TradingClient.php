<?php
   /**
   * By Qi Zhao (manazhao@soe.ucsc.edu)
   * April 16th 2014
   *
   * @brief TradingClient which is able to talk to the server
   *
   * the client can make queries to server using cURL module. 
   * we support the following queries:
   * Note: the response from server is a string in JSON format. For your convenience, the JSON string is decoded into a php associate object so that each field 
   *	of the response can be easily accessed. Please refer to each service query method for detailed documentation of the response format.
   *
   * 1) experiment setting. This will be done automatically in the constructor. The client will try to verify its identiy with a token which is assigned by 
   * 	administrator. If verification goes successful, the response from the server should contain basic information about the experiment setting. e.g. number
   *	of groups, number of products and the group number corresponding to the token.
   * 2) product information. Each group produces a number of products that are needed by other group, at the same time, it acquires products which are produced
   *	by another group. Each group can only see the production cost of the products it produces, and the utility of the products it acquires.  
   */
   class TradingClient { 
      # curl instance	
      protected $root_url = "http://128.114.63.97/index.php/service/";
      # service urls
      protected $query_setting_url = "querySetting";
      protected $query_product_url = "queryProductInfo";
      protected $query_clock_url = "queryClock";
      protected $offer_product_url = "offerProduct";
      protected $refer_product_url ="referProduct";
      protected $query_my_product_info_url = "queryMyProductInfo";
      protected $query_in_transaction_url = "queryInTransactions";
      protected $query_out_transaction_url = "queryOutTransactions";
      protected $accept_offer_url = "acceptOffer";
      # token identify a group. It is assigned by administrator and should be kept secretely. 
      protected $token = null;
      # your group id. It is unique for each group and can be retrieved from server with the token
      protected $group_id = null;
      # number of groups in the market
      protected $num_groups = null;
      # other groups
      protected $other_groups = null;
      # number of products in the market
      protected $num_products = null;

      /**
      * @brief encode post arguments as string.
      */
      private function array_to_post_string($postFields) {
	 $postFieldStr = "";
	 $i = 0;
	 foreach ( $postFields as $name => $value ) {
	    $postFieldStr .= (($i == 0 ? "" : "&") . $name . "=" . $value);
	    $i ++;
	 }
	 return $postFieldStr;
      }

      /**
      * @brief dump client information. Make sure the TradingClient is properly setup
      *
      */
      public function print_client_info(){
	 $info_arr = array("group_id" => $this->group_id, "num_products" => $this->num_products, "num_groups" => $this->num_groups);
	 print_r($info_arr);
      }

      public function get_group_id(){
	 return $this->group_id;
      }

      public function get_other_groups(){
	 return $this->other_groups;
      }
      /**
      * @brief execute service query.
      *
      * Note: this function is used internally, so you don't need to understand how it works
      *
      *	@argument $service_url url of the service to be accessed
      * @argument $post_fields arguments for the post
      * @return response as JSON string
      */
      private function execute($service_url, $post_fields = array()){
	 # initialize the server session
	 $url = $this->root_url . $service_url;
	 $post_fields["token"] = $this->token;
	 $ch = curl_init($url);
	 # client agent
	 curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6" );
	 # time out of the request
	 curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 $post_str = $this->array_to_post_string($post_fields);
	 curl_setopt($ch,CURLOPT_POSTFIELDS,$post_str);
	 $response = curl_exec($ch);
	 curl_close($ch);
	 return $response;
      }

      /**
      * @brief construct a TradingClient
      * 
      * Identity verification will take place with the provided token. If verification succeeds, experimental setting will be returned, error messages received
      * otherwise.
      *
      * @argument $token a token which is assigned by administrator
      */
      public function __construct($token, $localMode = false){
	 if($localMode){
	    $this->root_url = "http://tim211.ucsc.edu/frontend_dev.php/service/";
	 }
	 $this->token = $token;
	 $response = $this->execute($this->query_setting_url);
	 if(!$response){
	    throw new Exception("error in connecting to server");
	 }
	 # check the status
	 $response_obj = json_decode($response,true);
	 if($response_obj["status"] == "fail"){
	    throw new Exception($response_obj["message"]);
	 }
	 $this->num_groups = $response_obj["num_groups"];
	 $this->num_products = $response_obj["num_products"];
	 $this->group_id = $response_obj["group_id"];
	 $this->other_groups = array();
	 for($i = 1; $i <= $this->num_groups; $i++){
	    if($i != $this->group_id){
	       $this->other_groups[] = $i;
	    }
	 }
      }

      /**
      * @brief query product information
      * 
      */
      public function query_product_info(){
	 $response = $this->execute($this->query_product_url);
	 if(!$response){
	    throw new Exception("error in connecting to server");
	 }
	 return json_decode($response,true);
      }

      public function query_trading_clock(){
	 $response = $this->execute($this->query_clock_url);
	 if(!$response){
	    throw new Exception("error in connecting to server");
	 }
	 return json_decode($response,true);
      }

      public function offer_product($to_group, $product_id, $price, $first_ref_fee, $second_ref_fee){
	 $response = $this->execute($this->offer_product_url,array("recipient" => $to_group, "product" => $product_id, "price" => $price,
	 "firstRefFee" => $first_ref_fee, "secondRefFee" => $second_ref_fee));
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }

      public function refer_product($to_group, $transaction_id){
	 $response = $this->execute($this->refer_product_url,array("recipient" => $to_group, "transactionId" => $transaction_id));
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }

      public function accept_offer($transaction_id){
	 $response = $this->execute($this->accept_offer_url,array("transactionId" => $transaction_id));
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }

      public function query_my_product_info(){
	 $response = $this->execute($this->query_my_product_info_url);
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }

      public function query_in_transactions(){
	 $response = $this->execute($this->query_in_transaction_url);
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }

      public function query_out_transactions(){
	 $response = $this->execute($this->query_out_transaction_url);
	 if(!$response){
	    throw new Exception("error in communication");
	 }
	 return json_decode($response,true);
      }
   }
?>

<?php

class SpiderController{

	public $host;
	public $userName;
	public $userPass;
	public $dbName;
	public $link;
	public $dbPrifix = 'ps_';

	function __construct($host, $userName, $userPass, $dbName){
		$this->host = $host;
		$this->userName = $userName;
		$this->userPass = $userPass;
		$this->dbName = $dbName;
		$this->initDb();
	}

	private function initDb(){
		$this->link = mysql_connect($this->host, $this->userName, $this->userPass);
		mysql_select_db($this->dbName, $this->link);
	}

	public function getOrderInfo(){
		if(isset($_POST))
			$post = $_POST;

		if(!$this->authUCode($post['uCode']))
			return FALSE;
	}

	public function getOrderNumber($startTime, $endTime){
		$where = ' where id_order_state = 2 ';

		if($startTime){
			$startTime = date('Y-m-d H:m:s', $startTime - 3600 * 6);
			$where .= ' and date_add >= \'' . $startTime . '\'';
		}

		if($endTime){
			$endTime = date('Y-m-d H:m:s', $endTime - 3600 * 6);
			$where .= ' and date_add <= \'' . $endTime . '\'';
		}

		$sql = 'select id_order from ' . $this->dbPrifix . 'order_history ' . $where;
		$result = mysql_query($sql, $this->link);

		$orderNumber = array();
		while($row = mysql_fetch_assoc($result)){
			$orderNumber[] = $row;
		}
		mysql_free_result($result);

		$xml = '<?xml version=\'1.0\' encoding=\'utf-8\'?>' . chr(13);
		$xml = $xml . '<OrderList>' . chr(13);
		$xml = $xml . '<OrderCount>' . count($orderNumber) . '</OrderCount>' . chr(13);
		foreach($orderNumber as $row){
			$xml = $xml . '<OrderNO>' . $this->formatOrderId($row['id_order']) . '</OrderNo>' . chr(13);
		}

		$xml = $xml . '</OrderList>' . chr(13);

		echo $xml;
	}

	public function getOrderDetail($orderId){
		$queryOrderId = $this->deformatOrderId($orderId);
		$order = array();
		$sqlOrderBasic = '
			SELECT o.invoice_date,a.firstname,a.lastname,l.`name` as country,s.`name` as province,a.city,a.address1,a.postcode,t.email,a.phone_mobile,a.phone,o.total_products,o.total_shipping,o.payment
			FROM ' . $this->dbPrifix . 'orders AS o, ' . $this->dbPrifix . 'address AS a, ' . $this->dbPrifix . 'state AS s,' . $this->dbPrifix . 'country AS c, ' . $this->dbPrifix . 'country_lang AS l , ' . $this->dbPrifix . 'customer as t
			WHERE o.id_address_delivery = a.id_address AND a.id_state = s.id_state AND a.id_country = c.id_country AND c.id_country = l.id_country and a.id_customer =t.email AND  l.id_lang=1 AND o.id_order = ' . $queryOrderId . ';';

		$resultOrderBasic = mysql_query($sqlOrderBasic, $this->link);
		while($row = mysql_fetch_assoc($resultOrderBasic)){
			$order['invoice_date'] = $row['invoice_date'];
			$order['firstname'] = $row['firstname'];
			$order['lastname'] = $row['lastname'];
			$order['country'] = $row['country'];
			$order['province'] = $row['province'];
			$order['city'] = $row['city'];
			$order['address1'] = $row['address1'];
			$order['postcode'] = $row['postcode'];
			$order['email'] = $row['email'];
			$order['phone_mobile'] = $row['phone_mobile'];
			$order['phone'] = $row['phone'];
			$order['total_products'] = $row['total_products'];
			$order['total_shipping'] = $row['total_shipping'];
			$order['payment'] = $row['payment'];
		}

		mysql_free_result($resultOrderBasic);

		$goods = array();
		$sqlGoods = 'select product_reference,product_name,product_price,product_quantity,product_id
			from ps_order_detail
			where id_order = ' . $queryOrderId . ';';

		$resultGoods = mysql_query($sqlGoods, $this->link);

		while($row = mysql_fetch_assoc($resultGoods)){
			$goods[] = $row;
		}
		mysql_free_result($resultGoods);

		$xml = '<?xml version=\'1.0\' encoding=\'utf-8\'?>' . chr(13);
		$xml = $xml . '<Order>' . chr(13);
		$xml = $xml . '<Ver>1.0</Ver>' . chr(13);
		$xml = $xml . '<OrderNO>' . $orderId . '</OrderNO>' . chr(13);
		$xml = $xml . '<DateTime>'.substr( $order['invoice_date'],0,-3).'</DateTime>' . chr(13);
		$xml = $xml . '<BuyerID>' . $order['email'] . '</BuyerID>' . chr(13);
		$xml = $xml . '<BuyerName>' . $order['firstname'] . ' ' . $order['lastname'] . '</BuyerName>' . chr(13);
		$xml = $xml . '<Country>' . $order['country'] . '</Country>' . chr(13);
		$xml = $xml . '<Province>' . $order['province'] . '</Province>' . chr(13);
		$xml = $xml . '<City>' . $order['city'] . '</City>' . chr(13);
		$xml = $xml . '<Town></Town>' . chr(13);
		$xml = $xml . '<Adr>' . $order['address1'] . '</Adr>' . chr(13);
		$xml = $xml . '<Zip>' . $order['postcode'] . '</Zip>' . chr(13);
		$xml = $xml . '<Email>' . $order['email'] . '</Email>' . chr(13);
		$xml = $xml . '<Phone>' . $order['phone'] . ' ' . $order['phone_mobile'] . '</Phone>' . chr(13);
		$xml = $xml . '<Total>' . $order['total_products'] . '</Total>' . chr(13);
		$xml = $xml . '<Postage>' . $order['total_shipping'] . '</Postage>' . chr(13);
		$xml = $xml . '<PayAccount>' . $order['payment'] . '</PayAccount>' . chr(13);
		$xml = $xml . '<PayID></PayID>' . chr(13);
		$xml = $xml . '<LogisticsName>freeshipping</LogisticsName>' . chr(13);
		$xml = $xml . '<Chargetype></Chargetype>' . chr(13);
		$xml = $xml . '<CustomerRemark></CustomerRemark>' . chr(13);
		$xml = $xml . '<Remark></Remark>' . chr(13);

		foreach($goods as $good){
			$xml = $xml . '<Item>' . chr(13);
			if($good['product_reference']){
				$xml = $xml . '<GoodsID>' . $good['product_reference'] . '</GoodsID>' . chr(13);
			} else{
				$xml = $xml . '<GoodsID>' . $good['product_id'] . '</GoodsID>' . chr(13);
			}
			$xml = $xml . '<GoodsName>' . $good['product_name'] . '</GoodsName>' . chr(13);
			$xml = $xml . '<GoodsSpec></GoodsSpec>' . chr(13);
			$xml = $xml . '<Price>' . $good['product_price'] . '</Price>' . chr(13);
			$xml = $xml . '<Count>' . $good['product_quantity'] . '</Count>' . chr(13);
			$xml = $xml . '</Item>' . chr(13);
		}

		$xml = $xml . '</Order>' . chr(13);

		echo $xml;
	}

	private function deformatOrderId($orderId){
		return substr($orderId, 8);
	}

	private function formatOrderId($orderId){
		return 'DV' . date('ymd') . $orderId;
	}

	private function authUCode($uCode){
		if($uCode == '51375124')
			return true;
		return false;
	}

	public function __destruct(){
		if($this->link)
			mysql_close($this->link);
	}

}

//echo time('2012-09-10 05:45:10');
$spider = new SpiderController('localhost', 'root', 'zhuwenhua', 'prestashopnew');

//$spider->getOrderDetail('DV1211102');
if($_POST['mType'] == 'mOrderSearch'){
	$spider->getOrderNumber(1340327411, 1352529616);
} else if($_GET['mType'] == 'mGetOrder'){
	$spider->gerOrderDetail($_GET['OrderNO']);
}


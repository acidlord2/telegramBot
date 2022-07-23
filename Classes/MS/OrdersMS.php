<?php
/**
 *
 * @class OrdersMS
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class OrdersMS
{
	private $log;
	private $apiMSClass;

	private $cache = array ();

	public function __construct()
	{
		$this->log = new Log('Classes - MS - OrdersMS.log');
		$this->apiMSClass = new APIMS();
	}	
	/**
	* function findOrders - function find ms orders by ms filter passed
	*
	* @filters string - ms filter 
	* @return array - result as array of orders
	*/
	public function findOrders($filters)
    {
		$orders = array();
		$offset = 0;
		$this->log->write (__LINE__ . ' findOrders.filters - ' . json_encode ($filters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

		$filter = '';
		if (is_array($filters)) {
		    foreach ($filters as $key => $value)
		        $filter .= $key . '=' . $value . ';';
		}
		else {
		    $filter = $filters;
		}
		
		while (true)
		{
			
			$url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '?filter=' . $filter . '&limit=' . MS_LIMIT . '&offset=' . $offset;
			$this->log->write (__LINE__ . ' findOrders.url - ' . $url);
			$response_orders = $this->apiMSClass->getData($url);
			$offset += MS_LIMIT;
			$orders = array_merge ($orders, $response_orders['rows']);
			if ($offset >= $response_orders['meta']['size'])
				break;			
		}

		$this->log->write (__LINE__ . ' findOrders.orders - ' . json_encode ($orders, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $orders;
	}
	
	/**
	 * function getOrder - function returns order info
	 *
	 * @param id - order id
	 * @return array - result order info
	 */
	public function getOrder($id)
	{
	    $url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' . $id;
        $this->log->write (__LINE__ . ' getOrder.url - ' . $url);
        $order = $this->apiMSClass->getData($url);
	    $this->log->write (__LINE__ . ' getOrder.order - ' . json_encode ($order, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	    return $order;
	}
	
	/**
	 * function getOrder - function creates or updates customer order
	 *
	 * @param data - orders data
	 * @return array - result orders created or updated
	 */
	public function createUpdateOrders($data)
	{
	    $this->log->write(__LINE__ . ' createUpdateCustomerorders.data - ' . json_encode ($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		
		$url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER;
		$return = $this->apiMSClass->postData ($url, $data);
		$this->log->write(__LINE__ . ' createCustomerorder.return - ' . json_encode ($return, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $return;
	}
// 	public function updateCustomerorder($id, $data)
// 	{
// 		$this->logger->write("01-updateCustomerorder.data - " . json_encode ($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		
// 		$service_url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' . $id;
// 		$return = $this->apiMSClass->putData ($service_url, $data);
// 		$this->logger->write("02-updateCustomerorder.return - " . json_encode ($return, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
// 		return $return;
// 		//$logger->write("curl_response - " . $curl_response);
		
// 	}
}

?>
<?php
/**
 *
 * @class DeliveryTypeMS
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class DeliveryTypeMS
{
	private $log;
	private $apiMSClass;
	private $id;
	private $name;
	
	public function __construct($id, $name)
	{
		$this->log = new Log('Classes - MS - DeliveryTypeMS.log');
		$this->apiMSClass = new APIMS();
		$this->id = $id;
		$this->name = $name;
	}	

	/**
	 * function getDeliveryTypes - returns all delivery types
	 *
	 * @return array - result as array of delivery types
	 */
	public static function getDeliveryTypes()
	{
	    $log = new Log('Classes - MS - DeliveryTypeMS.log');
	    $apiMSClass = new APIMS();
	    $url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERENTITY . '/' . MS_SHIPTYPE_REF_ID;
	    $log->write (__LINE__ . ' getDrivers.url - ' . $url);
	    $deliveryTypes = $apiMSClass->getData($url);
	    $log->write (__LINE__ . ' getDrivers.drivers - ' . json_encode ($deliveryTypes['rows'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	    $return = array();
	    
	    foreach ($deliveryTypes['rows'] as $deliveryType) {
	        $return[] = new DeliveryTypeMS($deliveryType['id'], $deliveryType['name']);
	    }
	    return $return;
	}

	/**
	 * function findDeliveryTypeById - returns delivery type by id
	 *
	 * @return array - result as delivery type
	 */
	public static function findDeliveryTypeById($id)
	{
	    $deliveryTypes = self::getDrivers();
	    foreach ($deliveryTypes as $deliveryType)
	    {
	        if ($deliveryType->id == $id)
	            return $deliveryType;
	    }
	}
	/**
	 * function getName - returns delivery type name
	 *
	 * @return string - delivery type name
	 */
	public function getName()
	{
        return $this->name;
	}
	/**
	 * function getId - returns delivery type id
	 *
	 * @return string - delivery type id
	 */
	public function getId()
	{
	    return $this->id;
	}
}

?>
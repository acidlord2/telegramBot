<?php
/**
 *
 * @class OrderMessage
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class OrderMessage
{
    private $phone;
    private $deliveryAddress;
    private $orderNumber;
    private $sum;
    private $sumMarketplace;
    private $paymentType;
    private $description;
    private $project;
    
    public function __construct($order)
    {
        $this->orderNumber = $order['name'];
        $this->sum = $order['sum'] / 100;
        if (isset($order['description']))
            $this->description = $order['description'];
        
        $attributes = array_column($order['attributes'], 'id');
        $deliveryAddressKey = array_search(MS_ADDRESS_ATTR, $attributes);
        $phoneKey = array_search(MS_PHONE_ATTR, $attributes);
        $paymentTypeKey = array_search(MS_PAYMENTTYPE_ATTR, $attributes);
        $sumMarketplaceKey = array_search(MS_MPAMOUNT_ATTR, $attributes);

        if ($deliveryAddressKey !== false) {
            $this->deliveryAddress = $order['attributes'][$deliveryAddressKey]['value'];
        }
        if ($phoneKey !== false) {
            $this->phone = str_replace(' ', '', $order['attributes'][$phoneKey]['value']);
        }
        if ($paymentTypeKey !== false) {
            
            $this->paymentType = $order['attributes'][$paymentTypeKey]['value']['meta']['href'];
        }
        if ($sumMarketplaceKey !== false) {
            
            $this->sumMarketplace = $order['attributes'][$sumMarketplaceKey]['value'];
        }
        if (isset ($order['project']))
        {
            $apiMSClass = new APIMS();
            $this->project = $apiMSClass->getData($order['project']['meta']['href']);
        }
        
    }
    
    /*
     * returns order info in message
     *
     * @return OrderMessage
     */
    public function getMessage()
    {
        $return = '';
        $return = 'Заказ № <b>' . $this->orderNumber . '</b>';
        if (isset($this->deliveryAddress)) {
            $return .= PHP_EOL . $this->deliveryAddress;
        }
        if (isset($this->phone)) {
            $return .= PHP_EOL . (strpos($this->phone, '+') === 0 ? $this->phone : '+' . $this->phone);
        }
        if (isset($this->paymentType) ? APIMS::getIdFromHref($this->paymentType) == MS_PAYMENTTYPE_CASH_ID : false) {
            $return .= PHP_EOL . 'Сумма к оплате: <b>' . (isset($this->sumMarketplace) ? $this->sumMarketplace : $this->sum)  . '</b> руб.';
        }
        elseif (isset($this->paymentType) ? APIMS::getIdFromHref($this->paymentType) != MS_PAYMENTTYPE_CASH_ID : false ) {
            $return .= PHP_EOL . '<b>Заказ оплачен картой</b>';
        }
        else
        {
            $return .= PHP_EOL . 'Сумма к оплате: <b>' . $this->sum . '</b> руб.';
        }
        if (isset($this->project)) {
            $return .= PHP_EOL . 'Проект: <b>' . $this->project['name'] . '</b>';
        }
        if (isset($this->description)) {
            $return .= PHP_EOL . 'Комментарий: <b>' . $this->description . '</b>';
        }
        return $return;
    }
}
?>
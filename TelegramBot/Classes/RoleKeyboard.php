<?php
/**
 *
 * @class RoleKeyboard
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class RoleKeyboard
{
    private $keyboards;
    private $roleCode;
    private $keyboardType;
    private $log;
    
    public function __construct($roleCode, $keyboardType, $context = '', $addContext = '')
    {
        $this->log = new Log('TelegramBot - Classes - RoleKeyboard.log');
        $this->log->write(__LINE__ . ' roleCode - ' . $roleCode);
        $this->log->write(__LINE__ . ' keyboardType - ' . $keyboardType);
        $this->log->write(__LINE__ . ' context - ' . $context);
        $this->log->write(__LINE__ . ' addContext - ' . $addContext);
        $this->keyboards = array();
        $this->roleCode = $roleCode;
        $this->keyboardType = $keyboardType;
        if ($roleCode == 'driver' && $keyboardType == 'keyboard')
        {
            $this->keyboards[] = array(0, 0, GET_ALL_NEW_ORDERS);
            $this->keyboards[] = array(0, 1, GET_ALL_DELIVERED_ORDERS);
            $this->keyboards[] = array(1, 0, GET_ALL_CANCELLED_ORDERS);
            $this->keyboards[] = array(1, 1, GET_ALL_RESCHEDULED_ORDERS);
        }
        if ($roleCode == 'manager' && $keyboardType == 'keyboard')
        {
            $this->keyboards[] = array(0, 0, GET_ALL_DRIVERS);
            $this->keyboards[] = array(1, 0, ADD_DRIVER);
            $this->keyboards[] = array(1, 1, DELETE_DRIVER);
        }
        if ($roleCode == 'driver' && $keyboardType == 'inlineKeyboard' && $context == 'newOrder')
        {
            $this->keyboards[] = array(0, 0, 'Доставлен', 'deliver:' . $addContext);
            $this->keyboards[] = array(0, 1, 'Отменен', 'cancel:' . $addContext);
            $this->keyboards[] = array(1, 0, 'Перенесен','transfer:' . $addContext);
            $this->keyboards[] = array(1, 1, 'Недозвон','recall:' . $addContext);
        }
        
        if ($keyboardType == 'inlineKeyboard' && $context == 'deliveriedOrder')
        {
            $this->keyboards[] = array(0, 0, 'Вернуть в доставку', 'shipped:' . $addContext);
            $this->keyboards[] = array(0, 1, 'Отменен', 'cancel:' . $addContext);
        }
//         if ($keyboardType == 'inlineKeyboard' && $context == 'cancelledOrder')
//         {
//             $this->keyboards[] = array(0, 0, 'Вернуть в доставку','shipped:' . $addContext);
//             $this->keyboards[] = array(0, 1, 'Доставлен','deliver:' . $addContext);
//         }
        if ($keyboardType == 'inlineKeyboard' && $context == 'transferredOrder')
        {
            $this->keyboards[] = array(0, 0, 'Вернуть в доставку','shipped:' . $addContext);
            $this->keyboards[] = array(0, 1, 'Доставлен','deliver:' . $addContext);
            $this->keyboards[] = array(0, 2, 'Отменен','cancel:' . $addContext);
        }
        if ($keyboardType == 'inlineKeyboard' && $context == 'driver')
        {
            $this->keyboards[] = array(0, 0, 'Изменить', 'editDriver:' . $addContext);
            $this->keyboards[] = array(0, 1, 'Удалить', 'deleteDriver:' . $addContext);
        }
        
        if ($keyboardType == 'inlineKeyboard' && $context == 'yesNo')
        {
            $this->keyboards[] = array(0, 0, 'Да', 'yes:' . $addContext);
            $this->keyboards[] = array(0, 1, 'Нет', 'no:' . $addContext);
        }
        
    }
    
    
    /*
     * set keyboard for telegram object
     * 
     * @param telegram - telegram object
     */
    public function setKeyboard(&$telegram)
    {
        $this->log->write(__LINE__ . ' keyboards - ' . json_encode($this->keyboards, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        if (!is_array($this->keyboards))
            return;
        foreach ($this->keyboards as $keyboard)
        {
            if ($this->keyboardType == 'keyboard') {
                $telegram->getReplyMarkup()->getKeyboard()->addKeyboad($keyboard[0], $keyboard[1], $keyboard[2]);
            }
            else {
                $telegram->getReplyMarkup()->getInlineKeyboard()->addInlineKeyboad($keyboard[0], $keyboard[1], $keyboard[2], $keyboard[3]);
            }
        }
    }   
}
?>
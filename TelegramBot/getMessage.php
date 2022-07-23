<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/langConsts.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/MS/ApiMS.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/MS/OrdersMS.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/MS/ProjectMS.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/MS/DeliveryTypeMS.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/TelegramUser.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/ReplyMarkup.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/Keyboard.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/InlineKeyboard.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/Telegram.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/TelegramBotApi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/RoleKeyboard.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TelegramBot/Classes/OrderMessage.php';

$log = new Log('TelegramBot - getMessage.log');
$content = json_decode (file_get_contents('php://input'), true);
$log->write(__LINE__ . ' content - ' . json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
if (isset($content['callback_query']))
{
    $callbackData = explode(':', $content['callback_query']['data']);
    $telegram = new Telegram((string)$content['callback_query']['message']['chat']['id']);
    // если не авторизовался
    $user = TelegramUser::findUserByChat((string)$content['callback_query']['message']['chat']['id']);
    $log->write(__LINE__ . ' user - ' . json_encode($user, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    if (!$user)
    {
        $log->write(__LINE__ . ' авторизация');
        $telegram->setText('Необходимо авторизоваться');
        $telegram->getParseMode('HTML');
        $telegram->getReplyMarkup()->getKeyboard()->addKeyboad(0, 0, 'Отправить контактные данные', true);
        $telegram->sendMessage();
    }
    // заказ доставлен или отменен
    elseif ($callbackData[0] == 'deliver' || $callbackData[0] == 'shipped')
    {
        $log->write(__LINE__ . ' deliver/shipped');
        // typing action
        $telegramWait = new Telegram((string)$content['callback_query']['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        // edit message
//         $telegramOld = new Telegram((string)$content['callback_query']['message']['chat']['id']);
//         $telegramOld->setMessageId($content['callback_query']['message']['message_id']);
//         $telegramOld->setParseMode('HTML');
//         $telegramOld->setText('<s>' . $content['callback_query']['message']['text'] . '</s>');
//         $telegramOld->editMessageText();
//         $telegramOld->editMessageReplyMarkup();
        
        if ($callbackData[0] == 'deliver') {
            $state = MS_DELIVERED_STATE_ID;
            $text = MESSAGE_TEXT_DELIVERED;
//             $text = DIALOG_TEXT_DELIVERED;
        }
        elseif ($callbackData[0] == 'cancel') {
            $state = MS_CANCEL_STATE_ID;
            $text = MESSAGE_TEXT_CANCELLED;
//             $text = DIALOG_TEXT_CANCELLED;
        }
        elseif ($callbackData[0] == 'shipped') {
            $state = MS_SHIPPED_STATE_ID;
            $text = MESSAGE_TEXT_SHIPPED;
//             $text = DIALOG_TEXT_SHIPPED;
        }
        
        $data = array (
            array (
                'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' .  $callbackData[1], 'customerorder'),
                'state' => array (
                    'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . $state, 'state')
                ),
                'deliveryPlannedMoment' =>  date ('Y-m-d H:i:s')
            )
        );
        
        $ordersMSClass = new OrdersMS();
        $ordersMSClass->createUpdateOrders($data);
        $telegram->setParseMode('HTML');
        $telegram->setText($text);
        $telegram->sendMessage();

        
//         $ordersMSClass = new OrdersMS();
//         $order = $ordersMSClass->getOrder($callbackData[1]);

//         $orderMessage = new OrderMessage($order);
//         $messageText = $orderMessage->getMessage();
        
//         $messageText .= PHP_EOL . PHP_EOL . $text;
        
//         $telegram->setText($messageText);
//         $telegram->setParseMode('HTML');
//         $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'yesNo', $callbackData[0] . ':' . $order['id']);
//         $roleKeyboard->setKeyboard($telegram);
//         $telegram->sendMessage();
    }
    // заказ перенесен или недозвон
    elseif ($callbackData[0] == 'cancel' || $callbackData[0] == 'transfer' || $callbackData[0] == 'recall')
    {
        $log->write(__LINE__ . ' transfer/cancel/recall');
        $telegramWait = new Telegram((string)$content['callback_query']['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();

        $telegramOld = new Telegram((string)$content['callback_query']['message']['chat']['id']);
        $telegramOld->setMessageId($content['callback_query']['message']['message_id']);
        $telegramOld->setParseMode('HTML');
        $telegramOld->setText('<s>' . $content['callback_query']['message']['text'] . '</s>');
        $telegramOld->editMessageText();
        $telegramOld->editMessageReplyMarkup();
        
        if ($callbackData[0] == 'recall' || $callbackData[0] == 'transfer') {
            $data = array (
                array (
                    'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' .  $callbackData[1], 'customerorder'),
                    'deliveryPlannedMoment' => date ('Y-m-d H:i:s', strtotime('+1 day'))
                    
                )
            );
            
            $ordersMSClass = new OrdersMS();
            $ordersMSClass->createUpdateOrders($data);
        }
        if ($callbackData[0] == 'transfer') {
            $telegram->setText(MESSAGE_TEXT_TRANSFERRED);
        }
        if ($callbackData[0] == 'recall') {
            $telegram->setText(MESSAGE_TEXT_RECALLED);
        }
        if ($callbackData[0] == 'cancel') {
            $telegram->setText(MESSAGE_TEXT_CANCELLED);
        }
        
        $managers = TelegramUser::findUsersByRole('manager');
        foreach ($managers as $manager)
        {
            $order = $ordersMSClass->getOrder($callbackData[1]);
            
            if ($callbackData[0] == 'transfer')
            {
                $messageText = 'Заказ № <b>' . $order['name'] . '</b> перенесен на завтра курьером. Заказ перенесен на завтра. ' . PHP_EOL . 'Телефон курьера: ' . $user->getPhone();
                $messageText .= PHP_EOL . PHP_EOL . 'Информация по заказу';
                
            }
            if ($callbackData[0] == 'recall')
            {
                $messageText = 'По заказу № <b>' . $order['name'] . '</b> не удалось дозвониться. Заказ перенесен на завтра. ' . PHP_EOL . 'Телефон курьера: ' . $user->getPhone();
                $messageText .= PHP_EOL . PHP_EOL . 'Информация по заказу' . PHP_EOL;
            }
            if ($callbackData[0] == 'cancel')
            {
                $messageText = 'По заказу № <b>' . $order['name'] . '</b> запрошена отмена заказа. ' . PHP_EOL . 'Телефон курьера: ' . $user->getPhone();
                $messageText .= PHP_EOL . PHP_EOL . 'Информация по заказу' . PHP_EOL;
            }
            
            $orderMessage = new OrderMessage($order);
            $text = $orderMessage->getMessage();
            
            $messageText .= $text;
            $telegramManager = new Telegram($manager->getChatId());
            $telegramManager->setParseMode('HTML');
            $telegramManager->setText($messageText);
            $telegramManager->sendMessage();
        }

        $telegram->setParseMode('HTML');
        $telegram->sendMessage();
        
        
//         if ($callbackData[0] == 'recall') {
//             $text = DIALOG_TEXT_RECALLED;
//         }
//         elseif ($callbackData[0] == 'transfer') {
//             $text = DIALOG_TEXT_TRANSFERRED;
//         }
        
//         $ordersMSClass = new OrdersMS();
//         $order = $ordersMSClass->getOrder($callbackData[1]);
        
//         $orderMessage = new OrderMessage($order);
//         $messageText = $orderMessage->getMessage();
        
//         $messageText .= PHP_EOL . PHP_EOL . $text;
        
//         $telegram->setText($messageText);
//         $telegram->setParseMode('HTML');
//         //$telegram->getReplyMarkup()->getKeyboard()->updateKeyboardProperties('inline_keyboard', false, false);
//         $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'yesNo', $callbackData[0] . ':' . $order['id']);
//         $roleKeyboard->setKeyboard($telegram);
//         $telegram->sendMessage();
    }
    elseif ($callbackData[0] == 'yes')
    {
        $log->write(__LINE__ . ' yes');
        
        if ($callbackData[1] == 'deliver' || $callbackData[1] == 'cancel')
        {
            if ($callbackData[1] == 'deliver') {
                $state = MS_DELIVERED_STATE_ID;
                $text = MESSAGE_TEXT_DELIVERED;
            }
            elseif ($callbackData[1] == 'cancel') {
                $state = MS_CANCEL_STATE_ID;
                $text = MESSAGE_TEXT_CANCELLED;
            }
            elseif ($callbackData[1] == 'shipped') {
                $state = MS_SHIPPED_STATE_ID;
                $text = MESSAGE_TEXT_SHIPPED;
            }
            
            $data = array (
                array (
                    'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' .  $callbackData[2], 'customerorder'),
                    'state' => array (
                        'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . $state, 'state')
                    )
                )
            );
            // edit message
//             $telegramOld = new Telegram((string)$content['callback_query']['message']['chat']['id']);
//             $telegramOld->setMessageId($callbackData[3]);
//             $telegramOld->setParseMode('HTML');
//             $telegramOld->setText('<s>' . $callbackData[4] . '</s>');
//             $telegramOld->editMessageText();
//             $telegramOld->editMessageReplyMarkup();
            
            $ordersMSClass = new OrdersMS();
            $ordersMSClass->createUpdateOrders($data);
            $telegram->setParseMode('HTML');
            $telegram->setText($text);
            $telegram->sendMessage();
        }
        elseif ($callbackData[1] == 'transfer' || $callbackData[1] == 'recall')
        {
            
            $data = array (
                array (
                    'meta' => APIMS::createMeta(MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . '/' .  $callbackData[2], 'customerorder'),
                    'deliveryPlannedMoment' => date ('Y-m-d H:i:s', strtotime('+1 day'))
              
                )
            );
            $ordersMSClass = new OrdersMS();
            $ordersMSClass->createUpdateOrders($data);
            $telegram->setParseMode('HTML');
            if ($callbackData[1] == 'transfer') {
                $telegram->setText(MESSAGE_TEXT_TRANSFERRED);
            }
            if ($callbackData[1] == 'recall') {
                $telegram->setText(MESSAGE_TEXT_RECALLED);
            }
            $telegram->sendMessage();
            
            $managers = TelegramUser::findUsersByRole('manager');
            foreach ($managers as $manager)
            {
                $order = $ordersMSClass->getOrder($callbackData[2]);
                
                if ($callbackData[1] == 'transfer')
                {
                    $messageText = 'Заказ № <b>' . $order['name'] . '</b> перенесен на завтра курьером. ' . PHP_EOL . 'Телефон курьера: ' . $user->getPhone();
                    $messageText .= PHP_EOL . 'Информация по заказу';
                    
                }
                if ($callbackData[1] == 'recall')
                {
                    $messageText = 'По заказу № <b>' . $order['name'] . '</b> не удалось дозвониться. ' . PHP_EOL . 'Телефон курьера: ' . $user->getPhone();
                    $messageText .= PHP_EOL . PHP_EOL . 'Информация по заказу';
                }
                
                $orderMessage = new OrderMessage($order);
                $text = $orderMessage->getMessage();
                
                $messageText .= $text;
                $telegramManager = new Telegram($manager->getChatId());
                $telegramManager->setParseMode('HTML');
                $telegramManager->setText($messageText);
                $telegramManager->sendMessage();
            }
        }
        $currentTelegram = new Telegram((string)$content['callback_query']['message']['chat']['id']);
        $currentTelegram->setMessageId($content['callback_query']['message']['message_id']);
        $currentTelegram->deleteMessage();
    }
    elseif ($callbackData[0] == 'no')
    {
        $log->write(__LINE__ . ' no');
        
        $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'keyboard');
        $roleKeyboard->setKeyboard($telegram);
        
        $telegram->setParseMode('HTML');
        $telegram->setText('Статус заказа не изменен. Главное меню');
        $telegram->sendMessage();

        $currentTelegram = new Telegram((string)$content['callback_query']['message']['chat']['id']);
        $currentTelegram->setMessageId($content['callback_query']['message']['message_id']);
        $currentTelegram->deleteMessage();
    }
    elseif ($callbackData[0] == 'deliveryType')
    {
        $telegram->setParseMode('HTML');
        $telegram->setText('Введите или выберите ');
        $telegram->sendMessage();
        
    }
}
else 
{
    $telegram = new Telegram((string)$content['message']['chat']['id']);
    // если не авторизовался
    $user = TelegramUser::findUserByChat((string)$content['message']['chat']['id']);
    // если не авторизовался
    if (!$user && !isset($content['message']['contact']))
    {
        $log->write(__LINE__ . ' авторизация');
        $telegram->setText('Необходимо авторизоваться');
        $telegram->getParseMode('HTML');
        $telegram->getReplyMarkup()->getKeyboard()->addKeyboad(0, 0, 'Отправить контактные данные', true);
        $telegram->sendMessage();
    }
    // если пользователь не авторизован и прислал контактные данные
    elseif (!$user && isset($content['message']['contact']))
    {
        $log->write(__LINE__ . ' получение контактных данных');
        $user = TelegramUser::findUserByPhone(strpos($content['message']['contact']['phone_number'], '+') === 0 ? $content['message']['contact']['phone_number'] : '+' . $content['message']['contact']['phone_number']);
        $telegram->setParseMode('HTML');
        if (!$user)
        {
            $telegram->getReplyMarkup()->getKeyboard()->removeKeyboard();
            $telegram->setText('<b>Вы не имеете прав на работу с роботом!!!</b>');
            $telegram->sendMessage();
            return;
        }
        
        $user->setUserChat($content['message']['chat']['id']);

        $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'keyboard');
        $roleKeyboard->setKeyboard($telegram);
        
        if ($user->getUserRoleCode() == 'driver')
        {
            $telegram->setText('<b>Привет курьер, можешь начинать работу</b>. Главное меню');
            $telegram->sendMessage();
        }
        if ($user->getUserRoleCode() == 'manager') 
        {
            $telegram->setText('<b>Привет руководитель, можешь начинать работу</b>. Главное меню');
            $telegram->sendMessage();
        }
    }
    // сообщение получить пачку новых заказов
    elseif ($content['message']['text'] == GET_ALL_NEW_ORDERS)
    {
        $log->write(__LINE__ . ' новые заказы');
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $ordersMSClass = new OrdersMS();
        $filter = 'deliveryPlannedMoment%3C=' . date('Y-m-d') . '%2023:59:59;';
        $filter .= 'deliveryPlannedMoment%3E=' . date('Y-m-d') . '%2000:00:00;';
        $filter .= MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . MS_API_ATTRIBUTES . '/' . MS_SHIPTYPE_ATTR_ID . '=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERENTITY . '/' . MS_SHIPTYPE_REF_ID . '/' . $user->getMsId() . ';';
        $filter .= 'state=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . MS_SHIPPED_STATE_ID . ';';
        
        $orders = $ordersMSClass->findOrders($filter);
        
        if (count ($orders) == 0) {
            $telegram->setParseMode('HTML');
            $messageText = 'Новых заказов на сегодня нет';
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
        foreach ($orders as $order)
        {
            $orderMessage = new OrderMessage($order);
            $messageText = $orderMessage->getMessage();
            
            $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'newOrder', $order['id']);
            $roleKeyboard->setKeyboard($telegram);
            
            $telegram->setParseMode('HTML');
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
    }
    // сообщение получить доставленные заказы
    elseif ($content['message']['text'] == GET_ALL_DELIVERED_ORDERS)
    {
        $log->write(__LINE__ . ' доставленные заказы');
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $ordersMSClass = new OrdersMS();
        $filter = 'deliveryPlannedMoment%3C=' . date('Y-m-d') . '%2023:59:59;';
        $filter .= 'deliveryPlannedMoment%3E=' . date('Y-m-d') . '%2000:00:00;';
        $filter .= MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . MS_API_ATTRIBUTES . '/' . MS_SHIPTYPE_ATTR_ID . '=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERENTITY . '/' . MS_SHIPTYPE_REF_ID . '/' . $user->getMsId() . ';';
        $filter .= 'state=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . MS_DELIVERED_STATE_ID . ';';

        $orders = $ordersMSClass->findOrders($filter);
        if (count ($orders) == 0) {
            $messageText = 'Доставленных заказов на сегодня нет';
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
        
        foreach ($orders as $order)
        {
            $orderMessage = new OrderMessage($order);
            $messageText = $orderMessage->getMessage();
            
            $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'deliveriedOrder', $order['id']);
            $roleKeyboard->setKeyboard($telegram);

            $telegram->setParseMode('HTML');
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
    }
    // сообщение получить отмененные заказы
    elseif ($content['message']['text'] == GET_ALL_CANCELLED_ORDERS)
    {
        $log->write(__LINE__ . ' отмененные заказы');
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $ordersMSClass = new OrdersMS();
        $filter = 'deliveryPlannedMoment%3C=' . date('Y-m-d') . '%2023:59:59;';
        $filter .= 'deliveryPlannedMoment%3E=' . date('Y-m-d') . '%2000:00:00;';
        $filter .= MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . MS_API_ATTRIBUTES . '/' . MS_SHIPTYPE_ATTR_ID . '=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERENTITY . '/' . MS_SHIPTYPE_REF_ID . '/' . $user->getMsId() . ';';
        $filter .= 'state=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . MS_CANCEL_STATE_ID . ';';
        
        $orders = $ordersMSClass->findOrders($filter);
        if (count ($orders) == 0) {
            $messageText = 'Отмененных заказов на сегодня нет';
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
        
        foreach ($orders as $order)
        {
            $orderMessage = new OrderMessage($order);
            $messageText = $orderMessage->getMessage();
            
            $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'cancelledOrder', $order['id']);
            $roleKeyboard->setKeyboard($telegram);
            
            $telegram->setParseMode('HTML');
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
    }
    // сообщение получить перенесенные заказы
    elseif ($content['message']['text'] == GET_ALL_RESCHEDULED_ORDERS)
    {
        $log->write(__LINE__ . ' перенесенные заказы');
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $ordersMSClass = new OrdersMS();
        $filter = 'deliveryPlannedMoment%3C=' . date('Y-m-d', strtotime('+1 day')) . '%2023:59:59;';
        $filter .= 'deliveryPlannedMoment%3E=' . date('Y-m-d', strtotime('+1 day')) . '%2000:00:00;';
        $filter .= MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDER . MS_API_ATTRIBUTES . '/' . MS_SHIPTYPE_ATTR_ID . '=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERENTITY . '/' . MS_SHIPTYPE_REF_ID . '/' . $user->getMsId() . ';';
        $filter .= 'state=' . MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_CUSTOMERORDERSTATE . '/' . MS_SHIPPED_STATE_ID . ';';
        
        $orders = $ordersMSClass->findOrders($filter);
        if (count ($orders) == 0) {
            $messageText = 'Перенесенных на завтра заказов нет';
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
        
        foreach ($orders as $order)
        {
            $orderMessage = new OrderMessage($order);
            $messageText = $orderMessage->getMessage();
            
            $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'transferredOrder', $order['id']);
            $roleKeyboard->setKeyboard($telegram);
            
            $telegram->setParseMode('HTML');
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
    }
    // получить всех водителей
    elseif ($content['message']['text'] == GET_ALL_DRIVERS)
    {
        if ($user->getUserRoleCode() != 'manager')
        {
            $telegram->setText('Недостаточно прав на выполнение функции');
            $telegram->sendMessage();
            return;
        }
        
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $drivers = TelegramUser::findUsersByRole('driver');

        //$roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'keyboard');
        //$roleKeyboard->setKeyboard($telegram);
        
        $telegram->setParseMode('HTML');
        if (count ($drivers) == 0)
        {
            $telegram->setText('Ни один водитель еще не зарегистрирован. Главное меню');
            $telegram->sendMessage();
        }
        else 
        {
            foreach ($drivers as $key => $driver)
            {
                $messageText = 'Водитель ' . ((int)$key + 1);
                $messageText .= PHP_EOL . 'Имя: <b>' . $driver->getUserName() . '</b>';
                $messageText .= PHP_EOL . 'Зарегистрирован: <b>' . ($driver->getChatId() != '' ? 'Да' : 'Нет') . '</b>';
                $messageText .= PHP_EOL . 'Телефон: <b>' . $driver->getPhone() . '</b>';
                $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'inlineKeyboard', 'driver', $driver->getUserId());
                $roleKeyboard->setKeyboard($telegram);

                $telegram->setText($messageText);
                $telegram->sendMessage();
            }
        }
    }
    // добавим нового водителя
    elseif ($content['message']['text'] == ADD_DRIVER)
    {
        if ($user->getUserRoleCode() != 'manager')
        {
            $telegram->setText('Недостаточно прав на выполнение функции');
            $telegram->sendMessage();
            return;
        }
        
        $telegramWait = new Telegram((string)$content['message']['chat']['id']);
        $telegramWait->setAction('typing');
        $telegramWait->sendChatAction();
        
        $deliveryTypes = DeliveryTypeMS::getDeliveryTypes();
        
        $telegram->setParseMode('HTML');
        if (count ($deliveryTypes) == 0)
        {
            $telegram->setText('Ни одного способа доставки не зарегистрировано. Главное меню');
            $telegram->sendMessage();
        }
        else
        {
            $messageText = 'К какому способу доставки привязываем нового курьера?';
            foreach ($deliveryTypes as $key => $deliveryType)
            {
                $telegram->getReplyMarkup()->getInlineKeyboard()->addInlineKeyboad(intdiv($key, 2), $key % 2, $deliveryType->getName() , 'deliveryType:' . $deliveryType->getId());
            }
            $telegram->setText($messageText);
            $telegram->sendMessage();
        }
    }
    // если пользователь авторизован и послал произвольный запрос
    else
    {
        $log->write(__LINE__ . ' неопознанное сообщение');
        $roleKeyboard = new RoleKeyboard($user->getUserRoleCode(), 'keyboard');
        $roleKeyboard->setKeyboard($telegram);

        $telegram->setParseMode('HTML');
        $telegram->setText('Сообщение не опознано роботом. Главное меню');
        $telegram->sendMessage();
    }
}
?>
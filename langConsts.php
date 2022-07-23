<?php
// commands
define('GET_ALL_ORDERS', 'Все заказы');
define('GET_ALL_NEW_ORDERS', 'Заказы в доставке');
define('GET_ALL_DELIVERED_ORDERS', 'Доставленные заказы');
define('GET_ALL_CANCELLED_ORDERS', 'Отмененные заказы');
define('GET_ALL_RESCHEDULED_ORDERS', 'Перенесенные заказы');

define('GET_ALL_DRIVERS', 'Все курьеры');
define('ADD_DRIVER', 'Добавить курьера');
define('DELETE_DRIVER', 'Удалить курьера');

define('MESSAGE_TEXT_DELIVERED', 'Заказ успешно доставлен');
define('MESSAGE_TEXT_CANCELLED', 'Заказ помечен отмененным. Менеджер получил уведомление');
define('MESSAGE_TEXT_SHIPPED', 'Заказ успешно возвращен в доставку');
define('MESSAGE_TEXT_TRANSFERRED', 'Заказ успешно перенесен на завтра');
define('MESSAGE_TEXT_RECALLED', 'Заказ помечен как недозвон. Менеджер получил уведомление и сделает контрольный звонок');

define('DIALOG_TEXT_DELIVERED', 'Подтвеодить изменение статуса заказа на <b>Доставлен</b>?');
define('DIALOG_TEXT_CANCELLED', 'Подтвеодить изменение статуса заказа на <b>Отменен</b>?');
define('DIALOG_TEXT_SHIPPED', 'Подтвеодить возврат статуса заказа на <b>Отгружен</b>?');
define('DIALOG_TEXT_TRANSFERRED', 'Заказ будет перенесен на доставку на завтра. Вы уверены?');
define('DIALOG_TEXT_RECALLED', 'Заказ будет временно перенесен на доставку на завтра. Менеджер в офисе попытается дозвониться до клиента и если клиент ответит, будьте готовы отвезти заказ. Подтверждаете действие?');

?>
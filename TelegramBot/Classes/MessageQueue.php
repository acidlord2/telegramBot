<?php
/**
 *
 * @class MessageQueue
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class MessageQueue
{
    private $chatId;
    private $type;
    private $content;
    private $log;
    
    public function __construct($chatId, $type)
    {
        $this->chatId = $chatId;
        $this->type = $type;
    }
    /*
     * add new mew message to queue
     */
    public function addMessage($message, $stepNumber)
    {
        
        $sql = 'insert into telegram_queue (chat_id, type, message, step_number) values ("' . $this->chatId . '", "' . $this->type . '", "' . $message . '", "' . $stepNumber . '")';
        DB::exec_query($sql);
    }

    public function clearQueue() {
        $sql = 'delete from telegram_queue where chat_id = "' . $this->chatId . '" and type = "' . $this->type . '"';
        DB::exec_query($sql);
    }
    
    public function deleteLastMessage() {
        $sql = 'select max queue_id from telegram_queue where chat_id = "' . $this->chatId . '" and type = "' . $this->type . '"';
        DB::exec_query($sql);
    }
}
?>
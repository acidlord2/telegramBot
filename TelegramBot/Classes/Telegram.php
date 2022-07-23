<?php
/**
 *
 * @class Telegram
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class Telegram implements JsonSerializable
{
    private $log;
    
    private $chatId;
    private $text;
    private $parseMode;
    private ReplyMarkup $replyMarkup;
    private $action;
    private $messageId;
    
    /*
     * create class instance
     * @param chatId
     */
    public function __construct($chatId)
    {
        $this->chatId = $chatId;
        $this->log = new Log('TelegramBot - Classes - Telegram.log');
    }

    /*
     * create class instance
     * @param chatId
     */
    public function jsonSerialize()
    {
        $array = array();
        
        if (isset($this->chatId)) {
            $array['chat_id'] = (string)$this->chatId;
        }
        
        if (isset($this->text)) {
            $array['text'] = $this->text;
        }
            
        if (isset($this->parseMode)) {
            $array['parse_mode'] = $this->parseMode;
        }
        
        if (isset($this->replyMarkup))
        {
            $array['reply_markup'] = $this->replyMarkup->jsonSerialize();
        }
        
        if (isset($this->action)) {
            $array['action'] = (string)$this->action;
        }
        
        if (isset($this->messageId)) {
            $array['message_id'] = (int)$this->messageId;
        }
        
        return $array;
    }
    
    /*
     * returns message text
     * @return Telegram->text
     */
    public function getText() 
    {
        return $this->text;
    }
    /*
     * sets message text
     * @param text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /*
     * returns message parseMode
     * 
     * @return Telegram->parseMode
     */
    public function getParseMode()
    {
        return $this->parseMode;
    }
    /*
     * sets message parseMode
     * 
     * @param parseMode
     */
    public function setParseMode($parseMode)
    {
        $this->parseMode = $parseMode;
    }

    /*
     * returns message replyMarkup
     * 
     * @return Telegram->replyMarkup
     */
    public function getReplyMarkup()
    {
        if (!isset($this->replyMarkup)) {
            $this->replyMarkup = new ReplyMarkup();
        }
        
        return $this->replyMarkup;
    }
    
    /*
     * sets message replyMarkup
     * 
     * @param replyMarkup
     */
    public function setReplyMarkup($replyMarkup)
    {
        $this->replyMarkup = $replyMarkup;
    }

    /*
     * sets message id
     *
     * @param messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }
    
    /*
     * sets message replyMarkup
     *
     * @param replyMarkup
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    public function sendMessage() 
    {
        $this->log->write(__LINE__ . ' sendMessage.this - ' . json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        TelegramBotApi::postData('sendMessage', $this);
    }

    public function sendChatAction()
    {
        $this->log->write(__LINE__ . ' sendChatAction.this - ' . json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        TelegramBotApi::postData('sendChatAction', $this);
    }
    
    public function deleteMessage()
    {
        $this->log->write(__LINE__ . ' deleteMessage.this - ' . json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        TelegramBotApi::postData('deleteMessage', $this);
    }
    
    public function editMessageText() {
        $this->log->write(__LINE__ . ' editMessageText.this - ' . json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        TelegramBotApi::postData('editMessageText', $this);
    }

    public function editMessageReplyMarkup() {
        $this->log->write(__LINE__ . ' editMessageReplyMarkup.this - ' . json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        TelegramBotApi::postData('editMessageReplyMarkup', $this);
    }
}
?>
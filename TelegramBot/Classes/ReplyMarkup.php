<?php
/**
 *
 * @class ReplyMarkup
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class ReplyMarkup implements JsonSerializable
{
    private Keyboard $keyboard;
    private InlineKeyboard $inlineKeyboard;
    
    public function jsonSerialize()
    {
        $replyMarkup = array();
        if (isset($this->keyboard)) {
            $replyMarkup = array_merge($replyMarkup, $this->keyboard->jsonSerialize());
        }
        if (isset($this->inlineKeyboard)) {
            $replyMarkup = array_merge($replyMarkup, $this->inlineKeyboard->jsonSerialize());
        }
        return $replyMarkup;
    }
    
    /*
     * returns ReplyMarkup keyboard
     *
     * @return ReplyMarkup->keyboard
     */
    public function getKeyboard()
    {
        if (!isset($this->keyboard)) {
            $this->keyboard = new Keyboard();
        }
        return $this->keyboard;
    }
    /*
     * returns ReplyMarkup inline keyboard
     *
     * @return ReplyMarkup->inlineKeyboard
     */
    public function getInlineKeyboard()
    {
        if (!isset($this->inlineKeyboard)) {
            $this->inlineKeyboard = new InlineKeyboard();
        }
        return $this->inlineKeyboard;
    }
}
?>
<?php
/**
 *
 * @class Keyboard
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class InlineKeyboard implements JsonSerializable
{
    private $buttons;
    
    public function jsonSerialize()
    {
        $inlineKeyboard = array ();

        if (isset($this->buttons)) {
            $inlineKeyboard['inline_keyboard'] = $this->buttons;
        }
        
        return $inlineKeyboard;
    }

    /*
     * add new inline keyboard button
     *
     * @param row - button row
     * @param column - button column
     * @param text - button text
     * @param requesContacts - if need to request user contacts
     */
    public function addInlineKeyboad($row, $column, $text, $callbackData)
    {
        if (!isset($this->buttons[$row])) {
            $this->buttons[$row] = array();
        }
        if (!isset($this->buttons[$row][$column])) {
            $this->buttons[$row][$column] = array();
        }
        
        $this->buttons[$row][$column]['text'] = $text;
        $this->buttons[$row][$column]['callback_data'] = $callbackData;
    }
}
?>
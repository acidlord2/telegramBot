<?php
/**
 *
 * @class Keyboard
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class Keyboard implements JsonSerializable
{
    private $buttons;
    private $oneTimeKeyboard = FALSE;
    private $resizeKeyboard = TRUE;
    private $removeKeyboard;
    
    public function jsonSerialize()
    {
        $keyboard = array ();

        if (isset($this->removeKeyboard))
        {
            $keyboard['remove_keyboard'] = $this->removeKeyboard;
            return $keyboard;
        }
        
        $keyboard['resize_keyboard'] = $this->resizeKeyboard;
        $keyboard['one_time_keyboard'] = $this->oneTimeKeyboard;
        
        if (isset($this->buttons)) {
            $keyboard['keyboard'] = $this->buttons;
        }
        
        return $keyboard;
    }
    
    /*
     * add new keyboard button
     * 
     * @param row - button row
     * @param column - button column
     * @param text - button text
     * @param requesContacts - if need to request user contacts
     */
    public function addKeyboad($row, $column, $text, $requesContacts = FALSE)
    {
        unset($this->removeKeyboard);
        if (!isset($this->buttons[$row])) {
            $this->buttons[$row] = array();
        }
        if (!isset($this->buttons[$row][$column])) {
            $this->buttons[$row][$column] = array();
        }
        
        $this->buttons[$row][$column]['text'] = $text;
        $this->buttons[$row][$column]['request_contact'] = $requesContacts;
    }

    /*
     * removes all keyboard buttons
     */
    public function removeKeyboard()
    {
        unset($this->buttons);
        $this->removeKeyboard = TRUE;
    }
    
    /*
     * sets keyboard properties
     * 
     * @param keyboardType - sets keyboard type
     * @param resizeKeyboard - sets resize_keyboard flag
     * @param oneTimeKeyboard - sets show keyboard flag
     */
    public function updateKeyboardProperties($keyboardType, $resizeKeyboard = true, $oneTimeKeyboard = false)
    {
        $this->keyboardType = $keyboardType;
        $this->resizeKeyboard = $resizeKeyboard;
        $this->oneTimeKeyboard = $oneTimeKeyboard;
    }
    
}
?>
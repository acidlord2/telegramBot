<?php
/**
 *
 * @class TelegramUser
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class TelegramUser implements JsonSerializable
{
    private $phone;
    private $chatId;
    private $userId;
    private $userName;
    private $userRoleName;
    private $userRoleCode;
    private $msId;
    
    public function jsonSerialize() {
        return array(
            'phone' => $this->phone,
            'chatId' => $this->chatId,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'userRoleName' => $this->userRoleName,
            'userRoleCode' => $this->userRoleCode,
            'msId' => $this->msId,
            
        );
    }
    /*
     * finds user by chat
     *
     * @return TelegramUser
     */
    static public function findUserByChat($chatId)
    {
        $sql = 'SELECT u.user_id, u.name as user_name, u.ms_id, u.phone, c.chat_id, ur.code as user_role_code, ur.name as user_role_name
                FROM telegram_users u 
                LEFT JOIN telegram_chats c ON u.user_id = c.user_id 
                LEFT JOIN telegram_user_roles ur on u.user_role_id = ur.user_role_id
                where c.chat_id = "' . $chatId . '"';
        $driverArray = Db::exec_query_array($sql);
        if (!count($driverArray)) {
            return false;
        }
        
        $user = new TelegramUser();
        $user->phone = $driverArray[0]['phone'];
        $user->userId = $driverArray[0]['user_id'];
        $user->msId = $driverArray[0]['ms_id'];
        $user->userName = $driverArray[0]['user_name'];
        $user->userRoleCode = $driverArray[0]['user_role_code'];
        $user->userRoleName = $driverArray[0]['user_role_name'];
        $user->chatId = $driverArray[0]['chat_id'];
        return $user;
    }
    /*
     * finds user by phone
     *
     * @return TelegramUser
     */
    static public function findUserByPhone($phone)
    {
        $sql = 'SELECT u.user_id, u.name as user_name, u.ms_id, u.phone, c.chat_id, ur.code as user_role_code, ur.name as user_role_name
                FROM telegram_users u 
                LEFT JOIN telegram_chats c ON u.user_id = c.user_id 
                LEFT JOIN telegram_user_roles ur on u.user_role_id = ur.user_role_id
                where u.phone = "' . $phone . '"';
        $driverArray = Db::exec_query_array($sql);
        if (!count($driverArray)) {
            return false;
        }
            
        $user = new TelegramUser();
        $user->phone = $driverArray[0]['phone'];
        $user->userId = $driverArray[0]['user_id'];
        $user->msId = $driverArray[0]['ms_id'];
        $user->userName = $driverArray[0]['user_name'];
        $user->userRoleCode = $driverArray[0]['user_role_code'];
        $user->userRoleName = $driverArray[0]['user_role_name'];
        $user->chatId = $driverArray[0]['chat_id'];
        return $user;
    }
    /*
     * finds user by phone
     *
     * @return TelegramUser
     */
    static public function findUsersByRole($userRoleCode)
    {
        $log = new Log('TelegramBot - Classes - TelegramUser.log');
        $sql = 'SELECT u.user_id, u.name as user_name, u.ms_id, u.phone, c.chat_id, ur.code as user_role_code, ur.name as user_role_name
                FROM telegram_users u
                LEFT JOIN telegram_chats c ON u.user_id = c.user_id
                LEFT JOIN telegram_user_roles ur on u.user_role_id = ur.user_role_id
                where ur.code = "' . $userRoleCode . '"';
        $log->write(__LINE__ . ' sql - ' . $sql);
        $usersArray = Db::exec_query_array($sql);
        $log->write(__LINE__ . ' usersArray - ' . json_encode($usersArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        if (!count($usersArray)) {
            return false;
        }
        
        $users = array();
        foreach ($usersArray as $userArray)
        {
            $user = new TelegramUser();
            $user->phone = $userArray['phone'];
            $user->userId = $userArray['user_id'];
            $user->msId = $userArray['ms_id'];
            $user->userName = $userArray['user_name'];
            $user->userRoleCode = $userArray['user_role_code'];
            $user->userRoleName = $userArray['user_role_name'];
            $user->chatId = $userArray['chat_id'];
            $users[] = $user;
        }
        return $users;
    }
    /*
     * returns user id
     *
     * @return TelegramUser->userId
     */
    public function getUserId()
    {
        return $this->userId;
    }
        /*
     * returns user phone number
     * 
     * @return TelegramUser->phone
     */
    public function getPhone() 
    {
        return $this->phone;
    }
/*
     * returns user name
     *
     * @return TelegramUser->userName
     */
    
    public function getUserName()
    {
        return $this->userName;
    }
    /*
     * returns user ms curier_id
     *
     * @return TelegramUser->msId
     */

    public function getMsId()
    {
        return $this->msId;
    }
    /*
     * returns user chat_id
     *
     * @return TelegramUser->chatId
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /*
     * returns user role code
     *
     * @return TelegramUser->userRoleCode
     */
    public function getUserRoleCode()
    {
        return $this->userRoleCode;
    }
    
    /*
     * returns user role name
     *
     * @return TelegramUser->userRoleName
     */
    public function getUserRoleName()
    {
        return $this->userRoleName;
    }
    
    /*
     * returns user phone number
     *
     * @param $chatId - $chat_id
     * @return true
     */
    public function setUserChat($chatId)
    {
        $sql = 'delete from telegram_chats where user_id = ' . $this->userId . ' and chat_id = "' . $chatId . '"';
        Db::exec_query($sql);
        $sql = 'insert into telegram_chats (user_id, chat_id) values (' . $this->userId . ', "' . $chatId . '")';
        Db::exec_query($sql);
        return true;
    }
}
?>
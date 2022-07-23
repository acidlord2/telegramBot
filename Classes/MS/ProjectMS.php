<?php
/**
 *
 * @class ProjectMS
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class ProjectMS
{
    private $log;
    private $apiMSClass;
    
    private $cache = array ();
    
    public function __construct()
    {
        $this->log = new Log('Classes - MS - ProjectMS.log');
        $this->apiMSClass = new APIMS();
    }
    
    /**
     * function getProject - function returns project info
     *
     * @param id - project id
     * @return array - result project info
     */
    public function getProject($id)
    {
        $url = MS_API_BASE_URL . MS_API_VERSION_1_2 . MS_API_PROJECT . '/' . $id;
        $this->log->write (__LINE__ . ' getProject.url - ' . $url);
        $project = $this->apiMSClass->getData($url);
        $this->log->write (__LINE__ . ' getProject.project - ' . json_encode ($project, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $project;
    }
}

?>
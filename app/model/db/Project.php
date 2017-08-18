<?php

namespace Anthony\LineBot\Model\Db;

/**
 * Project Model
 */
class Project extends \Phalcon\Mvc\Model
{
    public $projectId;
    public $name;
    public $channelSecret;
    public $token;
    public $createTime;
    public $updateTime;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "Project";
    }
}
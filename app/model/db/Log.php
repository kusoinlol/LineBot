<?php

namespace Anthony\LineBot\Model\Db;

/**
 * Log Model
 */
class Log extends \Phalcon\Mvc\Model
{
    public $logId;
    public $type;
    public $json;
    public $createTime;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "Log";
    }
}
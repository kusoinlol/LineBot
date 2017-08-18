<?php

namespace Anthony\LineBot\Model\Db;

/**
 * Log Model
 */
class Keyword extends \Phalcon\Mvc\Model
{
    public $keywordId;
    public $projectId;
    public $groupId;
    public $type;
    public $response;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "Keyword";
    }
}
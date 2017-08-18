<?php

namespace Anthony\LineBot\Model\Db;

/**
 * Project Model
 */
class LazyPack extends \Phalcon\Mvc\Model
{
    public $id;
    public $groupId;
    public $type;
    public $content;
    public $date;
    public $messageJson;
    public $createTime;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "LazyPack";
    }
}
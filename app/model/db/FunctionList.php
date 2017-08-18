<?php

namespace Anthony\LineBot\Model\Db;

/**
 * FunctionList Model
 */
class FunctionList extends \Phalcon\Mvc\Model
{
    public $functionId;
    public $functionName;
    public $desc;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "FunctionList";
    }
}
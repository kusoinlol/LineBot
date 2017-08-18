<?php

namespace Anthony\LineBot\Model\Db;

/**
 * ProjectDetail Model
 */
class ProjectDetail extends \Phalcon\Mvc\Model
{
    public $projectDetailId;
    public $projectId;
    public $functionId;
    public $status;
    public $createTime;
    public $updateTime;

    /**
     * 資料表名稱
     *
     * @return string
     */
    public function getSource()
    {
        return "ProjectDetail";
    }
}
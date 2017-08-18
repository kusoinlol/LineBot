<?php

namespace Anthony\LineBot\Model\Dao;

use Anthony\LineBot\Model\Db\Keyword;

class MusicDao extends \Phalcon\Mvc\User\Component
{

    public function addKeyword($keywordObj)
    {
        $musicResult = $this->modelsManager->executeQuery(
        "SELECT mi.*, sm.uid, sm.addTime FROM \Anthony\Hsing\Model\Db\MusicInfo as mi left join \Anthony\Hsing\Model\Db\SingMusic as sm on mi.musicId = sm.musicId WHERE mi.name like '%$q%' or mi.artist LIKE '%$q%'"
        );
        
        return $musicResult;
    }
}
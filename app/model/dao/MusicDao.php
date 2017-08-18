<?php

namespace Anthony\Hsing\Model\Dao;

use Anthony\Hsing\Model\Db\MusicInfo;
use Anthony\Hsing\Model\Db\SingMusic;
use Anthony\Hsing\Model\Db\SongTeamAccompany;
use Anthony\Hsing\Model\Db\OtherTeamAccompany;

class MusicDao extends \Phalcon\Mvc\User\Component
{

    public function getMusicInfo($q)
    {
        // $musicInfoObj = new MusicInfo();
        // $musicResult  = $musicInfoObj->find(
        //     [
        //      "conditions" => "name LIKE '%$q%' or artist LIKE '%$q%'",
        //      "bind"       => [1 => $q],
        //     ]
        // );

        // return $musicResult;

        // $musicResult = $this->modelsManager->executeQuery(
        // "SELECT * FROM \Anthony\Hsing\Model\Db\MusicInfo WHERE name like '%:q:%' or artist LIKE '%:q:%'",
        // [
        //     "q" => $q,
        // ]
        // );

        // return $musicResult;

        $musicResult = $this->modelsManager->executeQuery(
        "SELECT mi.*, sm.uid, sm.addTime FROM \Anthony\Hsing\Model\Db\MusicInfo as mi left join \Anthony\Hsing\Model\Db\SingMusic as sm on mi.musicId = sm.musicId WHERE mi.name like '%$q%' or mi.artist LIKE '%$q%'"
        );
        
        return $musicResult;
    }

    public function getSong($q)
    {
        $songTeamAccompany = new SongTeamAccompany();

        $songResult = $songTeamAccompany->find("name like '%$q%' or singer like '%$q%' or songDesc like '%$q%' order by songId Desc");
        
        return $songResult;
    }

    public function getOtherSong($q)
    {
        $songTeamAccompany = new OtherTeamAccompany();

        $songResult = $songTeamAccompany->find("name like '%$q%' or singer like '%$q%' or uploadName like '%$q%' or songDesc like '%$q%' order by songId Desc limit 100");
        
        return $songResult;
    }

    public function getSongByUid($uid)
    {
        $songTeamAccompany = new SongTeamAccompany();

        $songResult = $songTeamAccompany->find("uploadUid = '$uid' order by songId Desc");
        
        return $songResult;
    }

    public function getNewSong()
    {
        $songTeamAccompany = new SongTeamAccompany();

        $songResult = $songTeamAccompany->find("1=1 order by songId Desc limit 100");
        
        return $songResult;
    }

    public function getSongByNoBody()
    {
        $songTeamAccompany = new SongTeamAccompany();

        $songResult = $songTeamAccompany->find("chorusCount = 0 order by songId Desc");
        
        return $songResult;
    }

    public function getSongByHot()
    {
        $songTeamAccompany = new SongTeamAccompany();

        $songResult = $songTeamAccompany->find("1=1 order by chorusCount Desc limit 100");
        
        return $songResult;
    }

    public function getTotalCount()
    {
        $musicResult = $this->modelsManager->executeQuery(
        "select count(id) as count FROM \Anthony\Hsing\Model\Db\SongTeamAccompany"
        );
        
        return $musicResult;
    }

    public function getUpdateTime()
    {
        $musicResult = $this->modelsManager->executeQuery(
        "select updateTime FROM \Anthony\Hsing\Model\Db\SongTeamJson order by updateTime Desc limit 1"
        );
        
        return $musicResult;
    }
}
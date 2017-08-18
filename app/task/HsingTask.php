<?php


use Anthony\Hsing\Model\Db\MusicInfo;
use Anthony\Hsing\Model\Db\Hsing;
use Anthony\Hsing\Model\Db\Exchange;
use Anthony\Hsing\Model\Db\LineInfo;
use Anthony\Hsing\Model\Db\SingMusic;
use Anthony\Hsing\Model\Db\SongTeamJson;
use Anthony\Hsing\Model\Db\SongTeamAccompany;
use Anthony\Hsing\Model\Db\OtherTeamAccompany;
use Anthony\Hsing\Model\Db\HsingGoodVoice;
use Anthony\Hsing\Model\Service\HsingService;

/**
 * Class AuthTask
 */
class HsingTask extends \Phalcon\CLI\Task
{
    public function getMusicInfoAction()
    {

        date_default_timezone_set("Asia/Taipei");
        $start = 1;
        $end   = 65000;

        $musicId = $start;
        while ($musicId <= $end) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?token=dRtdphviwbJQ5NBCsFBbGDIILJAs_VHa9DXWt75LZbtdo4E_nA-wdvxZrha-    GxUIf2DxGc0EQOaGsFnRFG1oeODiya_60AcyGkbrWfAXVHTB0es2yDc-udIIRF0q_9gH&musicId=" . $musicId .  "&uid=950094&action=GetMusicInfo");
            $songDataJson = curl_exec($ch);
            curl_close($ch);
            
            $songDataOri = json_decode($songDataJson);
            $songData    = $songDataOri->response_data;

            $musicInfo = MusicInfo::findFirst("musicId=$musicId");
            
            if (!$musicInfo) {
                $musicInfo = new MusicInfo();
            }

            $musicInfo->musicId    = $songData->id;
            $musicInfo->artist     = $songData->artist;
            $musicInfo->name       = $songData->name;
            $musicInfo->path       = $songData->path;
            $musicInfo->size       = $songData->size;
            $musicInfo->lyric      = $songData->lyric;
            $musicInfo->srcfrom    = $songData->srcfrom;
            $musicInfo->volume     = $songData->volume;
            $musicInfo->count      = $songData->count;
            $musicInfo->md5        = $songData->md5;
            $musicInfo->addtime    = date("Y-m-d H:i:s", $songData->addtime);
            $musicInfo->vip        = $songData->vip;
            $musicInfo->mp3        = $songData->mp3;
            $musicInfo->copyright  = $songData->copyright;
            $musicInfo->tag        = $songData->tag;
            $musicInfo->music      = $songData->music;
            $musicInfo->music_size = $songData->music_size;
            $musicInfo->music_orig = $songData->music_orig;
            $musicInfo->showTag    = $songData->showTag;

            $musicInfo->save();

            $musicId++;
            echo $musicInfo->musicId . ":" . $musicInfo->artist . "-" . $musicInfo->name . PHP_EOL;
        }//end while
    }

    public function lineAction()
    {
        $str = '<img src=3D"http://images.trvl-media.com/media/content/shared/images/naviga=
tion/expedia.com.tw_zh_tw.png"><br><font size=3D"4"><b>=E6=82=A8=E7=9A=84=
=E7=8F=AD=E6=A9=9F=E8=A9=B3=E7=B4=B0=E8=B3=87=E6=96=99=E5=B7=B2=E8=AE=8A=E6=
=9B=B4</b></font><table width=3D"900">';
        mail ("kusoinlol@gmail.com" , "JO I HSIEH 班機變更資訊" , $str, "From: Expedia Travel Services<no-reply@expedia.com.tw>" . "\r\n");
        exit;
        $hsingObj  = new Exchange();
        $hsingData = $hsingObj->find("lineId is not null");
        // var_dump($hsingData);
        foreach ($hsingData as $hsing) {
            $lineInfo = new LineInfo();

            $lineExist = $lineInfo->findFirst("lineId ='" . $hsing->lineId . "'");

            if ($lineExist === false) {
                $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('sAXBRt0wbS4zTgOLxd2yHwkn4yfpqewKd31o/RVDZLDCykuCaCjv/19pBX20t2AyLxj/8pboMRU22MUAKLXa3oEPNgabaVmogApm4s73pjOx1SyBW7u5/R2o1tZmtAaV1NITZlmsG07dxeA/cBRH0AdB04t89/1O/w1cDnyilFU=');

                $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'b1a14e24b490d61fc761393473385c3d']);

                $response = $bot->getProfile($hsing->lineId);
                $profile  = $response->getJSONDecodedBody();


                $lineInfo->lineId        = $hsing->lineId;
                $lineInfo->displayName   = $profile['displayName'];
                $lineInfo->userId        = $profile['userId'];
                $lineInfo->pictureUrl    = $profile['pictureUrl'];
                $lineInfo->statusMessage = $profile['statusMessage'];
                $lineInfo->json          = json_encode($profile);
                $lineInfo->createTime    = date('Y-m-d H:i:s');
                $lineInfo->save();


                echo $lineInfo->displayName . PHP_EOL;
            }
        }//end foreach

        exit;
        
        // if (!empty($text)) {
        //     $responseAry[$data['events'][0]['message']['id']] = "$date - $sender: $text"; 
        // } else {
        //     // var_dump($data);
        // }
    }

    public function getSingerAction()
    {
        $singMusic   = new SingMusic();
        $musicResult = $singMusic->find("uid is null");


        foreach ($musicResult as $music) {
            $tempData['addtime'] = 0;
            $tempData['song'] = null;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?token=dRtdphviwbJQ5NBCsFBbGDIILJAs_VHa9DXWt75LZbtdo4E_nA-wdvxZrha-    GxUIf2DxGc0EQOaGsFnRFG1oeODiya_60AcyGkbrWfAXVHTB0es2yDc-udIIRF0q_9gH&musicId=" . $music->musicId .  "&uid=950094&action=SearchSong");
            $responseJson = curl_exec($ch);
            curl_close($ch);
            
            $response      = json_decode($responseJson);
            $response_data = $response->response_data;

            foreach ($response_data->song as $songData) {
                if ($songData->type == 0 && $tempData['addtime'] < $songData->addtime) {
                    $tempData['addtime'] = $songData->addtime;
                    $tempData['song']    = $songData;
                }
            }

            if ($tempData['addtime'] != 0) {
                $music->uid     = $tempData['song']->uid;
                $music->songId  = $tempData['song']->id;
                $music->addTime = date("Y-m-d H:i:s", $tempData['addtime']);
                $music->save();

                echo $singMusic->musicId . " " . $singMusic->songName . "->" . $singMusic->uid . PHP_EOL;
            }
        }
    }

    public function cacheSongAction()
    {
        $musicResult = $this->modelsManager->executeQuery(
        "SELECT mi.*, sm.uid, sm.addTime FROM \Anthony\Hsing\Model\Db\MusicInfo as mi left join \Anthony\Hsing\Model\Db\SingMusic as sm on mi.musicId = sm.musicId"
        );

        $redis = new \redis();
        $redis->connect('127.0.0.1', 6379);

        foreach ($musicResult as $music) {
            $song['musicId'] = $value->mi->musicId;
            $song['artist']  = $value->mi->artist;
            $song['name']    = $value->mi->name;
            $song['uid']     = $value->uid;
            $song['addTime'] = date('Y-m-d', strtotime($value->addTime));
            
            $redis->hset('lineNew', time(), json_encode($receive));
        }
    }

    public function getTeamJsonAction()
    {
        $uploadManArys['team'] = new \Phalcon\Config\Adapter\Yaml(APP_PATH . "config/file/songTeam.yml");
        $uploadManArys['other'] = new \Phalcon\Config\Adapter\Yaml(APP_PATH . "config/file/otherTeam.yml");

        foreach ($uploadManArys as $uploadType => $uploadManAry) {
            // 開啟檔案
            switch ($uploadType) {
                case 'team':
                    $type = 1;
                    break;
                case 'other':
                    $type = 2;
                    break;
            }

            foreach ($uploadManAry as $uid => $name) {
                echo "讀取合唱歌曲" . $name . PHP_EOL;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?uid=" . $uid . "&type=2&token=Ev0zWR2PDisPBVLHWn_fpy-watGGwxdXVZ-V1KCOQcjqlHwEXG5sfIPAbQfmwGccxH0XXHb-YwdZhmJGB_fbzHne80LWsPW6zK6InN8FOhvd1MOIrLl85IizTTubWQcm&action=GetSongCount");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $oriJson = curl_exec($ch);
                curl_close($ch);

                $oriObj    = json_decode($oriJson);
                $songCount = $oriObj->response_data;

                $uploadManInfo = SongTeamJson::findFirst(
                    [
                     "uploadUid = $uid",
                     "columns" => "count",
                    ]
                );

                $filename = $uid . '.json';

                if (!$uploadManInfo) {
                    $songTeamJson = new SongTeamJson();

                    $songTeamJson->type       = $type;
                    $songTeamJson->uploadUid  = $uid;
                    $songTeamJson->uploadName = $name;
                    $songTeamJson->count      = $songCount;
                    $songTeamJson->fileName   = $filename;
                    $songTeamJson->updateTime = date('Y-m-d H:i:s');
                    $songTeamJson->save();
                    echo $name . " 新增資料 共 " . $songCount . "首" . PHP_EOL;
                } else {
                    if ($uploadManInfo->count != $songCount) {
                        $uploadManInfo = SongTeamJson::findFirst("uploadUid = $uid");

                        $uploadManInfo->count      = $songCount;
                        $uploadManInfo->fileName   = $filename;
                        $uploadManInfo->updateTime = date('Y-m-d H:i:s');
                        $uploadManInfo->save();
                        echo $name . " 更新歌曲 共 " . $songCount . "首" . PHP_EOL;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?action=GetMySong&uid=" . $uid . "&songId=0&selfUid=799472&type=2&token=dKt5mVpV2xb4VyoIO12wWsLQXGPGzy1XcCfPklvyY4DUzgf5fnOMmCR3Zm8lJ19PPeZUiWqDtUFRRVQntbX5xD0XOuopB0N3AKWlnzxK-wuh5Mw7g8IbcX3nvIfWedZ2&qty=3000");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $oriJson = curl_exec($ch);
                        curl_close($ch);

                        $oriObj   = json_decode($oriJson);
                        $songData = $oriObj->response_data;
                        $filename = $uid . '.json';
                        // 開啟檔案
                        switch ($uploadType) {
                            case 'team':
                                $file = fopen(APP_PATH . "config/file/soneTeamJson/" . $filename, "w+");
                                break;
                            case 'other':
                                $file = fopen(APP_PATH . "config/file/otherTeamJson/" . $filename, "w+");
                                break;
                        }

                        fwrite($file, json_encode($songData));
                        fclose($file);
                        
                        echo "歌曲檔案建立完成" . $filename . PHP_EOL;
                    } else {
                        echo $name . " 無新增歌曲" . PHP_EOL;
                    }
                }//end if
            }//end foreach
        }
    }

    public function getTeamSongAction()
    {
        $uploadManAry = SongTeamJson::find();
        foreach ($uploadManAry as $uploadManInfo) {
            // if (strtotime($uploadManInfo->updateTime) < (time() - 1800)) {
            //     continue;
            // }


            // if ($uploadManInfo->id != 12) {
            //     continue;
            // }

            echo "載入伴唱機資料" . $uploadManInfo->uploadUid . $uploadManInfo->uploadName . PHP_EOL;
            
            if ($uploadManInfo->type == 1) {
                $file = fopen(APP_PATH . "config/file/soneTeamJson/" . $uploadManInfo->fileName, "r");
            } else if ($uploadManInfo->type == 2) {
                $file = fopen(APP_PATH . "config/file/otherTeamJson/" . $uploadManInfo->fileName, "r");
            }

            $songJson = "";
            // Read the file line by line until the end
            while (!feof($file)) {
                $songJson = $songJson . fgets($file);
            }

            // Close the file that no longer in use
            fclose($file);

            $oriSongObj = json_decode($songJson);
            var_dump(count($oriSongObj));
            // foreach ($oriSongObj as $song) {
            //     echo $songDetail['singer'] . "-" . $songDetail['songName'] . PHP_EOL;
            //     $hsingService = new HsingService();
            //     $songDetail   = $hsingService->getSongDetail($song);
            //     $songDescLine = str_replace(array("\r", "\n", "\r\n", "\n\r", "💖"), '', $song->desc);
            //     if (strpos($songDescLine, "＃歡歌歌名：") !== false) {
            //         $startPos = mb_strpos($songDescLine, "＃歡歌歌名：", 0, "UTF-8");
            //         $hsing    = mb_substr($songDescLine, ($startPos + 6), 100, "UTF-8");
            //     } else {
            //         $hsing = $song->name;
            //     }

            //     if ($uploadManInfo->type == 1) {
            //         $songData = SongTeamAccompany::findFirst("songId = " . $song->id);
            //     } else if ($uploadManInfo->type == 2) {
            //         $songData = OtherTeamAccompany::findFirst("songId = " . $song->id);
            //     }
                
            //     if (!$songData) {
            //         if ($uploadManInfo->type == 1) {
            //             $songTeamAccompany = new SongTeamAccompany();
            //         } else if ($uploadManInfo->type == 2) {
            //             $songTeamAccompany = new OtherTeamAccompany();
            //         }
                    
            //         $songTeamAccompany->createTime = date('Y-m-d H:i:s');
            //         $songTeamAccompany->updateTime = date('Y-m-d H:i:s');
            //         if ($songDetail !== null) {
            //             $songTeamAccompany->songId      = $song->id;
            //             $songTeamAccompany->uploadName  = $uploadManInfo->uploadName;
            //             $songTeamAccompany->uploadUid   = $uploadManInfo->uploadUid;
            //             $songTeamAccompany->name        = $songDetail['songName'];
            //             $songTeamAccompany->singer      = $songDetail['singer'];
            //             $songTeamAccompany->hsing       = $hsing;
            //             $songTeamAccompany->addTime     = date("Y-m-d", $song->addtime);
            //             $songTeamAccompany->chorusCount = $song->chorusCount;
            //             $songTeamAccompany->songUrl     = 'http://17sing.tw/song//' . $song->id;
            //             $songTeamAccompany->songDesc    = $songDescLine;
            //             $songTeamAccompany->json        = json_encode($song);
            //             $songTeamAccompany->save();
            //         }
            //     } else {
            //         $songTeamAccompany = $songData;

            //         $songTeamAccompany->updateTime  = date('Y-m-d H:i:s');
            //         $songTeamAccompany->songDesc    = $songDescLine;
            //         $songTeamAccompany->chorusCount = $song->chorusCount;
            //         $songTeamAccompany->json        = json_encode($song);
            //             // $songTeamAccompany->name        = $songDetail['songName'];
            //             // $songTeamAccompany->singer      = $songDetail['singer'];
            //         $songTeamAccompany->save();
            //     }//end if
            // }//end foreach

        }//end foreach
    }

    public function checkDeleteSongAction()
    {
        $uploadManAry = SongTeamJson::find();
        $deleteTime   = (time() - 86400);
        foreach ($uploadManAry as $uploadMan) {
            if (strtotime($uploadMan->updateTime) < $deleteTime) {
                continue;
            }

            $songAry = SongTeamAccompany::find("uploadUid = " . $uploadMan->uploadUid);
            foreach ($songAry as $song) {
                if (strtotime($song->updateTime) < strtotime($uploadMan->updateTime) - 86400) {
                    echo $song->songId . $song->singer . "-" . $song->name . " 已刪除" . PHP_EOL;
                    $song->delete();
                }
            }
        }
    }

    public function deleteSongAction($arg)
    {
        $uid = $arg[0];
        $token = 'kU3M5xzgR_Zq1pPoeebNaD_0hSoL1GqHGTdCD7f55zKfxUW8vz5h0Pflzafp4bm31RsxxQ9D0Cn6qJHiiUSrSQIDxRdu1ndfWCaENcehEn10Or9Dq-u-CN7JRScG6Y8I';

        echo "讀取主頁歌曲" . $uid . PHP_EOL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?action=GetMySong&uid=" . $uid . "&songId=0&selfUid=1893505&type=0&token=" . $token . "&qty=1000");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $oriJson = curl_exec($ch);
        curl_close($ch);

        $oriObj = json_decode($oriJson);
        $songs  = $oriObj->response_data;
        echo "讀取主頁歌曲完成" . PHP_EOL;

        foreach ($songs as $song) {
            echo "刪除" . $song->id . "-" . $song->name;

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_URL, "http://act.17sing.tw/index.php?sid=" . $song->id . "&token=" . $token . "&uid=1893505&action=DeleteSong");
            $songData = curl_exec($ch2);
            $ori      = json_decode($songData);
            if ($ori->response_data == 'true') {
                echo "  成功" . PHP_EOL;
            } else {
                echo "  失敗" . PHP_EOL;
                var_dump($songData);
                exit;
            }
        }
    }

    public function getJsonAndSongAction()
    {
        $uploadManArys['team'] = new \Phalcon\Config\Adapter\Yaml(APP_PATH . "config/file/songTeam.yml");

        foreach ($uploadManArys as $uploadType => $uploadManAry) {
            // 開啟檔案
            switch ($uploadType) {
                case 'team':
                    $type = 1;
                    break;
                case 'other':
                    $type = 2;
                    break;
            }

            foreach ($uploadManAry as $uid => $name) {
                echo "讀取合唱歌曲" . $name . PHP_EOL;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?uid=" . $uid . "&type=2&token=Ev0zWR2PDisPBVLHWn_fpy-watGGwxdXVZ-V1KCOQcjqlHwEXG5sfIPAbQfmwGccxH0XXHb-YwdZhmJGB_fbzHne80LWsPW6zK6InN8FOhvd1MOIrLl85IizTTubWQcm&action=GetSongCount");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $oriJson = curl_exec($ch);
                curl_close($ch);

                $oriObj    = json_decode($oriJson);
                $songCount = $oriObj->response_data;

                $uploadManInfo = SongTeamJson::findFirst(
                    [
                     "uploadUid = $uid",
                     "columns" => "count",
                    ]
                );
                unset($songId);
                $filename = $uid . '.json';

                if (!$uploadManInfo) {
                    $songTeamJson = new SongTeamJson();

                    $songTeamJson->type       = $type;
                    $songTeamJson->uploadUid  = $uid;
                    $songTeamJson->uploadName = $name;
                    $songTeamJson->count      = $songCount;
                    $songTeamJson->fileName   = $filename;
                    $songTeamJson->updateTime = date('Y-m-d H:i:s');
                    $songTeamJson->save();
                    echo $name . " 新增資料 共 " . $songCount . "首" . PHP_EOL;
                } else {
                    if ($uploadManInfo->count != $songCount) {
                        $uploadManInfo = SongTeamJson::findFirst("uploadUid = $uid");

                        $uploadManInfo->count      = $songCount;
                        $uploadManInfo->fileName   = $filename;
                        $uploadManInfo->updateTime = date('Y-m-d H:i:s');
                        $uploadManInfo->save();
                        echo $name . " 更新歌曲 共 " . $songCount . "首" . PHP_EOL;
                        $songDbCount = 0;
                        if (!isset($songId)) {
                            $songId = 0;
                        }

                        while ($songDbCount < $songCount) {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?action=GetMySong&uid=" . $uid . "&songId=" . $songId . "&selfUid=799472&type=2&token=dKt5mVpV2xb4VyoIO12wWsLQXGPGzy1XcCfPklvyY4DUzgf5fnOMmCR3Zm8lJ19PPeZUiWqDtUFRRVQntbX5xD0XOuopB0N3AKWlnzxK-wuh5Mw7g8IbcX3nvIfWedZ2&qty=200");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $oriJson = curl_exec($ch);
                            curl_close($ch);

                            $oriObj   = json_decode($oriJson);
                            $songData = $oriObj->response_data;
                            var_dump(count($songData), $songDbCount, $songCount);
                            
                            foreach ($songData as $song) {
                                // echo $song['singer'] . "-" . $song['songName'] . PHP_EOL;
                                $hsingService = new HsingService();
                                $songDetail   = $hsingService->getSongDetail($song);
                                $songDescLine = str_replace(array("\r", "\n", "\r\n", "\n\r", "💖"), '', $song->desc);
                                if (strpos($songDescLine, "＃歡歌歌名：") !== false) {
                                    $startPos = mb_strpos($songDescLine, "＃歡歌歌名：", 0, "UTF-8");
                                    $hsing    = mb_substr($songDescLine, ($startPos + 6), 100, "UTF-8");
                                } else {
                                    $hsing = $song->name;
                                }

                                if ($uploadManInfo->type == 1) {
                                    $songData = SongTeamAccompany::findFirst("songId = " . $song->id);
                                } else if ($uploadManInfo->type == 2) {
                                    $songData = OtherTeamAccompany::findFirst("songId = " . $song->id);
                                }
                                
                                if (!$songData) {
                                    if ($uploadManInfo->type == 1) {
                                        $songTeamAccompany = new SongTeamAccompany();
                                    } else if ($uploadManInfo->type == 2) {
                                        $songTeamAccompany = new OtherTeamAccompany();
                                    }
                                    
                                    $songTeamAccompany->createTime = date('Y-m-d H:i:s');
                                    $songTeamAccompany->updateTime = date('Y-m-d H:i:s');
                                    if ($songDetail !== null) {
                                        $songTeamAccompany->songId      = $song->id;
                                        $songTeamAccompany->uploadName  = $uploadManInfo->uploadName;
                                        $songTeamAccompany->uploadUid   = $uploadManInfo->uploadUid;
                                        $songTeamAccompany->name        = $songDetail['songName'];
                                        $songTeamAccompany->singer      = $songDetail['singer'];
                                        $songTeamAccompany->hsing       = $hsing;
                                        $songTeamAccompany->addTime     = date("Y-m-d", $song->addtime);
                                        $songTeamAccompany->chorusCount = $song->chorusCount;
                                        $songTeamAccompany->songUrl     = 'http://17sing.tw/song//' . $song->id;
                                        $songTeamAccompany->songDesc    = $songDescLine;
                                        $songTeamAccompany->json        = json_encode($song);
                                        $songTeamAccompany->save();
                                    }
                                } else {
                                    $songTeamAccompany = $songData;

                                    $songTeamAccompany->updateTime  = date('Y-m-d H:i:s');
                                    $songTeamAccompany->songDesc    = $songDescLine;
                                    $songTeamAccompany->chorusCount = $song->chorusCount;
                                    $songTeamAccompany->json        = json_encode($song);
                                        // $songTeamAccompany->name        = $songDetail['songName'];
                                        // $songTeamAccompany->singer      = $songDetail['singer'];
                                    $songTeamAccompany->save();
                                }//end if

                                $songDbCount++;
                                $songId = $song->id;
                            }//end foreach
                        }//end while

                        $filename = $uid . '.json';
                    } else {
                        echo $name . " 無新增歌曲" . PHP_EOL;
                    }//end if
                }//end if
            }//end foreach
        }//end foreach
    }

    public function goodVoiceListAction()
    {
        $users['name']  = "0905505130";
        $users['token'] = "iMt0Zbe9JDlE-IghSDejegepXBtYmQLqtqdcPJdvSlCLFn4V1O0vuoQUl8vzcNrViD9CTlW6deTpjOHukSw9WpHZincCQKE8gb8_gb1pvJKBbcZdVMIVI7UnUMr0_U4E";

        $uid = "1832349";
        $gameId = "18";

        // 取得比賽名單
        $post = array(
                 "game_id" => $gameId,
                 "uid"     => $uid,
                 "token"   => $users['token'],
                 "action"  => "GoodVoice.ListenSong",
                );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $songCount = 1;
        while ($songCount <= 50) {
            $data = curl_exec($ch);
            $data = json_decode($data);
            if (isset($data->response_data->song_info->id)) {
                $songInfo = $data->response_data->song_info;

                $songId = $songInfo->id;

                $song = HsingGoodVoice::findFirst("songId = $songId");
                
                if (!$song) {
                    $post2 = array(
                              "game_id" => $gameId,
                              "song_id" => $songId,
                              "rate"    => 1,
                              "uid"     => $uid,
                              "token"   => $users['token'],
                              "action"  => "GoodVoice.RateSong",
                             );

                    $ch2 = curl_init();
                    curl_setopt($ch2, CURLOPT_POST, 1);
                    curl_setopt($ch2, CURLOPT_POSTFIELDS, $post2);
                    curl_setopt($ch2, CURLOPT_URL, "http://act.17sing.tw/index.php");
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                    $rateJson = curl_exec($ch2);
                    $rate     = json_decode($rateJson);

                    $song = new HsingGoodVoice();

                    $song->gameId    = $gameId;
                    $song->songId    = $songId;
                    $song->singerUid = $songInfo->uid;
                    $song->songName  = $songInfo->name;
                    $song->rank      = $rate->response_data->ranking;
                    $song->songJson  = json_encode($songInfo);
                    $song->rankJson  = $rateJson;
                    // $song->singerName = $songInfo->
                    var_dump($rateJson);
                    $song->save();
                    echo "$songCount - $songId - " . $songInfo->name . PHP_EOL;
                    $songCount++;
                }//end if
            } else {
                // echo "取得資料失敗" . json_encode($data) . PHP_EOL;
            }//end if
        }//end while
    }

    public function goodVoiceSingerAction()
    {
        $users['name']  = "0905505130";
        $users['token'] = "iMt0Zbe9JDlE-IghSDejegepXBtYmQLqtqdcPJdvSlCLFn4V1O0vuoQUl8vzcNrViD9CTlW6deTpjOHukSw9WpHZincCQKE8gb8_gb1pvJKBbcZdVMIVI7UnUMr0_U4E";

        $uid    = "1832349";
        $gameId = "18";

        // 取得比賽名單
        $songList = HsingGoodVoice::find("gameId = $gameId and singerName is null");

        foreach ($songList as $song) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?token=dRtdphviwbJQ5NBCsFBbGDIILJAs_VHa9DXWt75LZbtdo4E_nA-wdvxZrha-GxUIf2DxGc0EQOaGsFnRFG1oeODiya_60AcyGkbrWfAXVHTB0es2yDc-udIIRF0q_9gH&uid=" . $song->singerUid . "&action=GetUserInfo&selfUid=950094");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $singerJson       = curl_exec($ch);
            $singerObj        = json_decode($singerJson);
            $song->singerJson = $singerJson;
            $song->singerName = $singerObj->response_data->nick;
            $song->save();
            echo $singerObj->response_data->nick . PHP_EOL;
        }
    }
}
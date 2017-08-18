<?php
namespace Anthony\Hsing\Model\Service;


use Anthony\Hsing\Model\Db\SingMusic;
use \Exception;

/**
 * 
 */
class HsingService
{

    /**
     * [sendMessage description]
     * @param  [type] $to      [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getSongDetail($songData)
    {
        $igoneSong   = ['7145983', '5947919', 5871824, 14134629, 13915793, 11062627];
        // $tmpIgone    = ['5748165', '7380144', 7360966, 7360488, 7351905, 14909020];
        $tmpIgone    = [];
        $kalaKeyword = ["『工商服務』", "伴唱", "伴奏", "聯盟", "((", "精靈合唱", "與精靈"];
        $keepAllAry  = ['1347676', '1893563', '2091009', '1893505'];
        $replaceAry  = ["「自助伴唱機」", "【伴奏提供】", "【伴唱提供】", "『工商服務』", "💎工商服務💎", "：", "台語", "日語", "英語", "粵語", "國語", "缺男聲", "缺女聲", "【伴唱音樂】", "我發起了一首", "合唱", "「純伴奏提供」", "「伴奏提供」", "「伴唱提供」", "『", "』", "【金金陪你/妳唱】", "「伴奏＋正確歌名」", "【與藍精靈合唱系列】", "【與精靈把拔合唱】", "『與藍精靈合唱』", "歌曲名稱："];
        $uid         = $songData->uid;
        $songDetail  = [];
        $songDesc    = str_replace(array("\r", "\n", "\r\n", "\n\r", "💖"), '', $songData->desc);
        $songName    = "";
        $singer      = "";
        foreach ($kalaKeyword as $keyword) {
            if (strpos($songDesc, $keyword) !== false && !in_array($songData->id, $igoneSong) && !in_array($songData->id, $tmpIgone)) {
                $kala = true;
                break;
            } else if (in_array($uid, $keepAllAry)) {
                $kala = true;
                break;
            } else {
                $kala = false;
            }
        }

        if ($kala) {
            if (strpos($songDesc, "《") !== false && mb_strpos($songDesc, "》") !== false) {
                $songName = $this->catchStr($songDesc, "《", "》");
                $singer   = mb_substr($songDesc, 0, mb_strpos($songDesc, "《"));
            }

            if ($uid == "1347676" && mb_strpos($songDesc, "(") !== false && mb_strpos($songDesc, ")") !== false) {
                $songName = $this->catchStr($songDesc, "(", ")");
                $singer   = mb_substr($songDesc, 0, mb_strpos($songDesc, "("));
            }

            if (mb_strpos($songName, "－") !== false) {
                // 比我對你更好的人－汪佩蓉
                $endPos   = mb_strpos($songName, "－", 0, "UTF-8");
                $singer   = mb_substr($songName, ($endPos + 1));
                $songName = mb_substr($songName, 0, $endPos);
            }

            if (mb_strpos($songName, "-") !== false) {
                // 比我對你更好的人－汪佩蓉
                $endPos   = mb_strpos($songName, "-", 0, "UTF-8");
                $singer   = mb_substr($songName, ($endPos + 1));
                $songName = mb_substr($songName, 0, $endPos);
            }

            if (strpos($songDesc, "『") !== false && mb_strpos($songDesc, "』") !== false && mb_strpos($songDesc, "歌曲名稱：") !== false) {
                // 歌曲名稱：女兒圈『尤雅』
                $singer   = $this->catchStr($songDesc, "『", "』");
                $songName = mb_substr($songDesc, 0, mb_strpos($songDesc, "『"));
            }

            if (strpos($songDesc, "更改歌曲名稱為") !== false && mb_strpos($songDesc, "演唱人") !== false) {
                // 歌曲名稱：女兒圈『尤雅』
                $singer   = $this->catchStr($songDesc, "演唱人：", "↬注意事項↫");
                $songName = $this->catchStr($songDesc, "更改歌曲名稱為：", "演唱人：");
            }

            if (strpos($songDesc, "更改歌曲名稱為") !== false && mb_strpos($songDesc, "原唱人") !== false) {
                // 歌曲名稱：女兒圈『尤雅』
                $singer   = $this->catchStr($songDesc, "演唱人：", "↬注意事項↫");
                $songName = $this->catchStr($songDesc, "更改歌曲名稱為：", "原唱人：");
            }

            if ($uid == "1779303" || $uid == "24501571") {
                // 【伴奏提供】歌名： Lonely (消音版)主唱：草蜢■請無視系統歌詞，發放請標歌曲資料■
                // 【伴奏提供】歌名：妄想 (消音版)原唱：VRF■請無視系統歌詞
                // 【伴奏提供】歌名：那些年的小幸运  (a Cappella 消音版)主唱：MICappella■請無視系統歌詞，發
                $songDetailX = $songData->desc;
                $songDetailX = str_replace("【伴奏提供】\n", '', $songDetailX);
                $songName    = mb_substr($songDetailX, 0, mb_strpos($songDetailX, "\n", 0, "UTF-8"), "UTF-8");
                $songDetailX = str_replace($songName . "\n", '', $songDetailX);
                $singer      = mb_substr($songDetailX, 0, mb_strpos($songDetailX, "\n", 0, "UTF-8"), "UTF-8");
                $singer      = str_replace(array("原唱", "主唱", "：", "～", "；", ",", ":", "♢", "💖"), '', $singer);
                $songName    = str_replace(array("歌名", "：", "(", ")", "～", "；", ",", ":", "~", "."), '', $songName);
            }//end if

            if ($uid == "1760155") {
                // You're My Love, You're My Life (Patty Ryan)高音質伴唱帶錄
                $endPos      = mb_strpos($songDesc, ")", 0, "UTF-8");
                $startPos    = mb_strpos($songDesc, "(", 0, "UTF-8");
                $songDetailX = mb_substr($songDesc, 0, $endPos, "UTF-8");
                $singer      = mb_substr($songDetailX, ($startPos + 1), 100, "UTF-8");
                $songName    = mb_substr($songDetailX, 0, $startPos, "UTF-8");
            }//end if

            if ($uid == "1893505") {
                $songName = $songData->name;
            }//end if

            // $songDetail['songName'] = $songName;
            $songDetail['songName'] = str_replace($replaceAry, '', $songName);
            $songDetail['singer']   = str_replace($replaceAry, '', $singer);
            $songDetail['desc']     = $songDesc;
            if (strlen($songDetail['singer']) > 100) {
                $songDetail['singer'] = "";
            }
        } else {
            $songDetail = null;
        }//end if

        return $songDetail;
    }

    /**
     * [catchStr description]
     * @param  [type] $Str    [description]
     * @param  [type] $StaKey [description]
     * @param  [type] $EndKey [description]
     * @return [type]         [description]
     */
    private function catchStr($Str, $StaKey, $EndKey)
    {
        $StaPos = mb_strpos($Str, $StaKey);
        $EndPos = mb_strpos($Str, $EndKey);
        $StaLen = mb_strlen($StaKey);
        $EndLen = mb_strlen($EndKey);

        $CatchKey = mb_substr($Str, ($StaPos + $StaLen), (($EndPos - ($StaPos + $StaLen))));

        return $CatchKey;
    }

    public function checkMusic($musicId)
    {
        $song = SingMusic::findFirst("musicId = $musicId");
        if ($song->uid == "1893505") {
            return false;
        }
        
        return true;
    }

    public function postMusic($musicId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?token=dRtdphviwbJQ5NBCsFBbGDIILJAs_VHa9DXWt75LZbtdo4E_nA-wdvxZrha-    GxUIf2DxGc0EQOaGsFnRFG1oeODiya_60AcyGkbrWfAXVHTB0es2yDc-udIIRF0q_9gH&musicId=" . $musicId .  "&uid=950094&action=GetMusicInfo");
        $songDataJson = curl_exec($ch);
        curl_close($ch);
            
        $songDataOri = json_decode($songDataJson);
        $songData    = $songDataOri->response_data;

        $url    = 'http://act.17sing.tw/index.php?';
        $fields = array(
                   'action'  => 'UploadSong',
                   'uid'     => '1893505',
                   'lyric'   => urlencode('http://lyric.17sing.tw/encorelyric/'),
                   'device'  => urlencode('{"version":"1.1.0","system":"iPhone OS 10","brand":"跟麥許願池","headset":"true","volume":0,"network":"Wifi"}'),
                   'privacy' => '0',
                   'musicId' => $musicId,
                   'type'    => '0',
                   'name'    => $songData->name . "-" . $songData->artist,
                   'desc'    => '此為跟麥專用，許願請上伴唱聯盟網站 http://ec2.kusoinlol.com/hsing',
                   'token'   => 'XymOMh6fSO_Av4QACECQ5w7YUZNONyVQjFBeIssmL8F_U9teGqIwE64iL6DaxjGRK3jM1DFoEIE0NHPQAPcewFKRekUw7PCDnu6u040Jh26XWE2JWT_CISnpQaK2kXu3',
                  );

        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }

        $toURL = $url . $fields_string;
        $file  = array("song" => new \CurlFile(APP_PATH . "config/file/space.mp3", 'audio/mp3', 'space.mp3'));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'IOS,version=1.5.2(146),system=9.0.2');
        curl_setopt($ch, CURLOPT_URL, $toURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        $postOri = curl_exec($ch);
        curl_close($ch);

        $postObj = json_decode($postOri);
        $post    = $postObj->response_data;

        $response['uid']     = $post->uid;
        $response['songId']  = $post->id;
        $response['addtime'] = date("Y-m-d H:i:s", $post->addtime);

        return $response;
    }
}
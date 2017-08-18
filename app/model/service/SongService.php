<?php
namespace Anthony\LineBot\Model\Service;

use \Exception;

/**
 * 
 */
class SongService
{
    /**
     * [getHsingFile description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getHsingFile($message)
    {
        // http://17sing.tw/share_song/index.html?sid=
        $text = $message->text;
        if (strpos($text, "17sing.tw")) {
            // 取得songId
            $cutPos = strpos($text, '?sid=');
            if ($cutPos) {
                $songId = substr($text, ($cutPos + 5));
            } else {
                $cutPos = strpos($text, 'song/');
                $songId = substr($text, ($cutPos + 5));
            }

            $songId = str_replace('/', '', $songId);

            // 取得歌曲資訊
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://act.17sing.tw/index.php?token=dRtdphviwbJQ5NBCsFBbGDIILJAs_VHa9DXWt75LZbtdo4E_nA-wdvxZrha-    GxUIf2DxGc0EQOaGsFnRFG1oeODiya_60AcyGkbrWfAXVHTB0es2yDc-udIIRF0q_9gH&sid=" . $songId .  "&uid=950094&action=GetSongInfo");
            $songData = curl_exec($ch);
            curl_close($ch);
            
            $songData = json_decode($songData);
            
            $songUrl = $songData->response_data->song->path;
            $singer  = $songData->response_data->user->nick;
            $song    = $songData->response_data->song->name;

            $songUrl = "$singer - $song \n$songUrl";

            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($songUrl);
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [getRcFile description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getRcFile($message)
    {
        $text = $message->text;
        if (strpos($text, "rcsing.com/song/")) {
            $cutPos = strpos($text, 'rcsing.com/song/');
            $songId = substr($text, ($cutPos + 16));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://rcsing.com/api.php?c=song&a=getSongById&id=" . $songId);
            $songData = curl_exec($ch);
            curl_close($ch);
            $songData = json_decode($songData);
            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($songData->data->mp3);
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [getChangbaFile description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getChangbaFile($message)
    {
        $text = $message->text;
        if (strpos($text, "changba.com/s/")) {
            $cutPos = strpos($text, 'changba.com/s/');
            $songId = substr($text, ($cutPos + 14));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, "http://changba.com/s/" . $songId);
            $songData = curl_exec($ch);
            curl_close($ch);
            $url = $this->catchStr($songData, '(function(){var a="', '",b=/userwork\/([abc])(\d+)\/(\w+)\/(\w+)\.mp3/');
            
            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($url);
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [getLyric description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getLyric($message)
    {
        $text = $message->text;
        if (mb_strpos($text, "!歌詞") === 0 || mb_strpos($text, "！歌詞") === 0) {
            $keyword = mb_substr($text, 4);

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, "https://mojim.com/" . urlencode($keyword) . ".html?t3");
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            $responseText = curl_exec($ch2);
            $responseText = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $responseText);
            // $responseText = json_decode($responseText, 1);
            $songCount = $this->catchStr($responseText, '共有 ', '筆相關歌');
            $songList  = $this->catchStr($responseText, '<dl class="mxsh_dl0" >', '</dl></div>');
            $listAry   = explode('</dd>', $songList);
            $songAry   = array();

            unset($listAry[0]);
            array_pop($listAry);

            $responseStr = "資料來源為魔鏡歌詞網\n-----\n";
            $i = 0;
            foreach ($listAry as $lyrics) {
                $songData  = $this->catchStr($lyrics, '<span class="mxsh_ss4">', '<span class="mxsh_ss5">');
                $url       = "https://mojim.com" . $this->catchStr($songData, '<a href="', '" title="');
                $singer    = $this->catchStr($songData, ' 歌詞 ', '" >');
                $songName  = $this->catchStr($songData, 'title="', ' 歌詞 ');
                $responseStr .= "$singer - $songName\n$url\n-----\n";
                $i++;

                if ($i > 10) {
                    break;
                }
            }

            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($responseStr);
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [getHsingSong description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function getHsingSong($message)
    {
        $text = $message->text;
        if (mb_strpos($text, "!歌單") === 0 || mb_strpos($text, "！歌單") === 0) {
            $keyword = mb_substr($text, 4);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://ec2.kusoinlol.com/api/getSong?q=" . $keyword);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data, true);

            // 輪播型(僅手機看的到)
            $columns = array();
            $count   = 1;
            foreach ($data as $song) {
                $song['song'] = $song['singer'] . "《" . $song['name'] . "》";
                $song['url']  = str_replace('http://17sing.tw/song//', 'http://17sing.tw/share_song/index.html?sid=', $song['songUrl']);

                $desc    = "上傳人：" . $song['uploadMan'] . "\n上傳時間：" . $song['addtime'] . "\n歡歌歌名：" . $song['hsing'];
                $title   = (strlen($song['song']) < 40) ? $song['song'] : "標題錯誤";
                $img_url = null;
                $actions = array(
                                 // 一般訊息型 action
                                 0 => new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("顯示歌名(標題錯誤時使用)", $song['song']),
                                 // 網址型 action
                                 1 => new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("試聽連結", $song['url']),
                                );

                $column    = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($title, $desc, $img_url, $actions);
                $columns[] = $column;

                if ($count == 5) {
                    break;
                }

                $count++;
            }//end foreach

            $carousel = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);
            $response = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("電腦請使用伴唱聯盟歌單網站\nhttp://goo.gl/XXglcl", $carousel);
        } else {
            $response = false;
        }//end if

        return $response;
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
}
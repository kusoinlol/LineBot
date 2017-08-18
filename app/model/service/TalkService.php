<?php
namespace Anthony\LineBot\Model\Service;

use Anthony\LineBot\Model\Db\FunctionList;
use Anthony\LineBot\Model\Db\Keyword;
use \Exception;

/**
 * 
 */
class TalkService extends \Phalcon\Mvc\User\Component
{

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function talkToBot($message)
    {
        $text = $message->text;
        if (mb_strpos($text, "＃") === 0 || mb_strpos($text, "#") === 0) {
            $keyword = mb_substr($text, 1);

            // 繁轉簡
            $od = opencc_open("t2s.json");
            $text = opencc_convert($keyword, $od);
            opencc_close($od);

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, "http://api.qingyunke.com/api.php?key=free&appid=0&msg=" . urlencode($text));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            $responseText = curl_exec($ch2);
            $responseText = json_decode($responseText, 1);

            $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('sAXBRt0wbS4zTgOLxd2yHwkn4yfpqewKd31o/RVDZLDCykuCaCjv/19pBX20t2AyLxj/8pboMRU22MUAKLXa3oEPNgabaVmogApm4s73pjOx1SyBW7u5/R2o1tZmtAaV1NITZlmsG07dxeA/cBRH0AdB04t89/1O/w1cDnyilFU=');

            $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'b1a14e24b490d61fc761393473385c3d']);
            
            // 簡轉繁
            $od = opencc_open("s2twp.json");
            $responseText['content'] = opencc_convert($responseText['content'], $od);
            opencc_close($od);

            $responseText['content'] = str_replace('{br}', "\n", $responseText['content']);
            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($responseText['content']);
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [getKeyword description]
     * @param  [type] $message [description]
     * @param  [type] $source  [description]
     * @return [type]          [description]
     */
    public function getKeyword($message, $source)
    {
        $text    = $message->text;
        $type    = $source->type;
        $groupId = (isset($source->groupId)) ? $source->groupId : "";
        $roomId  = (isset($source->roomId)) ? $source->roomId : "";
        $userId  = $source->userId;

        if (mb_strpos($text, "*") === 0 || mb_strpos($text, "＊") === 0) {
            $response   = false;
            $keywordStr = mb_substr($text, 1);

            $keywords = Keyword::find(
                [
                 "conditions" => "keyword = ?1",
                 "bind"       => [
                    1 => $keywordStr
                 ]
                ]
            );
            
            foreach ($keywords as $keyword) {
                if ($keyword->groupId == 'all' || $keyword->groupId == $roomId || $keyword->groupId == $groupId || $keyword->groupId == $userId) {
                    switch ($keyword->type) {
                        case 1:
                            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($keyword->response);
                            break;

                        case 2:
                            $imageUrl  = 'https://ec2.kusoinlol.com/img/' . $keyword->response . '.jpg';
                            $imageUrl2 = 'https://ec2.kusoinlol.com/img/' . $keyword->response . 's.jpg';
                            $response  = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($imageUrl, $imageUrl2);
                            break;
                        
                        default:
                            $response = false;
                            break;
                    }
                }
            }
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [learnKeyword description]
     * @param  [type] $message [description]
     * @param  [type] $source  [description]
     * @return [type]          [description]
     */
    public function learnKeyword($message, $source)
    {
        // !關鍵字 keyword;response
        $text     = $message->text;
        $type     = $source->type;
        $groupStr = $type . 'Id';
        $groupId  = (isset($source->$groupStr)) ? $source->$groupStr : "";
        if (mb_strpos($text, "!關鍵字") === 0 || mb_strpos($text, "！關鍵字") === 0) {
            $response   = false;
            $textStr    = (mb_strpos($text, ' ')) ? mb_substr($text, 5) : false;
            if (!$textStr || !mb_strpos($textStr, ';')) {
                 return false;
            }

            $keywordStr  = mb_substr($textStr, 0, mb_strpos($textStr, ';'));
            $responseStr = mb_substr($textStr, mb_strpos($textStr, ';') + 1);

            $keyword = new Keyword();
            $keyword->projectId = $this->projectId;
            $keyword->groupId   = $groupId;
            $keyword->type      = 1;
            $keyword->keyword   = $keywordStr;
            $keyword->response  = $responseStr;
            $keyword->save();

            if ($keyword->save()) {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('..._〆(°▽°*)');
            } else {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('好像有哪裡壞掉惹');
            }
        } else {
            $response = false;
        }//end if

        return $response;
    }

    /**
     * [learnKeyword description]
     * @param  [type] $message [description]
     * @param  [type] $source  [description]
     * @return [type]          [description]
     */
    public function deleteKeyword($message, $source)
    {
        // !關鍵字 keyword;response
        $text     = $message->text;
        $type     = $source->type;
        $groupStr = $type . 'Id';
        $groupId  = (isset($source->$groupStr)) ? $source->$groupStr : "";
        if (mb_strpos($text, "!刪除關鍵字") === 0 || mb_strpos($text, "！刪除關鍵字") === 0) {
            $response   = false;
            $textStr    = (mb_strpos($text, ' ')) ? mb_substr($text, 7) : false;
            if (!$textStr) {
                 return false;
            }

            $keyword = Keyword::findFirst(
                [
                 "conditions" => "keyword = ?1 and groupId = ?2",
                 "bind"       => [
                    1 => $textStr,
                    2 => $groupId,
                 ]
                ]
            );

            if (!$keyword) {
                return new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('真的有這個關鍵字嗎');
            }

            if ($keyword->delete()) {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($textStr . "被刪掉惹啦");
            } else {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('好像有哪裡壞掉惹');
            }
        } else {
            $response = false;
        }//end if

        return $response;
    }
}
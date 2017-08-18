<?php
namespace Anthony\LineBot\Model\Service;

use \Exception;

/**
 * 
 */
class CommonService
{
    /**
     * [getHsingFile description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function common($message)
    {
        // http://17sing.tw/share_song/index.html?sid=
        $text = $message->text;
        if (mb_strpos($text, "!help") === 0 || mb_strpos($text, "！help") === 0) {
            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("別急我還沒寫好");
        } else {
            $response = false;
        }//end if

        return $response;
    }
}
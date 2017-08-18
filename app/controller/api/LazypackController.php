<?php

namespace Anthony\Hsing\Controller\Api;

use Anthony\Hsing\Controller\BaseController;
use Anthony\Hsing\Model\Service\LazyPackService;
use Anthony\Hsing\Model\Db\LazyPack;

use Phalcon\Mvc\Model\Query;
use \Exception;

/**
 * 
 */
class LazypackController extends BaseController
{

    /**
     * [webHookAction description]
     * @return [type] [description]
     */
    public function webHookAction()
    {
        date_default_timezone_set("Asia/Taipei");

        $lazyPackService = new LazyPackService();

        // 將收到的資料整理至變數
        $receive      = json_decode(file_get_contents("php://input"));
        $text         = $receive->events[0]->message->text;
        $from         = $receive->events[0]->replyToken;
        $content_type = $receive->events[0]->message->type;

        // $text = $receive->text;
        // 0: 文字
        // 1: 圖片
        // 99: debug
        $keywordAry = [
                       "Jin"  => array('type' => 0, 'param' => 'Jin：我女友比女優正多了。'),
                       "json" => array('type' => 0, 'param' => json_encode($receive)),
                       "重點" => array('type' => 99, 'param' => "(筆記!)", "json" => json_encode($receive)),
                       "懶人包" => array('type' => 98, 'param' => "歐齁歐～", "json" => json_encode($receive)),
                       "拜託刪掉" => array('type' => 97, 'param' => "歐齁歐～", "json" => json_encode($receive)),
                       "小秘密" => array('type' => 96, 'param' => "歐齁歐～", "json" => json_encode($receive)),
                      ];

        if (mb_strpos($text, "!") === 0 || mb_strpos($text, "！") === 0) {
            if (mb_strpos($text, " ")) {
                $keyword = mb_substr($text, 1, (mb_strpos($text, " ") - 1));
            } else {
                $keyword = mb_substr($text, 1);
            }
            
            if (array_key_exists($keyword, $keywordAry)) {
                $lazyPackService->buildMessage($from, $keywordAry[$keyword]);
                // var_dump($test, $from, $keywordAry, $keyword);exit;
                return;
            }
        }

        if ($text == '!help' || $text == '!幫助' || $text == '！help' || $text == '！幫助') {
            $message = "
筆記只能新增不能刪除～歐齁歐～
-----
輸入\"!重點 xxx\"
可以做筆記，重複的會記錄次數。
-----
輸入\"!懶人包\"
可以顯示今天的懶人包
-----
輸入\"!懶人包 昨天\"
可以顯示昨天的懶人包
-----
輸入\"!懶人包 2017-04-26\"
可以顯示2017-04-26的懶人包
-----
輸入\"!help 取得此訊息內容\"";
        }

        $lazyPackService->sendMessage($from, (string) $message);

        if (!isset($receive->events[0]->source->groupId)) {
            $lazyPack = new LazyPack();
            $lazyPack->groupId     = "not group";
            $lazyPack->type        = 'anthony';
            $lazyPack->content     = $text;
            $lazyPack->count       = 1;
            $lazyPack->date        = date('Y-m-d');
            $lazyPack->messageJson = json_encode($receive);
            $lazyPack->createTime  = date('Y-m-d H:i:s');
            $lazyPack->save();
        }

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
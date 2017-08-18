<?php

namespace Anthony\Hsing\Controller\Api;

use Anthony\Hsing\Controller\BaseController;
use Anthony\Hsing\Model\Service\ProjectService;
use Anthony\Hsing\Model\Service\AvService;
use Phalcon\Mvc\Model\Query;
use \Exception;

class ProjectController extends BaseController
{
    /**
     * [webHookAction description]
     * @return [type] [description]
     */
    public function webHookAction()
    {
        date_default_timezone_set("Asia/Taipei");

        $avService = new AvService();

        // 將收到的資料整理至變數
        $receive      = json_decode(file_get_contents("php://input"));
        $text         = $receive->events[0]->message->text;
        $from         = $receive->events[0]->replyToken;
        $content_type = $receive->events[0]->message->type;

        if (mb_strpos($text, "av") === 0 || mb_strpos($text, "AV") === 0 || mb_strpos($text, "Av") === 0) {
            $keyword  = mb_substr($text, 3);
            $response = $avService->avDetailByNo($keyword);
            // $response = "test\n" . $content_type . "\n" . $text;
        }


        if ($response) {
            $avService->sendMessage($from, $response);
        }
    }
}
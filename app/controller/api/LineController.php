<?php

namespace Anthony\LineBot\Controller\Api;

use Anthony\LineBot\Controller\BaseController;
use Anthony\LineBot\Model\Service\LineService;
use Anthony\LineBot\Model\Db\Project;
use Anthony\LineBot\Model\Db\ProjectDetail;
use Anthony\LineBot\Model\Db\Log;

use Phalcon\Mvc\Model\Query;
use \Exception;

/**
 * 
 */
class LineController extends BaseController
{

    /**
     * [webHookAction description]
     * @return [type] [description]
     */
    public function webHookAction($projectId)
    {
        // 將收到的資料整理至變數
        $receive = json_decode(file_get_contents("php://input"));

        if ($projectId == 60) {
            // 記錄傳入資料
            $log = new Log();
            $log->type = 'input';
            $log->json = json_encode($receive);
            $log->createTime = date('Y-m-d H:i:s');
            $log->save();
        }

        // type, replyToken, timestamp
        $event = $receive->events[0];

        // userId, type
        $source = $event->source;

        // type, id, text
        $message = $event->message;

        // 取得project的token
        $project = Project::findFirst($projectId);
        if (!$project) {
            throw new Exception("load project data fail");
        }

        $bot = new \LINE\LINEBot(
            new \LINE\LINEBot\HTTPClient\CurlHTTPClient($project->token),
            [
             'channelSecret' => $project->channelSecret
            ]
        );

        // 取得project開啟的function
        $functions = ProjectDetail::find("projectId = '" . $projectId . "'");
        $functionAry = [];

        foreach ($functions as $function) {
            if ($function->status == 1) {
                $functionAry[] = $function->functionId;
            }
        }

        // 根據開啟的function 取得對應的lineObj
        $lineService = new LineService($projectId);
        $responseAry = $lineService->loadFunction($functionAry, $message, $source);

        // 建立多重回覆
        $multiMessageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
        foreach ($responseAry as $response) {
            $multiMessageBuilder->add($response);
        }
        
        // 傳送回應訊息
        $test = $bot->replyMessage($event->replyToken, $multiMessageBuilder);

        if (json_encode($test) != "{}") {
            // 記錄傳入資料
            $log = new Log();
            $log->type = 'response';
            $log->json = json_encode($test);
            $log->createTime = date('Y-m-d H:i:s');
            $log->save();
        }
    }
}
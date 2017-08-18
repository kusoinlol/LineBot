<?php
namespace Anthony\LineBot\Model\Service;

use Anthony\LineBot\Model\Db\FunctionList;
use Anthony\LineBot\Model\Service\Song;
use Anthony\LineBot\Model\Service\Talk;
use Anthony\LineBot\Model\Service\Exchange;
use Anthony\LineBot\Model\Service\LazyPack;
use \Exception;

/**
 * 
 */
class LineService extends \Phalcon\Mvc\User\Component
{

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
        define(SONG_GET_HSING_FILE, 1);
        define(SONG_GET_RC_FILE, 2);
        define(SONG_GET_CHANGBA_FILE, 3);
        define(SONG_GET_LYRIC, 4);
        define(SONG_GET_HSING_SONG, 5);
        define(TALK_TALK_TO_BOT, 6);
        define(TALK_KEYWORD, 7);
        define(TALK_LEARN_KEYWORD, 8);
        define(EXCHANGE_GET_EXCHANGE_RATE, 9);
        define(EXCHANGE_OTHER_2_TW, 10);
        define(EXCHANGE_TW_2_OTHER, 11);
        define(LAZYPACK_NOTE, 12);
        define(LAZYPACK_SHOW, 13);
        define(TALK_DELETE_KEYWORD, 14);
        define(COMMON_COMMON, 15);
    }

    public function loadFunction($functionAry, $message, $source)
    {
        $responseAry = [];
        $needSourceAry = [TALK_KEYWORD, TALK_LEARN_KEYWORD, TALK_DELETE_KEYWORD, LAZYPACK_NOTE, LAZYPACK_SHOW];
        foreach ($functionAry as $functionId) {
            $function     = FunctionList::findFirst($functionId);
            $class        = $function->class;
            $functionName = $function->functionName;
            $namespaceStr = 'Anthony\LineBot\Model\Service\\' . $class . 'Service';

            try {
                $service = new $namespaceStr($this->projectId);
                if (in_array($functionId, $needSourceAry)) {
                    $response = $service->$functionName($message, $source);
                } else {
                    $response = $service->$functionName($message);
                }
            } catch (\Exception $e) {
                echo "載入$class, $functionName失敗。";
                continue;
            }

            if ($response) {
                $responseAry[] = $response;
            }
        }//end foreach

        return $responseAry;
    }
}
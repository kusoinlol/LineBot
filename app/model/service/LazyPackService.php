<?php
namespace Anthony\LineBot\Model\Service;

use Anthony\LineBot\Model\Db\FunctionList;
use Anthony\LineBot\Model\Db\LazyPack;
use \Exception;

/**
 * 
 */
class LazyPackService extends \Phalcon\Mvc\User\Component
{

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function note($message, $source)
    {
        $text      = $message->text;
        $type      = $source->type;
        $groupStr  = $type . 'Id';
        $groupId   = (isset($source->$groupStr)) ? $source->$groupStr : "";
        $returnStr = "";

        if (mb_strpos($text, "!重點") === 0 || mb_strpos($text, "！重點") === 0) {
            $content  = mb_substr($text, (mb_strpos($content, '重點') + 3));
            $lazyPack = LazyPack::findFirst("groupId = '" . $groupId . "' and content = '" . $content . "'");
            if ($lazyPack) {
                $lazyPack->count++;
                $returnStr = $content . " 已經被劃了" . $lazyPack->count . "次重點了！";
            } else {
                $lazyPack = new LazyPack();

                $lazyPack->groupId     = $groupId;
                $lazyPack->type        = 'point';
                $lazyPack->content     = $content;
                $lazyPack->count       = 1;
                $lazyPack->date        = date('Y-m-d');
                $lazyPack->messageJson = "";
                $lazyPack->createTime  = date('Y-m-d H:i:s');

            }
            
            if ($lazyPack->save()) {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('..._〆(°▽°*) ' . $returnStr);
            } else {
                $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('好像有哪裡壞掉惹');
            }
        } else {
            $response = false;
        }//end if

        return $response;
    }

    public function show($message, $source)
    {
        $text      = $message->text;
        $type      = $source->type;
        $groupStr  = $type . 'Id';
        $groupId   = (isset($source->$groupStr)) ? $source->$groupStr : "";
        $returnStr = "";
        $message   = "";
        if (mb_strpos($text, "!懶人包") === 0 || mb_strpos($text, "！懶人包") === 0) {
            $date = mb_substr($text, (mb_strpos($content, '懶人包') + 4));
            if ($date == "") {
                $date = date("Y-m-d");
            }

            if (mb_strpos($date, "昨天")) {
                $date = date("Y-m-d", mktime(0, 0, 0, date("m"), (date("d") - 1), date("Y")));
            }

            $message = $date . " 懶人包來囉～歐齁歐～\n\n";

            $lazyPacks = LazyPack::find(
                [
                 "groupId = '" . $groupId . "' and date = '" . $date . "'",
                 "order" => "count DESC",
                ]
            );


            if (count($lazyPacks) == 0) {
                $message = $date . " 沒人畫重點耶";
            } else {
                foreach ($lazyPacks as $lazyPack) {
                    $content  = $lazyPack->content;
                    $message .= $lazyPack->count . " - " . $content . "\n";
                }
            }

            $response = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
        } else {
            $response = false;
        }//end if

        return $response;
    }
}
<?php
use Anthony\Hsing\Model\Service\AvService;
use JonnyW\PhantomJs\Client;
use Anthony\Hsing\Model\Db\AvInfo;

/**
 * Class AuthTask
 */
class AvTask extends \Phalcon\CLI\Task
{
    public function getAvDataFrom141Action($arg)
    {
        $redis = new redis();
        $redis->connect('127.0.0.1', 6379);
        $lastTime = $redis->get('time');

        if ((time() - 1200) < $lastTime) {
            echo date('h:i:s', $lastTime) . PHP_EOL;
            echo $redis->get('page') . PHP_EOL;
            exit;
        }

        $page = $arg[0];
        if ($page == 'task') {
            $page = $redis->get('page');
        }

        if ($page == 0) {
            $page = 10000;
        }

        // http://av.nightlife141.com/Japan-AV/new/p9810
        $avCount = 0;
        while ($page != 0) {
            echo "page =>" . $page . PHP_EOL;

            $client = Client::getInstance();

            $request = $client->getMessageFactory()->createRequest('http://av.nightlife141.com/enter.php?agree=1&from=/Japan-AV/new/p' . $page, 'GET');

            $response = $client->getMessageFactory()->createResponse();

            // Send the request
            $responseObj  = $client->send($request, $response);
            $responseText = $responseObj->content;
            $responseText = mb_convert_encoding($responseText, 'utf8');
            $responseText = preg_replace('/\R/', '', $responseText);
            $responseText = substr($responseText, strpos($responseText, '<div class="section">      <ul>'));

            // var_dump($responseText);exit;
            $startStr  = '<li class="av-gallery-item">';
            $avInfoAry = explode($startStr, $responseText);
            // var_dump($avInfoAry);exit;
            // $responseStr = "查詢 " . $avNo . " 結果如下：\n\n";
            // $endStr   = '<h3 class="num">';
            // $strPos   = strpos($responseText, $startStr);
            // $endPos   = strpos($responseText, $endStr);
            foreach ($avInfoAry as $key => $avInfo) {
                // var_dump($avInfo);
                $avName = $this->catchStr($avInfo, '<h4>', '</h4>');
                $avPic  = $this->catchStr($avInfo, '<img src="', '" alt=');
                $avPic  = str_replace('s.jpg', '.jpg', $avPic);
                $avNo   = $this->catchStr($avPic, 'http://v1.nl141.com/', '.jpg');
                $avNo   = substr($avNo, 2);
                // var_dump($avPic, $avNo);

                if (strpos($avInfo, '<h4>') != false) {
                    $avData = AvInfo::findFirst("avNo='" . $avNo . "'");
                    
                    if (!$avData) {
                        $avCount++;
                        echo "$avCount - [$avNo]$avName\n$avPic\n-----\n";
                        $avData = new AvInfo();

                        $avData->avNo    = $avNo;
                        $avData->avName  = $avName;
                        $avData->picLink = $avPic;
                        $avData->save();
                    }
                }
            }//end foreach
            $page--;
            $redis->set('time', time());
            $redis->set('page', $page);
        }//end while
    }

    public function getAvInfoByAvNoAction($arg)
    {
        $avDataAry = AvInfo::find(
            [
                "tag is null and avNo != '' and picLink like '%http%' and avName like '%[%'",
                "limit" => 1000,
            ]
        );

        // $avDataAry = AvInfo::find(
        //     [
        //         "avNo = 'SRXV808'",
        //         "limit" => 1000,
        //     ]
        // );

        if (count($avDataAry) == 0) {
            exit;
        }

        foreach ($avDataAry as $avData) {
            $avNo = $avData->avNo;
            if (mb_strlen($avNo) > 15) {
                continue;
            }
            
            $tag  = '';
            var_dump($avNo);
            $client = Client::getInstance();

            $request = $client->getMessageFactory()->createRequest('http://av.nightlife141.com/enter.php?agree=1&from=/' . $avNo, 'GET');

            $response = $client->getMessageFactory()->createResponse();

            // Send the request
            $responseObj  = $client->send($request, $response);
            $responseText = $responseObj->content;
            // var_dump($responseText);exit;
            $responseText = mb_convert_encoding($responseText, 'utf8');
            $responseText = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $responseText);

            // av type
            $type = $this->catchStr($responseText, '</em>類別：', '</em>上架：');
            $type = $this->catchStr($type, '<a href="', '類" class="globalText">');
            $type = mb_substr($type, mb_strpos($type, '<a href="'));
            $type = mb_substr($type, mb_strpos($type, 'title="') + 7);
            // $type = $this->catchStr($type, '<a href="/1" title="', '類" class="globalText">');
            // var_dump($type);exit;
            // av name
            $name = $this->catchStr($responseText, '<title>', ' - AV資料庫</title>');
            // tag
            $tag = $this->catchStr($responseText, '<meta name="keywords" content="', '"><meta name="viewport"');

            $avData->type   = $type;
            $avData->avName = $name;
            $avData->tag    = $tag;
            $avData->save();
            var_dump($type, $name, $tag);
            echo '----' . PHP_EOL;
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
        $StaPos = mb_strpos($Str, $StaKey, 0, 'UTF-8');
        $EndPos = mb_strpos($Str, $EndKey, 0, 'UTF-8');
        $StaLen = mb_strlen($StaKey, 'UTF-8');
        $EndLen = mb_strlen($EndKey, 'UTF-8');
        // var_dump(($StaPos + $StaLen), (($EndPos - ($StaPos + $StaLen))), $StaLen, $EndLen);

        $CatchKey = mb_substr($Str, ($StaPos + $StaLen), (($EndPos - ($StaPos + $StaLen))), 'UTF-8');

        return $CatchKey;
    }
}
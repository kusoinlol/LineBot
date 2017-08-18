<?php
namespace Anthony\Hsing\Model\Service;

use \Exception;
use Anthony\Hsing\Model\Dao\MusicDao;
use JonnyW\PhantomJs\Client;

/**
 * 
 */
class AvService
{

    /**
     * [sendMessage description]
     * @param  [type] $to      [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function sendMessage($to, $message)
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('o0y+9usw1r3YbJ4NzAGazP7PJYsishSvts6Tvh7yrLYdff7rGrp4WLvO8PsIAWllYHt3Le2P/DtK16QCc1gW6W2Pt29y1fas6YN1JIyXHpaLd+46TsnOy7q5ZY/M5I0TKrc8o8cVPaDffopyPcTlkAdB04t89/1O/w1cDnyilFU=');

        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '0e6f307bd49f2f14a9b9192ab0c46960']);

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);

        $response = $bot->replyMessage($to, $textMessageBuilder);
        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }

    /**
     * [searchSong description]
     * @param  [type] $keyword [description]
     * @return [type]          [description]
     */
    public function avDetailByNo($avNo)
    {
        $client = Client::getInstance();

        $request = $client->getMessageFactory()->createRequest('http://av.nightlife141.com/enter.php?agree=1&from=/?keyword=' . urlencode($avNo), 'GET');

        $response = $client->getMessageFactory()->createResponse();

        // Send the request
        $responseObj  = $client->send($request, $response);
        $responseText = $responseObj->content;
        $responseText = mb_convert_encoding($responseText, 'utf8');
        $responseText = preg_replace('/\R/', '', $responseText);
        $responseText = substr($responseText, strpos($responseText, '<div class="section">'));

        $startStr  = '<li class="av-gallery-item">';
        $avInfoAry = explode($startStr, $responseText);
        // var_dump($avInfoAry);exit;
        $responseStr = "查詢 " . $avNo . " 結果如下：\n\n";
        // $endStr   = '<h3 class="num">';
        // $strPos   = strpos($responseText, $startStr);
        // $endPos   = strpos($responseText, $endStr);
        $avCount = 0;
        foreach ($avInfoAry as $avInfo) {
            if ($avCount >= 5) {
                continue;
            }

            // var_dump($avInfo);
            $avName = $this->catchStr($avInfo, '<h4>', '</h4>');
            $avPic  = $this->catchStr($avInfo, '<img src="', '" alt=');
            $avPic  = str_replace('s.jpg', '.jpg', $avPic);
            $avNo   = $this->catchStr($avInfo, '<b>', '</b>');

            if (strpos($avInfo, '<h4>') != false) {
                $responseStr .= "[$avNo]$avName\n$avPic\n-----\n";
                $avCount++;
            }
        }

        // $avInfo = substr($responseText, $strPos, ($endPos - $strPos));
        // $avName = $this->catchStr($avInfo, '<h4>', '</h4>');
        // $avPic  = $this->catchStr($avInfo, '<img src="', '" alt=');

        // var_dump($responseStr);exit;
        return mb_convert_encoding($responseStr, 'utf8');
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
        $StaPos = strpos($Str, $StaKey);
        $EndPos = strpos($Str, $EndKey);
        $StaLen = strlen($StaKey);
        $EndLen = strlen($EndKey);

        $CatchKey = substr($Str, ($StaPos + $StaLen), (($EndPos - ($StaPos + $StaLen))));

        return $CatchKey;
    }
}
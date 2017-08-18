<?php

namespace Anthony\Hsing\Controller\Api;

use Anthony\Hsing\Controller\BaseController;

use Anthony\Hsing\Model\Db\UploadMan;
use Phalcon\Mvc\Model\Query;
use \Exception;

class TestController extends BaseController
{
    public function demoAction()
    {
        try {
            echo "TestController" . PHP_EOL;
            $test = UploadMan::findFirst();
            foreach ($test as $key => $value) {
                var_dump($key);
                var_dump($value);
            }
            exit;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
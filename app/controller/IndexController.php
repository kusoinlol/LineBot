<?php

namespace Anthony\LineBot\Controller;

/**
 * Index Controller
 *
 * @category CartPlugin
 * @package  Cart
 * @author   XXX <XXX@hiiir.com>
 * @license  Hiiir shopping.friday.tw
 * @link     http://shopping.friday.tw
 */
class IndexController extends \Phalcon\Mvc\Controller
{

    /**
     * Not Found
     *
     * @return void
     * @throws HttpException
     */
    public function notFoundAction()
    {
        echo "notFound from index.php";
        exit();
    }

}
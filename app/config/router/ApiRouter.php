<?php
/**
 * Api Router
 *
 * @category Router
 * @package  Api
 * @author   Anthonyhsiao
 */
class ApiRouter extends Phalcon\Mvc\Router\Group
{
    /**
     * Router Group init
     *
     * @return void
     */
    public function initialize()
    {
        $this->setPrefix('/api');
        $this->setPaths(['namespace' => 'Anthony\LineBot\Controller\Api']);

        $this->addGet(
            '/{projectId}/linebot',
            [
             'controller' => 'line',
             'action'     => 'webHook',
            ]
        );

        $this->addPost(
            '/{projectId}/linebot',
            [
             'controller' => 'line',
             'action'     => 'webHook',
            ]
        );
    }
}
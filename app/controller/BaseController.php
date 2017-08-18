<?php

namespace Anthony\LineBot\Controller;

use ReflectionClass;

/**
 * Controller Base
 */
class BaseController extends \Phalcon\Mvc\Controller
{
    /**
     * [initialize description]
     * @return [type] [description]
     */
    protected function initialize()
    {
        $action = $this->dispatcher->getActionName();
        $controllerRefClass = new ReflectionClass($this->dispatcher->getHandlerClass());
        $controller         = $controllerRefClass->getShortName();
    }



    /**
     * ResponsePure
     *
     * @param array   $content      輸出內容
     * @param boolean $isJson       是否需要json_encode
     * @param integer $filterOption 過濾選項
     * @param array   $columns      欄位
     *
     * @return void
     */
    public function responsePure($content = null, $isJson = false, $filterOption = 0, $columns = array())
    {

        $dispatcher    = $this->di->get('dispatcher');
        // $this->view->disable();
        if ($isJson) {
            $this->response->setStatusCode("200", "OK");
            $this->response->setHeader("Content-Type", "application/json");

            if ($filterOption > 0) {
                $content = $this->arrayFilterEmpty($content, $filterOption, $columns);
            }
            
            $this->response->setContent(json_encode($content));
        } else {
            $this->response->setContent($content);
        }
    }

    
    /**
     * 去除空白
     *
     * @param array   $content      內容
     * @param integer $filterOption 過濾選項
     * @param array   $columns      欄位
     *
     * @return array
     */
    private function arrayFilterEmpty($content, $filterOption = 1, $columns = array())
    {
        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $content[$key] = $this->arrayFilterEmpty($content[$key], $filterOption);
            }

            if (! empty($columns)) {
                if (! in_array($key, $columns)) {
                    continue;
                }
            }
            
            if ($filterOption == self::FILTER_NULL_VALUE) {
                if (is_null($content[$key])) {
                    unset($content[$key]);
                }
            } else if ($filterOption == self::FILTER_EMPTY) {
                if (empty($content[$key]) && ! is_array($content[$key])) {
                    unset($content[$key]);
                }
            } else if ($filterOption == self::CONVERT_NULL_TO_STRING) {
                if (is_null($content[$key])) {
                    $content[$key] = "";
                }
            } else if ($filterOption == self::CONVERT_EMPTY_VALUES_TO_STRING) {
                if (empty($content[$key]) && ! is_array($content[$key]) && $content[$key] != 0) {
                    $content[$key] = "";
                }
            }
        }//end foreach
        return $content;
    }


}
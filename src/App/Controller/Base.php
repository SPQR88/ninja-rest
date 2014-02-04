<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 10.12.13
 * Time: 11:37
 */

namespace App\Controller {

    /**
     * Base controller
     *
     * @package App\Controller
     */
    class Base extends \Phalcon\DI\Injectable
    {
        /**
         * MIME type
         *
         * @var string
         */
        protected $contentType = 'application/json';

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->setDI(\Phalcon\DI::getDefault());
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
        }

        public function beforeDispatch(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
        {

        }

        /**
         * @param \Phalcon\Mvc\Dispatcher $dispatcher
         */
        public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
        {
            if ($this->request->isOptions()) {
                $this->response->setHeader('Access-Control-Allow-Origin', "*");
                $this->response->setHeader('Access-Control-Allow-Methods', "GET, POST, PUT, DELETE, OPTIONS");
                $this->response->setHeader('Access-Control-Allow-Headers', "Accept, Accept-Encoding, Accept-Language, Referrer, Host, X-Auth-Token, X-Auth-Request-Token, Keep-Alive, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type");
                $this->response->setHeader('Access-Control-Allow-Headers', "x-requested-with, accept, accept-language, x-auth-token, x-auth-request-token, user-agent, content-type, if-modified-since, origin, host, accept-encoding, connection");
                //$this->response->setHeader('Access-Control-Max-Age', 1728000);
                $this->response->setHeader('Access-Control-Allow-Credentials', "true");
                $this->response->send();
            }

            $this->response->setHeader('Access-Control-Allow-Origin', "*");
        }

        /**
         * @param \Phalcon\Mvc\Dispatcher $dispatcher
         */
        public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
        {
            $returnedValueFromAction = $dispatcher->getReturnedValue();
            $httpResponse            = $this->getDI()->getResponse();

            if (\is_array($returnedValueFromAction)) {
                $returnedValueFromAction = \json_encode($returnedValueFromAction);
            }

            $expireDate = new \DateTime();
            $expireDate->modify('-10 minutes');

            if ((int)$dispatcher->getParam('status') > 0) {
                $httpResponse->setStatusCode(
                    (int)$dispatcher->getParam('status')[0],
                    (string)$dispatcher->getParam('status')[1]
                );
            }

            $httpResponse->setExpires($expireDate);
            $httpResponse->setContentType($this->contentType);
            $httpResponse->setContent($returnedValueFromAction);
            $httpResponse->send();
        }
    }
}
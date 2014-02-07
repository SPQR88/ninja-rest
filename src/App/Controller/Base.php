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

        /**
         * @param \Phalcon\Events\Event $event
         * @param \Phalcon\Mvc\Dispatcher $dispatcher
         */
        public function beforeDispatch(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
        {

        }

        /**
         * @param \Phalcon\Mvc\Dispatcher $dispatcher
         */
        public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
        {
            $headers = $this->request->getHeaders();

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

            if (\array_key_exists('RANGE', $headers)) {
                if (preg_match('/^items\=([0-9]+)\-([0-9]+)$/i', $headers['RANGE'], $rangeMatch)) {
                    $this->dispatcher->setParam("range", [$rangeMatch[1], $rangeMatch[2]]);
                }
            }

            $annotations = $this->annotations->getMethod(
                $dispatcher->getActiveController(),
                $dispatcher->getActiveMethod()
            );

            if ($annotations->has('Access')) {
                $annotation = $annotations->get('Access');

                if ($annotation->getNamedArgument(0) == 'private') {
                    $headers = $this->request->getHeaders();

                    $dispatcher->forward([
                        'action' => 'unauthorized'
                    ]);
                }
            }

            if ($annotations->has('Offline')) {
                $offline = $annotations->get('Offline');

                if ((int)$offline->getNamedArgument(0) === 1) {
                    $dispatcher->forward([
                        'action' => 'offline'
                    ]);
                }
            }
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

            if ((int)$dispatcher->getParam('status')[0] > 0 && \strlen((string)$dispatcher->getParam('status')[1]) > 0) {
                $httpResponse->setStatusCode(
                    (int)$dispatcher->getParam('status')[0],
                    (string)$dispatcher->getParam('status')[1]
                );
            }

            if ($dispatcher->getDI()->getConfiguration()->application->name) {
                $httpResponse->setHeader('Server', $dispatcher->getDI()->getConfiguration()->application->name);
            }

            $httpResponse->setExpires($expireDate);
            $httpResponse->setContentType($this->contentType);
            $httpResponse->setContent($returnedValueFromAction);
            $httpResponse->send();
        }

        /**
         *
         */
        public function unauthorizedAction()
        {
            $this->dispatcher->setParam("status", [401, "Login first"]);
        }

        /**
         *
         */
        public function offlineAction()
        {
            $this->dispatcher->setParam("status", [503, "Temporary unavailable"]);
        }

    }
}
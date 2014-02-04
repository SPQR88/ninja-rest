<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 17:11
 */

namespace App\Controller
{
    /**
     * Базовый класс для всех контроллеров
     *
     * @package App\Controller
     */
    class BaseController extends \Phalcon\DI\Injectable
    {
        /**
         * Язык агента с приоритетом 1
         *
         * @var string
         */
        protected $requestLanguage;

        /**
         * Доступные языки агента
         *
         * @var array
         */
        protected $requestAvailableLanguages;

        /**
         * Конструктор
         */
        public function __construct()
        {
            $this->setDI(\Phalcon\DI::getDefault());

            $this->requestAvailableLanguages = $this->request->getLanguages();
            $this->requestLanguage           = $this->request->getBestLanguage();
            $this->view->language            = $this->requestLanguage;
        }

        public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
        {

        }
        /**
         * @param \Phalcon\Mvc\Dispatcher $dispatcher
         */
        public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
        {

            $this->view->render(\lcfirst($dispatcher->getControllerName()), $dispatcher->getActionName());
        }

        public function beforeNotFoundAction(\Phalcon\Mvc\Dispatcher $dispatcher)
        {
        }

        public function beforeException()
        {
        }
    }
}

 
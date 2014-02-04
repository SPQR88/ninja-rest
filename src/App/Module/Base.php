<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 22:19
 */

namespace App\Module {

    /**
     * Class Base
     * @package Module
     */
    class Base extends \Phalcon\DI\Injectable implements \Phalcon\Mvc\ModuleDefinitionInterface
    {
        /**
         * Регистрация дополнительных классов
         */
        public function registerAutoloaders($diInterface)
        {

        }

        /**
         * Регистрация сервисов
         */
        public function registerServices($di)
        {
            $di->set('dispatcher', function () use ($di) {
                $dispatcher = new \Phalcon\Mvc\Dispatcher();
                $dispatcher->setDefaultNamespace(\App\Util::arrayToNamespace([
                    "Module",
                    \ucfirst($di->getShared('router')->getModuleName()),
                    "Controller"
                ]));

                return $dispatcher;
            });
        }
    }
}
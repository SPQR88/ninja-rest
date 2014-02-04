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

            $di->set('view', function () use ($di) {
                $viewDirectory = \App\Util::arrayToPath([
                    \App::$rootDir,
                    "application",
                    $di->getResolver()->id,
                    "template",
                    "Module",
                    \ucfirst($di->getShared('router')->getModuleName())
                ], true, false);

                $viewCompiledDirectory = \App\Util::arrayToPath(
                    [
                        \App::$rootDir,
                        "tmp",
                        "cache",
                        "template",
                    ],
                    true,
                    false
                );

                $view = new \Phalcon\Mvc\View();
                $view->setViewsDir($viewDirectory);
                $view->registerEngines(
                    [
                        '.volt' => function ($view, $di) use ($viewCompiledDirectory) {
                                $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                                $volt->setOptions([
                                    'compiledPath' => $viewCompiledDirectory,
                                    'stat' => true,
                                    'compileAlways' => true
                                ]);

                                return $volt;
                            }
                    ]
                );

                return $view;
            });
        }
    }
}
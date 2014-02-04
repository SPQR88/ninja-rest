<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 11:48
 */

namespace App {
    /**
     * Class Bootstrap
     * @package App
     */
    class Bootstrap
    {
        /**
         * @var \Phalcon\DI\FactoryDefault
         */
        public $di;

        /**
         * Префикс для вызываемых методов
         *
         * @var string
         */
        protected $getterKey;

        /**
         * @var string
         */
        protected $defaultModule, $defaultAction = "index";

        protected $defaultController = 'home';

        /**
         * Конструктор
         */
        public function __construct($resolve)
        {
            $this->di = new \Phalcon\DI\FactoryDefault;

            $this->di->set('resolver', function () use ($resolve) {
                return $resolve;
            });
        }

        /**
         * Получение префикса для вызываемых методов
         *
         * @param $key
         * @return \App\Bootstrap
         */
        public function __get($key)
        {
            $this->getterKey = $key;

            return $this;
        }

        /**
         * Магический метод для инициализации компонентов
         * $bootstrap->initialize->configuration()->database();
         *
         * @param $method
         * @param $arguments
         * @return \App\Bootstrap
         */
        public function __call($method, $arguments)
        {
            if (\strlen($method) > 0) {
                $method = $this->formatCallingMethodName($method);

                if (\method_exists($this, $method)) {
                    return $this->$method($arguments);
                }
            }

            return $this;
        }

        /**
         * @param $methodName
         * @return string
         */
        protected function formatCallingMethodName($methodName)
        {
            return \strtolower($this->getterKey) . \strtolower(\ucfirst($methodName));
        }

        /**
         * Инициализация конфигурации
         *
         * @return \App\Bootstrap
         */
        protected function initializeConfiguration()
        {
            $di = $this->di;
            $this->di->setShared("configuration", function () use ($di) {
                try {

                    $pathToConfig = \App\Util::arrayToPath(
                        [
                            \App::$rootDir,
                            "application",
                            $di->getResolver()->id,
                            "config",
                            "config.json"
                        ]
                    );

                    return new \Phalcon\Config\Adapter\Json($pathToConfig);
                } catch (\Exception $e) {
                    print $e->getMessage();
                }

            });

            return $this;
        }

        /**
         * Инициализация Базы данных
         *
         * @return \App\Bootstrap
         */
        protected function initializeDatabase()
        {
            $di = $this->di;
            $this->di->setShared("database", function () use ($di) {
                $config = $di->getConfiguration()->toArray();
                if (\array_key_exists('database', $config)) {
                    $config = $config['database'];
                    $defaultConnection = $config['defaultConnection'];
                    $classPath = "\\Phalcon\\Db\\Adapter\\Pdo\\" . $config['connections'][$defaultConnection]['vendor'];

                    return new $classPath($config['connections'][$defaultConnection]['connection']);
                }
            });

            return $this;
        }

        /**
         * Инициализация сессии
         *
         * @return \App\Bootstrap
         */
        protected function initializeSession()
        {
            $di = $this->di;
            $this->di->setShared("session", function () use ($di) {
                $session = new \Phalcon\Session\Adapter\Files([
                    'uniqueId' => $di->configuration->application->session->key
                ]);

                return $session;
            });

            return $this;
        }

        /**
         * Инициализация Http Request
         *
         * @return \App\Bootstrap
         */
        protected function initializeRequest()
        {
            $this->di->setShared("request", function () {
                return new \Phalcon\Http\Request();
            });

            return $this;
        }

        /**
         * Инициализация Http Response
         *
         * @return \App\Bootstrap
         */
        protected function initializeResponse()
        {
            $this->di->setShared("response", function () {
                return new \Phalcon\Http\Response();
            });

            return $this;
        }

        /**
         * @return \App\Bootstrap
         */
        protected function initializeCookie()
        {
            $this->di->setShared("cookie", function () {
                return new \Phalcon\Http\Cookie();
            });

            return $this;
        }

        public function initializeCache()
        {
            $this->di->setShared("cache", function () {
                //return new \Phalcon\Http\Cookie();
            });

            return $this;
        }

        /**
         * Инициализация журнала
         *
         * @return \App\Bootstrap
         */
        protected function initializeLogger()
        {
            $di = $this->di;
            $this->di->setShared("logger", function () use ($di) {
                $pathToLogFile = \App\Util::arrayToPath(
                    [
                        \App::$rootDir,
                        "logs",
                        \sprintf("%s-%s.main.log", $di->getResolver()->id, $di->getResolver()->host)
                    ]
                );

                return new \Phalcon\Logger\Adapter\File($pathToLogFile);
            });

            return $this;
        }

        /**
         * Инициализация View (Volt)
         *
         * @return \App\Bootstrap
         */
        protected function initializeTemplate()
        {
            $this->di->setShared("template", function () {
                $viewDirectory = \App\Util::arrayToPath(
                    [
                        \App::$rootDir,
                        "template"
                    ],
                    true,
                    false
                );
                $view = new \Phalcon\Mvc\View\Simple();

                $view->setViewsDir($viewDirectory);
                $view->registerEngines([
                    ".volt" => "\\Phalcon\\Mvc\\View\\Engine\\Volt",
                ]);
                return $view;
            });

            return $this;
        }

        protected function serviceVolt()
        {

        }

        /**
         * Инициализация View (Volt)
         *
         * @return \App\Bootstrap
         */
        protected function initializeView()
        {
            $di = $this->di;
            $di->setShared("view", function () use ($di) {
                $view = new \Phalcon\Mvc\View();
                $viewDirectory = \App\Util::arrayToPath([
                    \App::$rootDir,
                    "application",
                    $di->getResolver()->id,
                    "template"
                ], true, false);
                $viewCompiledDirectory = \App\Util::arrayToPath([
                    \App::$rootDir,
                    "application",
                    $di->getResolver()->id,
                    "template",
                    "compiled"
                ], true, false);

                $view->setViewsDir($viewDirectory);
                $view->registerEngines([
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

            return $this;
        }

        /**
         * Инициализация конфигурации роутинга
         *
         * @return \App\Bootstrap
         */
        protected function initializeRouteConfiguration()
        {
            $di = $this->di;
            $this->di->setShared('route', function () use ($di) {
                $pathToRouteConfig = \App\Util::arrayToPath([
                    \App::$rootDir,
                    "application",
                    $di->getResolver()->id,
                    "config",
                    "route.json"
                ]);

                return new \Phalcon\Config\Adapter\Json($pathToRouteConfig);
            });

            return $this;
        }

        /**
         * Инициализация роутера
         *
         * @return \App\Bootstrap
         */
        protected function initializeRouter()
        {
            $di = $this->di;
            $di->setShared("router", function () use ($di) {
                $router = new \Phalcon\Mvc\Router();

                if (!$di->getRequest()->get('_url')) {
                    $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
                }

                $route = $di->getRoute();
                //$router->setDefaultModule($this->defaultModule);
                $router->setDefaultController($this->defaultController);
                $router->setDefaultAction($this->defaultAction);

                if ($route) {
                    if ($route->count() > 0) {
                        //$router->setDefaultModule($route->toArray()['module']['modules'][0]);


                        foreach ($route->toArray()['route'] as $moduleName => $moduleObject) {
                            $routerGroupOptions = [
                                'module' => $moduleName,
                            ];

                            if (\array_key_exists('controller', $moduleObject)) {
                                $routerGroupOptions['controller'] = $moduleObject['controller'];
                            }

                            $routerGroup = new \Phalcon\Mvc\Router\Group($routerGroupOptions);

                            if ($moduleObject['rules']) {
                                $routerGroup->setPrefix($moduleObject['prefix']);

                                if (\array_key_exists('host', $moduleObject)) {
                                    $routerGroup->setHostName($moduleObject['host']);

//                                    if (\array_key_exists('host', $route->toArray())) {
//                                        if (\array_key_exists('defaultModule', $route->toArray()['host'])) {
//                                            $router->setDefaultModule($route->toArray()['host']['defaultModule'][$moduleObject['host']]);
//                                        }
//                                    }
                                }

                                foreach ($moduleObject['rules'] as $ruleObject) {
                                    $routeParams = [];
                                    $methodNamePrefix = 'add';
                                    $methodSingle = true;

                                    if (!\is_array($ruleObject['method'])) {
                                        $methodName = (\strlen($ruleObject['method']) > 0) ? \ucfirst($ruleObject['method']) : "";
                                        $methodName = $methodNamePrefix . $methodName;
                                    } else {
                                        $methodName = $methodNamePrefix;
                                        $methodSingle = false;
                                    }

                                    if (\array_key_exists('action', $ruleObject)) {
                                        $routeParams['action'] = $ruleObject['action'];
                                    } else {
                                        $routeParams['action'] = $this->defaultAction;
                                    }

                                    if (\array_key_exists('controller', $ruleObject)) {
                                        $routeParams['controller'] = \ucfirst($ruleObject['controller']);
                                    } else {
                                        $routeParams['controller'] = \ucfirst($this->defaultController);
                                    }

                                    if (\array_key_exists('module', $ruleObject)) {
                                        $routeParams['module'] = $moduleName;
                                    }

                                    if (\array_key_exists('params', $ruleObject)) {
                                        $routeParams['params'] = $ruleObject['params'];
                                    }

                                    if (!$methodSingle) {
                                        $routerGroup->$methodName($ruleObject['rule'], $routeParams)
                                            ->via(\App\Util::arrayValuesToUpper($ruleObject['method']));

                                    } else {
                                        $routerGroup->$methodName($ruleObject['rule'], $routeParams);
                                    }

//                                    if (\array_key_exists('host', $routeParams)) {
//                                        if ($di->getResolver()->host == $routeParams['host']) {
//                                            $routerGroup->$methodName($ruleObject['rule'], $routeParams)->setHostName($routeParams['host']);
//                                        }
//                                    } else {
//
//                                    }
                                }
                            }

                            $router->mount($routerGroup);
                        }
                    }
                }

                $router->removeExtraSlashes(true);

                return $router;
            });

            return $this;
        }

        /**
         *
         */
        protected function initializeEventsManager()
        {
            $this->di->set('eventsManager', function () {
                $eventsManger = new \Phalcon\Events\Manager();


                return $eventsManger;
            });
        }

        /**
         *
         */
        protected function initializeDispatcher()
        {
            $di = $this->di;
            $di->set('dispatcher', function () use ($di) {
                try {
                    $dispatcher = new \Phalcon\Mvc\Dispatcher();
                    $eventsManager = $di->getEventsManager();
                    //$modules = $di->application->getModules();
//                    $eventsManager->attach("dispatch:beforeForward", function ($event, $dispatcher, array $forward) {
//                        if(isset($forward['module'])){
//
//                            // Check whether the module is registered
//                            if(!isset($modules[ $forward['module'] ])){
//                                throw new \Phalcon\Mvc\Dispatcher\Exception('Module ' . $forward['module'] . ' does not exist.');
//                            }
//
//                            // Check whether module contains meta data
//                            $moduleData = $modules[ $forward['module'] ];
//                            if(!isset($moduleData['metadata']) || !isset($moduleData['metadata']['controllersNamespace'])){
//                                // @todo think of something nice to automatically get controller dir from existing config?
//                                throw new \Phalcon\Mvc\Dispatcher\Exception('Module ' . $forward['module'] . ' does not have meta data. Controller namespace must be specified.');
//                            }
//
//                            // Set controller namespace
//                            $metadata = $moduleData['metadata'];
//                            $dispatcher->setNamespaceName($metadata['controllersNamespace']);
//
//                            // Set controller suffix
//                            if(isset($metadata['controllerSuffix'])){
//                                $dispatcher->setControllerSuffix($metadata['controllerSuffix']);
//                            }
//
//                            // Set action suffix
//                            if(isset($metadata['actionSuffix'])){
//                                $dispatcher->setActionSuffix($metadata['actionSuffix']);
//                            }
//                        }
//                    });

                    $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {

                        //Handle 404 exceptions
//                    if ($exception instanceof DispatchException) {
//                        $dispatcher->forward(array(
//                            'module' => 'index',
//                            'controller' => 'index',
//                            'action' => 'index'
//                        ));
//                        return false;
//                    }
//
//                    //Handle other exceptions
//                    $dispatcher->forward(array(
//                        'module' => 'index',
//                        'controller' => 'index',
//                        'action' => 'index'
//                    ));
//                    print $dispatcher->getModuleName();

                        return false;
                    });
                    $dispatcher->setEventsManager($eventsManager);

                    return $dispatcher;
                } catch (\Exception $e) {
                    print 'a';
                    print $e->getMessage();
                }
            });

            return $this;
        }
    }
}
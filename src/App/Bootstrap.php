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

        /**
         * @var string
         */
        protected $defaultController = 'index';

        /**
         * Constructor
         */
        public function __construct($resolve)
        {
            $this->di = new \Phalcon\DI\FactoryDefault;

            $this->di->set('resolver', function () use ($resolve) {
                return $resolve;
            });
        }

        /**
         * Methods prefix getter
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
         * Register services
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
         * Config
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
         * Db
         *
         * @return \App\Bootstrap
         */
        protected function initializeDatabase()
        {
            $di     = $this->di;
            $config = $config = $di->getConfiguration()->toArray();

            if (\array_key_exists('database', $config)) {
                $defaultConnection = $config['database']['defaultConnection'];

                if (\array_key_exists('connections', $config['database'])) {

                    foreach ($config['database']['connections'] as $connectionName => $connectionData) {
                        $serviceName = "db" . \ucfirst($connectionName);
                        $vendor      = \ucfirst($connectionData['vendor']);
                        $options     = $connectionData['connection'];

                        if ($connectionName == $defaultConnection) {
                            $serviceName = "db";
                        }

                        $this->di->setShared($serviceName, function () use ($di, $vendor, $options) {
                            $classPath = "\\Phalcon\\Db\\Adapter\\Pdo\\" . $vendor;

                            return new $classPath($options);
                        });
                    }
                }
            }

            return $this;
        }

        /**
         * Session
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
         * Http Request
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
         * Http Response
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
         * Cookie
         *
         * @return \App\Bootstrap
         */
        protected function initializeCookie()
        {
            $this->di->setShared("cookie", function () {
                return new \Phalcon\Http\Cookie();
            });

            return $this;
        }

        /**
         * Cache
         *
         * @return $this
         */
        public function initializeCache()
        {
            $this->di->setShared("cache", function () {
                //return new \Phalcon\Http\Cookie();
            });

            return $this;
        }

        /**
         * Logger
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
         * View (template service) (Volt)
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

        /**
         * Routing
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
         * Router
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
         * Event manager
         *
         * @return $this
         */
        protected function initializeEventsManager()
        {
            $this->di->set('eventsManager', function () {
                $eventsManger = new \Phalcon\Events\Manager();


                return $eventsManger;
            });

            return $this;
        }

        /**
         * Dispatcher
         *
         * @return $this
         */
        protected function initializeDispatcher()
        {
            $di = $this->di;
            $di->set('dispatcher', function () use ($di) {
                try {
                    $dispatcher = new \Phalcon\Mvc\Dispatcher();
                    $eventsManager = $di->getEventsManager();

                    $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {
                        return false;
                    });
                    $dispatcher->setEventsManager($eventsManager);

                    return $dispatcher;
                } catch (\Exception $e) {
                    print $e->getMessage();
                }
            });

            return $this;
        }
    }
}
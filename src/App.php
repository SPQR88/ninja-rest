<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 11:47
 */

/**
 * Class App
 */
class App
{
    /**
     * \App\Bootstrap
     *
     * @var mixed
     */
    protected $bootstrap;

    /**
     * @var mixed
     */
    protected $application;

    /**
     * @var string
     */
    public static $rootDir;

    /**
     * @var string
     */
    public static $directorySeparator = \DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    public static $namespaceSeparator = "\x5c";

    public $loader;
    /**
     * Constructor
     */
    public function __construct($loader)
    {
        $this->loader = $loader;
        $this->setRootDirectory()
            ->setErrorHandler()
            ->setExceptionHandler();
    }

    /**
     * DI initializing
     *
     * @param $resolve
     * @return \App
     */
    public function initialize($resolve)
    {
        $this->loader->add("", \App\Util::arrayToPath([
                \App::$rootDir,
                "application",
                $resolve->id,
                "src"
            ], true, false)
        );
        $this->bootstrap = new \App\Bootstrap($resolve);

        $this->bootstrap
            ->initialize
            ->configuration()
            ->database()
            ->request()
            ->response()
            ->cookie()
            ->session()
            ->logger()
            ->eventsManger()
            ->routeConfiguration()
            ->router()
            ->dispatcher()
            ->template()
        ;

        return $this;
    }

    /**
     * Application initialize
     */
    public function application()
    {
        try {
            $resolve = $this->resolveHostToApplication();

            if (null !== $resolve->host && null !== $resolve->id) {
                $this->initialize($resolve);
                $this->application = new \Phalcon\Mvc\Application($this->bootstrap->di);
                $this->registerModules();
                
                print $this->application->handle()->getContent();
            }
        } catch (\Exception $e) {
            $this->bootstrap->di->getLogger()->error($e->getMessage());
        }
    }

    /**
     * Register modules
     *
     * @return void
     */
    protected function registerModules()
    {
        $moduleConfiguration = $this->bootstrap->di->getRoute();
        $result = [];

        if ($moduleConfiguration) {
            $moduleConfigurationAsArray = $moduleConfiguration->toArray();

            foreach ($moduleConfigurationAsArray['module']['modules'] as $moduleName) {
                $classPath = \App\Util::arrayToNamespace([
                        \App\Util::arrayToNamespace($moduleConfigurationAsArray['module']['settings']['namespace']),
                        \ucfirst($moduleName),
                        $moduleConfigurationAsArray['module']['settings']['className']
                    ]
                );
                $result[$moduleName] = [
                    "className" => $classPath
                ];
            }
        }
        $this->application->registerModules($result);
    }

    /**
     * Set root directory
     *
     * @return \App
     */
    protected function setRootDirectory()
    {
        self::$rootDir = \App\Util::parentDirectory(__DIR__);

        return $this;
    }


    /**
     * @TODO
     *
     * @return \App
     */
    protected function setErrorHandler()
    {
        return $this;
    }

    /**
     * @TODO
     *
     * @return \App
     */
    protected function setExceptionHandler()
    {
        return $this;
    }

    /**
     * Resolving host to application names
     * @TODO DNS TXT entry query
     */
    protected function resolveHostToApplication()
    {
        $result      = (object)null;
        $httpRequest = new \Phalcon\Http\Request();
        $httpHost    = $httpRequest->getHttpHost();
        $resolver    = (new \Phalcon\Config\Adapter\Json(
            \App\Util::arrayToPath(
                [
                    \App::$rootDir,
                    "application",
                    "resolve.json"
                ]
            )))->toArray();

        $isSimple = '/^([a-zA-Z0-9\-\.])+$/';

        foreach ($resolver as $applicationId => $applicationHosts) {

            if (\is_array($applicationHosts) && \count($applicationHosts) > 0) {

                foreach ($applicationHosts as $applicationHostname) {

                    if (\preg_match($isSimple, $applicationHostname))
                    {
                        if (\preg_match("/^" . \preg_quote($applicationHostname) . "$/", $httpHost)) {
                            $result->host = $applicationHostname;
                            $result->id   = $applicationId;
                            break;
                        }
                    } else {
                        if (\preg_match("/" . $applicationHostname . "/", $httpHost)) {
                            $result->host = $applicationHostname;
                            $result->id   = $applicationId;
                            break;
                        }
                    }
                }
            }
        }

        if (!$result->id) {
            $result->id   = "default";
            $result->host = $httpHost;
        }

        return $result;
    }
}

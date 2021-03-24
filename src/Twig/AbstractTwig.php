<?php

namespace App\Twig;

abstract class AbstractTwig extends TwigExtension
{
    /**
     * Variable $this->twig.
     *
     * @var Twig\Environment
     */
    protected $twig;

    /**
     * Void __construct().
     */
    public function __construct()
    {
        $this->_twigConfiguration();
        $this->_twigExtension();
        parent::__construct();
    }

    /**
     * Twig Configuration.
     *
     * @return void
     */
    private function _twigConfiguration()
    {
        // Class name dossier twig
        $className = substr(get_class($this), strlen(get_class($this)), -10);

        // Twig Configuration
        $path = APP_VIEW_PATH.strtolower($className);
        $loader = new \Twig\Loader\FilesystemLoader($path);
        $this->twig = new \Twig\Environment(
            $loader, [
                'debug' => true,
                'cache' => APP_CACHE_PATH,
            ]
        );
    }

    /**
     * Variable global twig.
     *
     * @return void
     */
    private function _twigExtension()
    {
        $app = new \stdClass();

        if (isset($_SESSION) && !empty($_SESSION)) {
            $app->session = $_SESSION;
            foreach ($_SESSION as $key => $value) {
                if (in_array($key, ['alert', 'toast']) && !empty($value)) {
                    $app->$key = $_SESSION[$key];
                    $_SESSION[$key] = new \stdClass();
                } else {
                    $app->$key = $_SESSION[$key];
                }
            }
        }

        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addGlobal('app', $app);
        $this->twig->addGlobal('absolute_url', PATH_BASE_TWIG);
    }

    /**
     * Render twig.
     *
     * @param string $template chemin template
     * @param array  $array    tableau paramètres
     *
     * @return void
     */
    public function render(string $template, array $array = [])
    {
        return $this->twig->render($template, $array);
    }
}

<?php

namespace App\Twig;

use App\Traits\ClassMethods;

class TwigExtension
{
    use ClassMethods;

    /**
     * Void __construct().
     */
    public function __construct()
    {
        $this->getFilters();
        $this->getFunctions();
    }

    /**
     * Tableau des filtres twig.
     *
     * @return void
     */
    public function getFilters()
    {
        $target = PATH_ROOT.'/src/Twig/Filter';
        if (is_dir($target)) {
            $dh = opendir($target);
            while (false !== ($filename = readdir($dh))) {
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if (!is_dir($filename) && $extension === 'php') {
                    $file = str_replace('.php', '', $filename);
                    $namespace = 'App\\Twig\\Filter\\'.$file;
                    $class = new $namespace();
                    $methods = $this->classMethods($class);
                    foreach ($methods as $method) {
                        $this->twig->addFilter(new \Twig\TwigFilter($method, [$namespace, $method]));
                    }
                }
            }
        }
    }

    /**
     * Tableau des functions twig.
     *
     * @return void
     */
    public function getFunctions()
    {
        $target = PATH_ROOT.'/src/Twig/Function';
        if (is_dir($target)) {
            $dh = opendir($target);
            while (false !== ($filename = readdir($dh))) {
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if (!is_dir($filename) && $extension === 'php') {
                    $file = str_replace('.php', '', $filename);
                    $namespace = 'App\\Twig\\Function\\'.$file;
                    $class = new $namespace();
                    $methods = $this->classMethods($class);
                    foreach ($methods as $method) {
                        $this->twig->addFunction(new \Twig\TwigFunction($method, [$namespace, $method]));
                    }
                }
            }
        }
    }
}

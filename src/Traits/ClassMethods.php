<?php

namespace App\Traits;

trait ClassMethods
{
    /**
     * Public function classMethods()
     * Retourne les noms des méthodes d'une classe.
     *
     * @param object $nameClass Nom de la class intancier
     *                          Ex: $class = new class();
     * @param string $method    null toutes les methodes,
     *                          get retoune les getters,
     *                          set retourne les setters
     */
    public function classMethods(object $nameClass, string $method = null): array
    {
        $class_methods = get_class_methods(get_class($nameClass));

        if ($method !== null) {
            $methods = [];
            foreach ($class_methods as $name_method) {
                if (substr($name_method, 0, 3) === $method) {
                    array_push($methods, $name_method);
                }
            }
        } else {
            $methods = $class_methods;
        }

        return $methods;
    }
}

<?php

namespace App\Http\Session;

interface SessionInterface
{
    /**
     * Initialise Session.
     *
     * @return void
     */
    public function initialize();

    /**
     * Lit et/ou modifie l'identifiant courant de session
     * session_id ([ string $id ] ) : string.
     *
     * session_id() est utilisé pour récupérer ou définir
     * l'identifiant de session pour la session courante.
     *
     * @return void
     */
    public function setSessionId(string $id);

    /**
     * Lit et/ou modifie l'identifiant courant de session
     * session_id ([ string $id ] ) : string.
     *
     * session_id() est utilisé pour récupérer ou définir
     * l'identifiant de session pour la session courante.
     *
     * @return string|null
     */
    public function getSessionId();

    /**
     * Ajoute une session.
     *
     * @param string $name  name session
     * @param mixed  $value value session
     *
     * @return void
     */
    public function set($name, $value);

    /**
     * Retourne une session.
     *
     * @param string $name comment
     *
     * @return object|array|null
     */
    public function get(string $name);

    /**
     * Ajoute un message flash Alert.
     *
     * @param string $type    error, success, warning, info
     * @param string $message message
     *
     * @return void
     */
    public function addFlash(string $type, string $message);

    /**
     * Ajoute un message Toast.
     *
     * @param string $title   comment
     * @param string $time    comment
     * @param string $message comment
     *
     * @return void
     */
    public function addToast(string $title, string $time, string $message);

    /**
     * Returne le tableau session.
     *
     * @return array|object
     */
    public function all();

    /**
     * Supprime une session.
     *
     * @param string $name nom de session
     *
     * @return void
     */
    public function remove(string $name);

    /**
     * Remet le tableau session à zero.
     *
     * @return void
     */
    public function clear();

    /**
     * Destruction de la session.
     *
     * @return void
     */
    public function destroy();
}

<?php

namespace App\Http\Session;

class Session implements SessionInterface
{
    /**
     * Variable $this->_session.
     *
     * @var array
     */
    private $_session = [];

    /**
     * Void __construct().
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialise Session.
     *
     * @return void
     */
    public function initialize()
    {
        if (!isset($_SESSION)) {
            session_start();
        } else {
            $this->_session = $_SESSION;
        }
    }

    /**
     * Lit et/ou modifie l'identifiant courant de session
     * session_id ([ string $id ] ) : string.
     *
     * session_id() est utilisé pour récupérer ou définir
     * l'identifiant de session pour la session courante.
     *
     * @return void
     */
    public function setSessionId(string $id)
    {
        session_id($id);
    }

    /**
     * Lit et/ou modifie l'identifiant courant de session
     * session_id ([ string $id ] ) : string.
     *
     * session_id() est utilisé pour récupérer ou définir
     * l'identifiant de session pour la session courante.
     */
    public function getSessionId(): ?string
    {
        return session_id();
    }

    /**
     * Ajoute une session.
     *
     * @param string $name  name session
     * @param mixed  $value value session
     *
     * @return void
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        $this->initialize();
    }

    /**
     * Retourne une session.
     *
     * @param string $name comment
     *
     * @return object|array|null
     */
    public function get(string $name)
    {
        if (\array_key_exists($name, $this->_session)) {
            return $this->_session[$name];
        } else {
            return null;
        }
    }

    /**
     * Ajoute un message flash Alert.
     *
     * @param string $type    error, success, warning, info
     * @param string $message message
     *
     * @return void
     */
    public function addFlash(string $type, string $message)
    {
        $this->set('alert', ['type' => $type, 'message' => $message]);
    }

    /**
     * Ajoute un message Toast.
     *
     * @param string $title   comment
     * @param string $time    comment
     * @param string $message comment
     *
     * @return void
     */
    public function addToast(string $title, string $time, string $message)
    {
        $this->set('toast', ['title' => $title, 'time' => $time, 'message' => $message]);
    }

    /**
     * Returne le tableau session.
     *
     * @return array|object
     */
    public function all()
    {
        return $this->_session;
    }

    /**
     * Supprime une session.
     *
     * @param string $name nom de session
     *
     * @return void
     */
    public function remove(string $name)
    {
        if (\array_key_exists($name, $this->_session)) {
            unset($this->_session[$name]);
        }
    }

    /**
     * Remet le tableau session à zero.
     *
     * @return void
     */
    public function clear()
    {
        $this->_session = [];
    }

    /**
     * Destruction de la session.
     *
     * @return void
     */
    public function destroy()
    {
        session_destroy();
        setcookie(session_name(), '', -3600, '/');
    }
}

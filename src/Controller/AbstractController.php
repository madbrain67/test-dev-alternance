<?php

namespace App\Controller;

use App\Http\Response;
use App\Http\Session\Session;
use App\Twig\AbstractTwig;

abstract class AbstractController extends AbstractTwig
{
    /**
     * Variable $this->_session.
     *
     * @var Session
     */
    private $_session;

    /**
     * Void __construct().
     */
    public function __construct()
    {
        $this->_session = new Session();
        parent::__construct();
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
        $this->_session->addFlash($type, $message);
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
        $this->_session->addToast($title, $time, $message);
    }

    /**
     * Redirection header.
     *
     * @param string $url comment
     */
    public function redirection(string $url): Response
    {
        $response = new Response();
        $response->redirection($url);

        return $response;
    }
}

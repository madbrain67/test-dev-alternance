<?php

namespace App\Controller;

use App\Model\HomeManager;

class HomeController extends AbstractController
{
    /**
     * Variable $this->manager.
     *
     * @var HomeManager
     */
    private $manager;

    /**
     * Void __constructeur.
     */
    public function __construct()
    {
        $this->manager = new HomeManager();
        parent::__construct();
    }

    /**
     * Method index().
     */
    public function index()
    {
        if (isset($_POST['name']) && !empty($_POST['name']) && \strip_tags($_POST['name']) !== '') {
            $argonaute = $_POST['name'];
            $this->manager->add($argonaute);
        }

        return $this->render('home/index.html.twig', [
            'equipages' => $this->manager->findAll(),
        ]);
    }
}

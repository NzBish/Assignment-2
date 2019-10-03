<?php
namespace ktc\a2\controller;

/**
 * Class HomeController
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class HomeController extends Controller
{
    /**
     * Home Index action
     */
    public function indexAction()
    {
        session_start();
        if (isset($_SESSION['userName'])) {
            $this->redirect('accountIndex');
        } else {
            $this->redirect('userLogin');
        }
    }
}

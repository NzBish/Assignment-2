<?php
namespace agilman\a2\controller;

/**
 * Class HomeController
 *
 * @package agilman/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class HomeController extends Controller
{
    /**
     * Home Index action
     */
    public function indexAction()
    {
        if (isset($_SESSION['userName'])) {
            $this->redirect('accountIndex');
        } else {
            $this->redirect('userLogin');
        }
    }
}

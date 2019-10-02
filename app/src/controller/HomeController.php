<?php
namespace ctk\a2\controller;

/**
 * Class HomeController
 *
 * @package ctk/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class HomeController extends Controller
{
    /**
     * Account Index action
     */
    public function indexAction()
    {
        $this->redirect('accountIndex');
    }
}

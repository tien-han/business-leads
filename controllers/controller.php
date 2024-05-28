<?php

    namespace controllers;

    /**
     * Controller class for the UPS Business Leads project
     * 328/business-leads/controllers/controller.php
     */
    class Controller
    {
        private $_f3; //Fat-Free Router

        function __construct($f3)
        {
            $this->_f3 = $f3;
        }

        function homepage(): void
        {
            //Render a view page
            $view = new Template();
            echo $view->render('views/homepage.html');
        }
    }
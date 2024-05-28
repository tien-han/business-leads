<?php

    /**
     * My Controller class for the Diner project
     * 328/diner/controllers/controller.php
     */
    class Controller
    {
        private $_f3; //Fat-Free Router

        function __construct($f3)
        {
            $this->_f3 = $f3;
        }

        function home()
        {
            //Render a view page
            $view = new Template();
            echo $view->render('views/home-page.html');
        }

        function breakfast()
        {
            //Render a view page
            $view = new Template();
            echo $view->render('views/breakfast-menu.html');
        }

        function lunch()
        {
            //Render a view page
            $view = new Template();
            echo $view->render('views/lunch-menu.html');
        }

        function dinner()
        {
            //Render a view page
            $view = new Template();
            echo $view->render('views/dinner-menu.html');
        }

        function order1()
        {
            //Initialize variables
            $food = '';
            $meal = '';

            //If the form has been posted
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if (Validate::validFood($_POST['food'])) {
                    $food = $_POST['food'];
                } else {
                    $this->_f3->set('errors["food"]', 'Please enter a food');
                }

                //Get the data from the post array
                if (isset($_POST['meal']) and Validate::validMeal($_POST['meal'])) {
                    $meal = $_POST['meal'];
                } else {
                    $this->_f3->set('errors["meal"]', 'Please select a meal');
                }

                //If the data is valid
                // if (!empty($food) && !empty($meal)) {
                //Add the data to the session array
                $order = new Order($food, $meal);
                $this->_f3->set('SESSION.order', $order);

                if(empty($this->_f3->get('errors'))) {
                    //Send the user to the next form
                    $this->_f3->reroute('order2');
                }
            }

            //Get the data from the model
            //and add it to the F3 hive so that it's visible in page
            $meals = DataLayer::getMeals();
            $this->_f3->set('meals', $meals);

            //Render a view page
            $view = new Template();
            echo $view->render('views/order1.html');
        }



    }
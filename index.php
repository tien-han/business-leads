<?php
/**
 * 328/business-leads/index.php
 * Simple MVC using the Fat-Free framework for our group project.
 * @author Garrett Ballreich, Sage Markwardt, Tien Han
 * @date 5/3/2024
 * @version 1.0
 */

    //Turn on error reporting
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    //Require the autoload file
    require_once('vendor/autoload.php');

    //Instantiate the F3 Base class (Fat-Free)
    $f3 = Base::instance();

    //Define a default route
    $f3-> route('GET /', function() {
        //Render a view page
        $view = new Template();
        echo $view->render('views/homepage.html');
    });

    //Route to our contact us page
    $f3-> route('GET|POST /contact', function($f3) {
        var_dump($_POST);

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $firstName = $_POST['firstName'];
            $f3->set('SESSION.firstName', $firstName);
            $lastName = $_POST['lastName'];
            $f3->set('SESSION.lastName', $lastName);
            $email = $_POST['email'];
            $f3->set('SESSION.email', $email);
            $concern= $_POST['concern'];
            $f3->set('SESSION.concern', $concern);
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/contact.html');
    });

    //Run Fat-Free
    $f3->run();
?>
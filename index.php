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

    //Require the form validation file
    require_once('model/validate.php');

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

    //Define the FAQ route
    $f3-> route('GET /faq', function() {
        //Render a view page
        $view = new Template();
        echo $view->render('views/faq.html');
    });

    //Define the Login route
    $f3-> route('GET|POST /login', function($f3) {
        //If the user has submitted a post request (filled out the login form)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Get submitted form data
            $email = $_POST['email'];
            $password = $_POST['password'];

            $allValid = true;

            //Perform validation on the submitted username (email)
            $emailError = '';
            if (!empty($email)) {
                if (!validEmail($email)) {
                    $allValid = false;
                    $emailError = 'Please enter in a valid UPS email!';
                }
            } else {
                //Email is required
                $allValid = false;
                $emailError = 'Please enter an email.';
            }
            $f3->set('SESSION.email', $email);
            $f3->set('SESSION.emailError', $emailError);

            //TODO: Add in password validation

            //TODO: Redirect once we have the page
            //Redirect to the application dashboard
            //if ($allValid) {
            //    $f3->reroute("application-form/experience");
            //}
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/login.html');
    });

    //Define the MAIN FORM route
    $f3-> route('GET|POST /form', function() {
        //var_dump($_POST);
        //Render a view page
        $view = new Template();
        echo $view->render('views/form.html');
    });

    //Run Fat-Free
    $f3->run();
?>
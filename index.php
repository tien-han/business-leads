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
                if (!validateEmail($email)) {
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

            //Perform validation on the submitted password
            $passwordError = '';
            if (!empty($password)) {
                if (!validatePassword($password)) {
                    $allValid = false;
                    $passwordError = 'Please enter in a valid password!';
                }
            } else {
                //Password is required
                $allValid = false;
                $passwordError = 'Please enter your password.';
            }
            $f3->set('SESSION.passwordError', $passwordError);

            //Redirect to the application dashboard
            if ($allValid) {
                $f3->reroute("dashboard");
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/login.html');
    });

    // define the signup route
    $f3-> route('GET|POST /sign-up', function() {
        //var_dump($_POST);
        // TODO: add in validations
        //Render a view page
        $view = new Template();
        echo $view->render('views/sign-up.html');
    });

    //Define the MAIN FORM route
    $f3-> route('GET|POST /form', function($f3) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $businessName = $_POST['businessName'];
            $businessAddress = $_POST['businessAddress'];
            $contactName = $_POST['firstName'];
            $contactLastName = $_POST['lastName'];
            $businessPhone = $_POST['phoneNum'];
            $contactEmail = $_POST['email'];
            $driverName = $_POST['driverName'];
            $driverID = $_POST['driverID'];
            $slic = $_POST['slic'];

            //validate name
            //all letter and not null
            if(validName($businessName)){
                $f3->set('SESSION.businessName',$businessName);
            }else{
                $f3->set('errors["businessNameError"]',"please enter a valid name");
            }
            //validate address

            //validate contact name
            if(validName($contactName)){
                $f3->set('SESSION.contactName',$contactName);
            }else{
                $f3->set('errors["contactNameError"]',"please enter a valid contact name");
            }

            //validate contact lastname
            if(validName($contactLastName)){
                $f3->set('SESSION.contactLastName',$contactLastName);
            }else{
                $f3->set('errors["contactLastNameError"]',"please enter a valid contact last name");
            }

            //validate phone number
            if(validPhone($businessPhone)){
                $f3->set('SESSION.businessPhone',$businessPhone);
            }else{
                $f3->set('errors["businessPhoneError"]',"please enter a valid phone number");
            }

            //validate email
            if(validEmail($contactEmail)){
                $f3->set('SESSION.contactEmail',$contactEmail);
            }else{
                $f3->set('errors["contactEmailError"]',"please enter a valid email address");
            }

            //validate driver name
            if(validName($driverName)){
                $f3->set('SESSION.driverName',$driverName);
            }else{
                $f3->set('errors["driverNameError"]',"please enter a valid contact last name");
            }
            //---------------MORE VALIDATION GOES HERE-------------------------------//



            //check errors[]
            if (empty($f3->get('errors'))) {
                $f3->reroute('formSummary');
            }
        }


        //route to summary page if complete and valid

        //Render a view page
        $view = new Template();
        echo $view->render('views/form.html');
    });

//Define the form summary route
$f3-> route('GET /formSummary', function() {
    //Render a view page
    $view = new Template();
    echo $view->render('views/formSummary.html');
});

//Define the dashboard route
$f3-> route('GET /dashboard', function() {
    //Render a view page
    $view = new Template();
    echo $view->render('views/dashboard.html');
});

    //Run Fat-Free
    $f3->run();
?>
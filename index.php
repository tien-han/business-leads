<?php
/**
 * 328/business-leads/index.php
 * Simple MVC using the Fat-Free framework for our group project.
 * @author Garrett Ballreich, Sage Markwardt, Tien Han <tienthuyhan@gmail.com>
 * @date 5/29/2024
 * @version 1.0
 */

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Require the autoload file
require_once('vendor/autoload.php');

//Require the form validation file
require_once('model/validate.php');
//need to update composer with /classes
require_once('classes/lead.php');
require_once('classes/user.php');

$obj="";

//Instantiate the F3 Base class (Fat-Free)
$f3 = Base::instance();
$controller = new Controller($f3);

/**
 * Define a default route (homepage)
 */
$f3-> route('GET /', function() {
    $GLOBALS['controller']->homepage();
});

/**
 * Route to the "Contact Us" page
 */
$f3-> route('GET|POST /contact', function() {
    $GLOBALS['controller']->contactUs();
});

/**
 * Route to the "FAQ" page
 */
$f3-> route('GET /faq', function() {
    $GLOBALS['controller']->faq();
});

/**
 * Route to the "Login" page
 */
$f3-> route('GET|POST /login', function() {
    $GLOBALS['controller']->login();
});

/**
 * Route to the reset password request email page
 */
$f3->route('GET|POST /password-request', function() {
    $GLOBALS['controller']->passwordReset();
});

/**
 * Route to the emailed password reset page
 */
$f3->route('GET|POST /password-email', function() {
    $GLOBALS['controller']->passwordEmail();
});

/**
 * Route to the signup page
 */
$f3-> route('GET|POST /sign-up', function() {
    $GLOBALS['controller']->signUp();
});

/**
 * Route to the approval page
 */
$f3-> route('GET|POST /approval', function() {
    $GLOBALS['controller']->approval();
});

/**
 * Route to the main form page
 */
$f3-> route('GET|POST /form', function() {
    $GLOBALS['controller']->mainForm();
});

//Define the form summary route
$f3-> route('GET /form-summary', function() {
    $GLOBALS['controller']->formSummary();
});

//Define the dashboard route
$f3-> route('GET /dashboard', function() {
    $GLOBALS['controller']->dashboard();
});

//Run Fat-Free
$f3->run();
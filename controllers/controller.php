<?php

/**
 * The Controller class defines methods that can be called for routing
 * within the UPS Business Leads project.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   5/29/2024
 */
class Controller
{
    //Instance variables
    private $_f3; //Fat-Free Router

    /**
     * Constructor creates a controller object, which instantiates Fat-Free
     *
     * @param Base $f3 Fat-Free
     */
    function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    /**
     * The method for rendering the homepage
     *
     * @return void
     */
    function homepage(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/homepage.html');
    }

    /**
     * The method for rendering the Contact Us page
     *
     * @return void
     */
    function contactUs(): void
    {
        //If the user has submitted a post request (filled out the contact us form)
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //Get all form responses
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $concern = $_POST['concern'];

            $allValid = true;
            $this->_f3->set('SESSION.contactSubmitted', false);

            //Perform validation on the submitted first name
            $firstNameError = '';
            if (!empty($firstName)) {
                if (!validateName($firstName)) {
                    $allValid = false;
                    $firstNameError = 'Please enter in an alphabetic first name!';
                }
            } else {
                //First name is required
                $allValid = false;
                $firstNameError = 'Please enter your first name.';
            }
            $this->_f3->set('SESSION.firstName', $firstName);
            $this->_f3->set('SESSION.firstNameError', $firstNameError);

            //Perform validation on the submitted last name
            $lastNameError = '';
            if (!empty($lastName)) {
                if (!validateName($lastName)) {
                    $allValid = false;
                    $lastNameError = 'Please enter in an alphabetic last name!';
                }
            } else {
                //Last name is required
                $allValid = false;
                $lastNameError = 'Please enter your last name.';
            }
            $this->_f3->set('SESSION.lastName', $lastName);
            $this->_f3->set('SESSION.lastNameError', $lastNameError);

            //Perform validation on the submitted email
            $emailError = '';
            if (!empty($email)) {
                if (!validateEmail($email)) {
                    $allValid = false;
                    $emailError = 'Please enter in a valid email!';
                }
            } else {
                //Email is required
                $allValid = false;
                $emailError = 'Please enter an email.';
            }
            $this->_f3->set('SESSION.email', $email);
            $this->_f3->set('SESSION.emailError', $emailError);

            //Ensure that there is text in the concern text box
            $concernError = '';
            if (empty($concern)) {
                //Concern is required
                $allValid = false;
                $concernError = 'Please enter your concern.';
            }
            $this->_f3->set('SESSION.concern', $concern);
            $this->_f3->set('SESSION.concernError', $concernError);

            //If everything is valid, let the user know that their response has been sent
            if ($allValid) {
                $this->_f3->set('SESSION.contactSubmitted', true);
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/contact.html');
        session_destroy();
    }

    function passwordReset() : void {
        $view = new Template();
        echo $view->render('views/password-reset.html');
    }
}
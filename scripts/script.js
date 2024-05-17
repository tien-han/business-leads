/**
 * Description: This file holds functional JavaScript that enables UI processes.
 * @author: Tien Han
 * @date: 5/17/2024
 * @version 1.0
 */

//Enables tooltip functionality (pop up)
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

//Powers "Show Password" checkboxes (for example, on the Login Page)
function showPassword() {
    var password = document.getElementById("password");
    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }
}



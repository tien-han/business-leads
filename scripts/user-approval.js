/**
 * Description: This file holds functional JavaScript that approves users.
 * @author: Garrett Ballreich
 * @date: 6/11/2024
 * @version 1.0
 */

$(".approve_button").click (function(){
    //alert($(this).attr("id"));
    var id = ($(this).attr("id"));
    //addUser(id);
    $.ajax({
        url: 'https://garrettballreich.greenriverdev.com/328/business-leads/approveRequest',
        type: 'POST',
        data: {id: id},
        success: function(response) {
            alert("Request approved: " + id);

        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("An error occurred: " + textStatus);
        }
    });
});

$(".delete_button").click (function(){

    var id = ($(this).attr("data-id"));

    $.ajax({
        url: 'https://garrettballreich.greenriverdev.com/328/business-leads/deleteRequest',
        type: 'POST',
        data: {id: id},
        success: function(response) {
            alert("Request approved: " + id);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("An error occurred: " + textStatus);
        }
    });
});

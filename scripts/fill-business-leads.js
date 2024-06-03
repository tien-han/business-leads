/*
    This page holds JavaScript code that pulls all business leads and populates the
    business leads table.

    Author: Tien Han
    File: fill-business-leads.js
    Date: 6/2/2024
*/

//Make the business leads table into a datatable & set parameters
$(document).ready(function () {
    $("#bl-table").DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 75, 100, { label: "All", value: - 1 }],
        paging: true,
        scrollY: "560px",
        searching: true,
        columns: [
            null,
            null,
            null,
            null,
            {searchable: false},
            {searchable: false}
        ],
        "language": {
            "search": "Search: ",
            "infoEmpty": "No matching records found"
        },
        columnDefs: [
            // Center align header content of columns 1, 2, 3, 4
            { className: "dt-head-center", targets: [0, 1, 2, 3] },
            // Center align body content of columns 1, 2, 3, 4
            { className: "dt-body-center", targets: [0, 1, 2, 3] },
        ],
        autoWidth: false,
    });

    getBusinessLeads();
});

$(window).on('resize', function () {
    $("#bl-table").DataTable().columns.adjust().draw();
});

async function getBusinessLeads() {
    await fetch("/328/business-leads/model/get-all-business-leads.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Something went wrong while trying to get all business leads.");
            }

            return response.json();
        })
        .then(data => {
            updateBusinessLeadsTable(data);
        })
        .catch((error) => {
            console.error('Error loading business leads:', error);
        });
}

//This method updates the business leads table with all the leads
function updateBusinessLeadsTable(leads) {
    let leadsTable = $("#bl-table").DataTable();

    leads.forEach(lead => {
        const leadDate = new Date(lead.created_at).toISOString().split('T')[0];

        //Create and add a row for the business lead
        const rowData = leadsTable.row.add([
            leadDate,
            lead.name,
            lead.address,
            lead.contact_phone,
            `
                <td>
                    <input type="hidden" name="leadId" value="${lead.id}">
                    <button type="submit" class="btn btn-primary" onclick="sendLead()">Update</button>
                </td>
            `,
            `
                <td>
                    <input type="hidden" name="leadId" value="${lead.id}">
                    <button type="submit" class="btn btn-danger" onclick="deleteLead()">Delete</button>
                </td>
            `,
        ]).draw(false).node();
    })

    //Create filtering for the business leads table
    $("#bl-table-header tr:eq(0) td").not(":eq(4),:eq(5)").each(function (i) {
        //Create selects for each column (besides the View and Delete columns)
        //And enable search/filter based on what's selected
        const select = $('<select><option value=""></option></select>')
            .appendTo($(this).empty())
            .on('change', function () {
                var term = $(this).val();
                leadsTable.column(i).search(term, false, false).draw();
            });

        //Apply values from the table into the select button
        leadsTable.column(i).data().unique().sort().each(function (d, j) {
            select.append('<option value="' + d + '">' + d + '</option>')
        });

        //Stop select triggering sort in the filter row (row above header)
        $("#bl-table-header tr:eq(0) td").click(function (e) {
            e.stopPropagation();
        });
    });

    leadsTable.columns.adjust().draw();
}
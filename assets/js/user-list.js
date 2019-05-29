require("bootstrap-table");
require("bootstrap-table/dist/locale/bootstrap-table-fr-BE.min.js");

let userList = $('#user-list');
let availabilityBtnFilter = document.getElementById('availability-btn-filter');

userList.bootstrapTable({
    url: 'http://127.0.0.1:8080/events/1/manage/resources.json',
    classes: 'table-hover',
    theadClasses: 'thead-light',
    pagination: true,
    search: true,
    uniqueId: 'id',
    toolbar: "#toolbar",
    toolbarAlign: "right",
    detailView: true,
    detailFormatter: function(index, row, element) {
        let formattedHtml = "";
        if (row.contacts === undefined) {
            $.ajax("http://127.0.0.1:8080/events/1/manage/resources/1/contact.json", {
                async: false,
                complete: function (data) {
                    row.contacts = data.responseJSON;
                }
            });
        }
        row.contacts.forEach(function (contact) {
            formattedHtml += '<a href="tel:'+contact.value+'" class="badge badge-primary" title="'+contact.name+'"><i class="fa fa-phone mr-2"></i>'+contact.value+'</a>';
        });
        return formattedHtml;
    },
    columns: [{
        checkbox: true
        },{
            title: '<i class="fa fa-user-check text-muted"></i>',
            align: 'center',
            width: 15,
            sortable: true,
            titleTooltip: "Disponibilité de la ressource",
            formatter: function(val, row, i, field) {
                if (row.pending) {
                    row.availabilityValue = 3;
                    return '<i class="fa fa-circle text-secondary" title="En attente"></i>';
                } else if (row.fullyAvailable) {
                    row.availabilityValue = 1;
                    return '<i class="fa fa-circle text-success" title="Disponible"></i>';
                } else if (row.available) {
                    row.availabilityValue = 2;
                    return '<i class="fa fa-circle text-medium-success" title="Partiellement disponible"></i>';
                } else {
                    row.availabilityValue = 4;
                    return '<i class="fa fa-circle text-danger" title="Non disponible"></i>';
                }
            },
            sorter: function(a, b, rowA, rowB) {
                return rowA.availabilityValue - rowB.availabilityValue;
            }
        }, {
            clickToSelect: true,
            field: 'user.name',
            title: 'Nom',
            sortable: true,
            searchable: true,
            formatter: function (val, row, i, field) {
                return '<p class="mb-0"><strong>'+val+'</strong></p>\n' +
                    '<a href="mailto:'+row.user.email+'" class="text-muted"><small>'+row.user.email+'</small></a>';
            }
        }, {
            field: '',
            title: "Groupe d'équité",
        },{
            title: 'Actions',
        }
    ],
});

/**
 * HANDLE REMOVE USER
 */
let btnActions = $("#actions-btn");
let disableActionsBtn = function() {
    btnActions.attr('disabled', true);
};

let enableActionsBtn = function() {
    btnActions.removeAttr('disabled');
};

userList.on('uncheck-all.bs.table', disableActionsBtn);
userList.on('check-all.bs.table check.bs.table', enableActionsBtn);

userList.on('uncheck.bs.table', function () {
    let hasSelection = userList.bootstrapTable('getSelections').length !== 0;
    if (!hasSelection) {
        disableActionsBtn();
    }
});



let availabilityCheckboxes = availabilityBtnFilter.querySelectorAll('input[type="checkbox"]');
availabilityCheckboxes.forEach(function (availabilityCheckbox) {
    availabilityCheckbox.addEventListener('click', function() {
        let selectedAvailabilities = availabilityBtnFilter.querySelectorAll('input[type="checkbox"]:checked');
        let values = [].map.call(selectedAvailabilities, el => el.value*1);
        userList.bootstrapTable('filterBy', {
            'availabilityValue': values
        });
    });
});

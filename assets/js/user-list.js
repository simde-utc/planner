require("bootstrap-table");
require("bootstrap-table/dist/locale/bootstrap-table-fr-BE.min.js");

let userList = $('#user-list');
let availabilityBtnFilter = document.getElementById('availability-btn-filter');
let userIconSrc = userList.data('user-icon-url');
let userListUrl = userList.data('user-list-url');
let contactUrl = userList.data('contact-url'); //TODO: implement that

userList.bootstrapTable({
    url: userListUrl,
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
            //TODO: find a way to do it async
            $.ajax("http://127.0.0.1:8080/events/1/manage/resources/"+row.user.id+"/contact.json", {
                async: false,
                complete: function (data) {
                    row.contacts = data.responseJSON;
                }
            });
        }
        row.contacts.forEach(function (contact) {
            let icon = contact.type.type === 'phone' ? 'phone' : 'envelope';
            let href = contact.type.type === 'phone' ? 'tel' : 'mailto';
            formattedHtml += '<a href="'+href+':'+contact.value+'" class="badge badge-primary" title="'+contact.name+'"><i class="fa fa-'+icon+' mr-2"></i>'+contact.value+'</a>';
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
                    return '<i data-toggle="tooltip" class="fa fa-circle text-secondary" title="En attente"></i>';
                } else if (row.fullyAvailable) {
                    row.availabilityValue = 1;
                    return '<i data-toggle="tooltip" class="fa fa-circle text-success" title="Disponible"></i>';
                } else if (row.available) {
                    row.availabilityValue = 2;
                    return '<i data-toggle="tooltip" class="fa fa-circle text-medium-success" title="Partiellement disponible"></i>';
                } else {
                    row.availabilityValue = 4;
                    return '<i data-toggle="tooltip" class="fa fa-circle text-danger" title="Non disponible"></i>';
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
                let imgSrc = row.user.image === null ? userIconSrc : row.user.image;
                return '<div class="user-list-picture"><img src="'+imgSrc+'" alt=""></div><div class="user-list-name"><p class="mb-0"><strong>'+val+'</strong></p>\n' +
                    '<a href="mailto:'+row.user.email+'" class="text-muted"><small>'+row.user.email+'</small></a></div>';
            }
        }, {
            sortable: true,
            field: 'equityGroup.name',
            title: "Groupe d'équité",
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

$("#remove_modal, #add_group_modal").on('show.bs.modal', function (e) {
    let usersToRemove = userList.bootstrapTable('getSelections');
    let idsToRemove = [].map.call(usersToRemove, user => user.id);
    $(this).find(".nb-users").html(usersToRemove.length);
    $(this).find(".users-to-remove select option").each(function () {
        if (idsToRemove.includes(this.value*1)) {
            $(this).attr('selected', true);
            $(this).removeClass('d-none');
        } else {
            $(this).addClass('d-none');
            $(this).removeAttr('selected');
        }
    });
});

/**
 * HANDLE USER INVITATIONS
 */
let invitationSelect = $("#invitation_form_users");

var newOption = new Option('hey', 'id', false, false);
invitationSelect.append(newOption).trigger('change');

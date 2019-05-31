
// Change content of modal before showing it
$("#more_modal").on('show.bs.modal', function (e) {
    let button = $(e.relatedTarget);
    let user = button.data('user');
    let message = button.data('message');
    let date = button.data('date');

    let modal = $(this);
    modal.find('.request-user').text(user);
    modal.find('.request-message').text(message);
    modal.find('.request-date').text(date);
});
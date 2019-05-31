
// Change content of modal before showing it
$("#more_modal").on('show.bs.modal', function (e) {
    let button = $(e.relatedTarget);
    let user = button.data('user');
    let message = button.data('message');
    let date = button.data('date');
    let acceptUrl = button.data('accept-url');
    let refuseUrl = button.data('refuse-url');
    let accepted = button.data('accepted');
    let acceptedBy = button.data('accepted-by');
    let acceptedAt = button.data('accepted-at');
    console.log(acceptedAt, acceptedBy);

    let modal = $(this);
    modal.find('.request-user').text(user);
    modal.find('.request-message').text(message);
    modal.find('.request-date').text(date);
    modal.find('#accept-btn').attr('href', acceptUrl);
    modal.find('#refuse-btn').attr('href', refuseUrl);

    if (accepted === null) {
        modal.find('.modal-footer .accepted').hide();
        modal.find('.modal-footer .tools').show();
    } else {
        let text = accepted ? "Accepté" : "Refusé";
        text += " le "+acceptedAt+" par "+acceptedBy;

        modal.find('.modal-footer .accepted').text(text).show();
        modal.find('.modal-footer .tools').hide();
    }
});
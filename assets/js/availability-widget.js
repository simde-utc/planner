let slots = document.querySelectorAll('.time-slot .slot');
var mouseIsDown = false;
var currentState = false;

Array.from(slots).forEach(slot => {
    slot.addEventListener('mousedown', function () {
        mouseIsDown = true;
        currentState = !this.classList.contains('bg-success');
        this.classList.toggle('bg-success');
    });
    document.addEventListener('mouseup', function () {
        mouseIsDown = false;
    });

    slot.addEventListener('mouseenter', function () {
        if (mouseIsDown) {
            if (currentState) {
                this.classList.add('bg-success');
            } else {
                this.classList.remove('bg-success');
            }
        }
    });
});
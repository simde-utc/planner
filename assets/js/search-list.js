
/**
 * Handle search on event task page
 */
let filter = function () {
    let value = this.value.toLowerCase();
    let listItems = Array.from(document.querySelectorAll(".searchable-list .list-group-item"));
    listItems.forEach(el => {
        if (el.text.toLowerCase().indexOf(value) > -1) {
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
        }
    });
};

let searchInput = document.getElementById("search_input");
if (searchInput) {
    searchInput.addEventListener("search", filter);
    searchInput.addEventListener("keyup", filter);
}
$(document).ready(function () {
    searchForm = jQuery("#searchForm");
    inputs = searchForm.find('input');

    searchForm.submit(function () {
        inputs.each(function (index, input) {
            if (input.value == "") {
                input.disabled = true;
            }
        });
    })
});

$('#add-image').click(function () {
    // Gets the number of ad images, + is to parse val into integer
    const counter = +$('#widget_counter').val();
    // Gets new fields template, for each of them found, replace __name__ by the counter
    const template = $('#announce_images').data('prototype').replace(/__name__/g, counter)

    // Appends the new field in the page
    $('#announce_images').append(template)

    $('#widget_counter').val(counter + 1)

    // Handle delete buttons
    handleDeleteButtons();
})

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#announce_images div.form-group').length;

    $('#widget_counter').val(count);
}

// initialises the images counter base on the number of image form-group elements in the DOM
updateCounter();
// Handle delete buttons on page load
handleDeleteButtons();
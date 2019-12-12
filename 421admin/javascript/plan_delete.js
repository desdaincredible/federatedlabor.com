$(document).ready(function () {

    $('form').each(function (i) {
        $(this).submit(function () {
            var answer = confirm('Are you sure you want to delete plan ' + $(this).find('input[name=plan_number]').val() + '?');

            if (!answer) {
                return false;
            }

        });
    });


});

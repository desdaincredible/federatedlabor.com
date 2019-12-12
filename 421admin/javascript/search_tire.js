$(document).ready(function () {


    $('form').each(function () {
        $(this).submit(function () {
            if ($('input:checkbox').attr('checked') == true) {
                var doublecheck = confirm("If you delete the make, all models associated with that make will also be deleted. Proceed?");
                if (doublecheck) {
                    return true;
                }
                else {
                    return false;
                }
            }
        });
    });


});

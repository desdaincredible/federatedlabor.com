$(document).ready(function () {


    $("#abs_signup").submit(function () {
        $(".form_header").css("color", "#000000");
        var error = 0;
        if ($("#contact_name").val() == '') {
            error = 1;
            $("#c_name").css("color", "#ff0000");

        }
        if ($("#business_name").val() == '') {
            error = 1;
            $("#b_name").css("color", "#ff0000");

        }
        if ($("#telephone").val() == '') {
            error = 1;
            $("#tel").css("color", "#ff0000");
        }

        if ($("#telephone").val() == '') {
            error = 1;
            $("#tel").css("color", "#ff0000");
        }
        else {

            var phoneString = $("#telephone").val().replace(/[\(\)\.\-\ ]/g, '');
            if (isNaN(parseInt(phoneString))) {
                alert("The phone number contains illegal characters");
                $(this).val('');
                return false;
            }

            if (!(phoneString.length == 10)) {
                alert("Please enter the full phone number including area code");
                $(this).val('');
                return false;

            }

        }

        if ($("#address").val() == '') {
            error = 1;
            $("#adr").css("color", "#ff0000");
        }
        if ($("#city").val() == '') {
            error = 1;
            $("#cit").css("color", "#ff0000");
        }
        if ($("#state").val() == '') {
            error = 1;
            $("#sta").css("color", "#ff0000");
        }
        if ($("#zip").val() == '') {
            error = 1;
            $("#z_code").css("color", "#ff0000");
        }
        if ($("#email").val() == '') {
            error = 1;
            $("#ema").css("color", "#ff0000");
        }
        else {

            var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            if (!regex.test($("#email").val())) {
                alert('Please enter a valid email address');
                return false;
            }


        }

        if ($("#contact_message").val() == '') {
            error = 1;
            $("#c_message").css("color", "#ff0000");
        }

        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });


});

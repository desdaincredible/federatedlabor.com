$(document).ready(function () {
    $("#dealer_check").submit(function () {

        return false;
    });

    $("#dealer_edit").click(function () {

        $("#next_page").val('0');
        $("#dealer_review").hide();
        $("#dealer_complete").hide();
        $("#dealer_edit").hide();
        $("#edit_new_dealer").show();
        $("#final_check").show();
    });


    $("#dealer_complete").click(function () {

        $("#next_page").val('1');
        document.dealer_check.submit();

    });

    $("#final_check").click(function () {

        $("#next_page").val('0');
        document.dealer_check.submit();

    });


    $("#dealer_check").submit(function () {

        $(".plan_header").css("color", "#000000");
        var error = 0;


        if ($("#contact_first_name").val() == '') {

            error = 1;
            $("#con_first").css("color", "#ff0000");

        }


        if ($("#contact_last_name").val() == '') {

            error = 1;
            $("#con_last").css("color", "#ff0000");

        }

        if ($("#contact_title").val() == '') {

            error = 1;
            $("#con_title").css("color", "#ff0000");

        }


        if ($("#business_name").val() == '') {

            error = 1;
            $("#bus_name").css("color", "#ff0000");

        }

        if ($("#business_address").val() == '') {

            error = 1;
            $("#bus_address").css("color", "#ff0000");

        }


        if ($("#business_city").val() == '') {

            error = 1;
            $("#bus_city").css("color", "#ff0000");

        }


        if ($("#business_state").val() == '') {

            error = 1;
            $("#bus_state").css("color", "#ff0000");

        }

        if ($("#business_zip").val() == '') {

            error = 1;
            $("#bus_zip").css("color", "#ff0000");

        }


        if ($("#business_phone").val() == '') {

            error = 1;
            $("#bus_phone").css("color", "#ff0000");

        }


        if ($("#business_fax").val() == '') {

            error = 1;
            $("#bus_fax").css("color", "#ff0000");

        }

        if ($("#business_email").val() == '') {

            error = 1;
            $("#bus_email").css("color", "#ff0000");

        }

        if ($("#dealer_id").val() == '') {

            error = 1;
            $("#d_id").css("color", "#ff0000");

        }

        if ($("#dealer_username").val() == '') {
            error = 1;
            $("#d_username").css("color", "#ff0000");

        }

        if ($("#dealer_password").val() == '') {

            error = 1;
            $("#d_password").css("color", "#ff0000");

        }
        if ($("#dealer_site").val() == '') {

            error = 1;
            $("#d_site").css("color", "#ff0000");

        }

        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });


});

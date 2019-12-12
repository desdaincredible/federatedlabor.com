function checkFullForm() {

    $(".plan_header").css("color", "#000000");
    var error = 0;
    if ($("#customer_first_name").val() == '') {
        error = 1;
        $("#cust_first").css("color", "#ff0000");

    }
    if ($("#customer_last_name").val() == '') {
        error = 1;
        $("#cust_last").css("color", "#ff0000");
    }
    if ($("#customer_phone").val() == '') {
        error = 1;
        $("#cust_phone").css("color", "#ff0000");
    }
    if ($("#invoice_number").val() == '') {
        error = 1;
        $("#invoice_num").css("color", "#ff0000");
    }
    if ($("#vehicle_year").val() == '') {
        error = 1;
        $("#car_year").css("color", "#ff0000");
    }
    if ($("#vehicle_make").val() == '') {
        error = 1;
        $("#car_make").css("color", "#ff0000");
    }
    if ($("#vehicle_model").val() == '') {
        error = 1;
        $("#car_model").css("color", "#ff0000");
    }

    if ($("#vehicle_mileage").val() == '') {
        error = 1;
        $("#car_miles").css("color", "#ff0000");
    }

    if (error == 1) {
        alert("Please fill in the missing fields");

    }

    return error;


}


$(document).ready(function () {
    if (show_modal > 0) {
        $("#dealer_info").dialog({
            resizable: false,
            height: "auto",
            width: "auto",
            modal: true
        });
        if (show_modal == 1) {
            $("#dealer_verify").show();
        }
        else {
            $("#dealer_complete").hide();
            $("#edit_dealer").hide();
            $("#update_submit").show();
            $("#dealer_update").show();
        }


        $("#dealer_complete").click(function () {
            $("#dealer_info").dialog("close");
        });
        $("#dealer_edit").click(function () {
            $("#dealer_complete").hide();
            $("#dealer_edit").hide();
            $("#update_submit").show();

            $("#dealer_verify").hide();
            $("#dealer_update").show();
        });

        $("#update_submit").click(function () {

            $(".plan_header").css("color", "#000000");

            var error = 0;

            if ($("#contact_first_name").val() == '') {
                error = 1;
                $("#con_first").css("color", "#ff0000");

            }
            else {
                $("#con_first").css("color", "#000000");
            }
            if ($("#contact_last_name").val() == '') {
                error = 1;
                $("#con_last").css("color", "#ff0000");
            }
            else {
                $("#con_last").css("color", "#000000");
            }

            if ($("#contact_title").val() == '') {
                error = 1;
                $("#con_title").css("color", "#ff0000");
            }
            else {
                $("#con_title").css("color", "#000000");
            }

            if ($("#business_name").val() == '') {
                error = 1;
                $("#bus_name").css("color", "#ff0000");
            }
            else {
                $("#bus_name").css("color", "#000000");
            }

            if ($("#business_address").val() == '') {
                error = 1;
                $("#bus_address").css("color", "#ff0000");
            }
            else {
                $("#bus_address").css("color", "#000000");
            }

            if ($("#business_city").val() == '') {
                error = 1;
                $("#bus_city").css("color", "#ff0000");
            }
            else {
                $("#bus_city").css("color", "#000000");
            }


            if ($("#business_state").val() == '') {
                error = 1;
                $("#bus_state").css("color", "#ff0000");
            }
            else {
                $("#bus_state").css("color", "#000000");
            }

            if ($("#business_zip").val() == '') {
                error = 1;
                $("#bus_zip").css("color", "#ff0000");
            }
            else {
                $("#bus_zip").css("color", "#000000");
            }

            if ($("#business_phone").val() == '') {
                error = 1;
                $("#bus_phone").css("color", "#ff0000");
            }
            else {
                $("#bus_phone").css("color", "#000000");
            }

            if ($("#business_email").val() == '') {
                error = 1;
                $("#bus_email").css("color", "#ff0000");
            }
            else {
                $("#bus_email").css("color", "#000000");
            }

            if (error == 1) {
                alert("Please fill in the missing fields");
                return false;

            }
            else {
                /* Update info */
                $.ajax({
                    type: "POST",
                    url: "/php/ajax_dealer_self_update.php",
                    data: {
                        dealer_id: $("#dealer_id").val(),
                        contact_first_name: $("#contact_first_name").val(),
                        contact_last_name: $("#contact_last_name").val(),
                        contact_title: $("#contact_title").val(),
                        business_name: $("#business_name").val(),
                        business_address: $("#business_address").val(),
                        business_city: $("#business_city").val(),
                        business_state: $("#business_state").val(),
                        business_zip: $("#business_zip").val(),
                        business_phone: $("#business_phone").val(),
                        business_email: $("#business_email").val()
                    }
                }).done(function (resp) {
                    $("#dealer_complete").attr('value', 'Close Window');
                    $("#dealer_complete").show();
                    $("#dealer_edit").hide();
                    $("#edit_dealer").hide();
                    $("#update_submit").hide();
                    $("#dealer_update").html('<h4>' + resp + '</h4>');
                });


            }


        });


    }

    $('.formcheck').keypress(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {

            if ($(this).attr('tabindex')) {

                if ($(".formcheck[rel='last']").attr('tabindex') != $(this).attr('tabindex')) {

                    var newtab = parseInt($(this).attr('tabindex')) + 1;

                    $(".formcheck[tabindex='" + newtab + "']").focus();

                }

            }

            return false;


        }

    });

    $("#vehicle_year").blur(function () {
        if ($("#vehicle_year").val().length < 4) {
            $("#vehicle_year").val('');
            alert('Please use the four-digit year');
        }
    });

    $("#vehicle_mileage").blur(function () {

        if (/\D/g.test($("#vehicle_mileage").val())) {
            $("#vehicle_mileage").val('');
            alert('Please enter the mileage without commas or periods');
        }


    });
    $(function () {
        $("#invoice_date").datepicker();
    });

    $(function () {
        $("#vehicle_model").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("show_models", {
                    "vmid": $("#vehicle_make").val(),
                    "term": $("#vehicle_model").val()
                }, function (data) {
                    response(data);
                });

            },
            select: function (event, ui) {

                if (ui.item.value == 'No Suggestions Found') {
                    ui.item.value = '';
                }
                $(this).val(ui.item.value);
            }


        });

    });


    $("#new_plan").submit(function () {
        if (checkFullForm() == 1) {
            return false;

        }
    });

});

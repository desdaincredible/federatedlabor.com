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

    $("#comm_ag_use_yes").click(function () {
        alert("Commercial or Agricultural vehicles are excluded from the road hazard warranty. You cannot register these types of vehicles.");
        $("#comm_ag_use_no").attr('checked', true);
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
        $("#vehicle_model").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("php/show_models.php", {
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

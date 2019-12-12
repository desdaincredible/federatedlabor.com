$(document).ready(function () {

    $("#comm_ag_use_yes").click(function () {
        alert("Commercial or Agricultural vehicles are excluded from the road hazard warranty. You cannot register these types of vehicles.");
        $("#comm_ag_use_no").attr('checked', true);
    });

    $("#customer_phone").blur(function () {

        var phoneString = $(this).val();

        if (phoneString != '') {
            phoneString = phoneString.replace(/[\(\)\.\-\ ]/g, '');
            if (isNaN(parseInt(phoneString))) {
                alert("The phone number contains illegal characters");
                $(this).val('');
                return;
            }

            if (!(phoneString.length == 10)) {
                alert("Please enter the full phone number including area code");
                $(this).val('');
                return;

            }

            var areaCode = phoneString.substring(0, 3);
            var locality = phoneString.substring(3, 6);
            var digits = phoneString.substring(6, 10);

            $(this).val('(' + areaCode + ') ' + locality + '-' + digits);

            areaCode = null;
            locality = null;
            digits = null;

        }


    });
    $(function () {
        $("#vehicle_make").autocomplete({
            minLength: 2,
            source: "php/show_makes.php",
            select: function (event, ui) {
                if (ui.item.value == 'No Suggestions Found') {
                    ui.item.value = '';
                }
                $(this).val(ui.item.value);
                $("#vehicle_make_id").text(ui.item.make_id);
                vmid = ui.item.make_id;
            }


        });

        $("#vehicle_model").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("php/show_models.php", {
                    "vmid": $("#vehicle_make_id").text(),
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
            return false;

        }
    });


});

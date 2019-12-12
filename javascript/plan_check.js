function addTire(cloneId) {
    var currentId = cloneId.replace('add_', '');
    var nextId = parseInt(currentId) + 1;
    if (currentId == 5) {

        $("#newTire").hide();
        $("#divider").hide();
        $("#remove_tire").attr("name", "remove_" + nextId);

    }
    else {
        $("#minusTire").show();
        $("#divider").show();
        $("#remove_tire").attr("name", "remove_" + nextId);
        $("#add_tire").attr("name", "add_" + nextId);


    }

    $("#tire_" + nextId).show();
    $("#tire_dot_" + nextId).val('');
    $("#tire_make_" + nextId).val($("#tire_make_" + currentId).val());
    $("#tire_model_" + nextId).val($("#tire_model_" + currentId).val());
    $("#tire_size_" + nextId).val($("#tire_size_" + currentId).val());
    $("#tire_price_" + nextId).val($("#tire_price_" + currentId).val());


    $("#tire_make_" + nextId).autocomplete({
        minLength: 2,
        source: "php/show_tire_makes.php",
        select: function (event, ui) {
            if (ui.item.value == 'No Suggestions Found') {
                ui.item.value = '';
            }
            $(this).val(ui.item.value);
            $("#vehicle_make_id").text(ui.item.make_id);
            vmid = ui.item.make_id;
        }


    });

    $("#tire_model_" + nextId).autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
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


    currentId = null;
    nextId = null;
    currentDiv = null;
    newDiv = $(currentDiv).html();
    stringToReplace = null;
    replacement = null;


}

function removeTire(cloneId) {

    var currentId = cloneId.replace('remove_', '');
    var prevId = parseInt(currentId) - 1;

    if (currentId == 2) {

        $("#minusTire").hide();
        $("#divider").hide();
        $("#add_tire").attr("name", "add_" + prevId);


    }
    else {
        $("#newTire").show();
        $("#divider").show();
        $("#remove_tire").attr("name", "remove_" + prevId);
        $("#add_tire").attr("name", "add_" + prevId);


    }


    $("#tire_make_" + currentId).autocomplete("destroy");
    $("#tire_model_" + currentId).autocomplete("destroy");
    $("#tire_dot_" + currentId).val('');
    $("#tire_make_" + currentId).val('');
    $("#tire_model_" + currentId).val('');
    $("#tire_size_" + currentId).val('');
    $("#tire_price_" + currentId).val('');
    $("#tire_price_" + currentId).unbind();
    $("#tire_" + currentId).hide();

}

$(document).ready(function () {
    $("#plan_check").submit(function () {

        return false;
    });

    $("#plan_edit").click(function () {

        $("#next_page").val('0');
        $("#plan_review").hide();
        $("#plan_complete").hide();
        $("#plan_edit").hide();
        $("#edit_plan").show();
        $("#final_check").show();
    });


    $("#plan_complete").click(function () {

        $("#next_page").val('1');
        document.plan_check.submit();

    });

    $("#final_check").click(function () {

        $("#next_page").val('0');
        document.plan_check.submit();

    });


    $("#add_tire").click(function () {
        addTire($(this).attr('name'));
        return false;


    });

    $("#remove_tire").click(function () {
        removeTire($(this).attr('name'));
        return false;


    });

    $(function () {
        $("#tire_make_1, #tire_make_2, #tire_make_3, #tire_make_4,#tire_make_5, #tire_make_6").autocomplete({
            minLength: 2,
            source: "php/show_tire_makes.php",
            select: function (event, ui) {
                if (ui.item.value == 'No Suggestions Found') {
                    ui.item.value = '';
                }
                $(this).val(ui.item.value);
                $("#vehicle_make_id").text(ui.item.make_id);
                vmid = ui.item.make_id;
            }


        });

        $("#tire_model_1, #tire_model_2, #tire_model_3, #tire_model_4,#tire_model_5, #tire_model_6").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("php/show_tire_models.php", {
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

    $("#comm_ag_use_yes").click(function () {
        alert("Commercial or Agricultural vehicles are excluded from the road hazard warranty. You cannot register these types of vehicles.");
        $("#comm_ag_use_no").attr('checked', true);
    });


    $("#vehicle_year").blur(function () {
        if ($("#vehicle_year").val().length < 4) {
            alert('Please use the four-digit year');
        }
    });
    $("#vehicle_mileage").blur(function () {


        if (/\D/g.test($("#vehicle_mileage").val())) {
            $("#vehicle_mileage").val('');
            alert('Please enter the mileage without commas or periods');


        }


    });

    $("#tire_price_1, #tire_price_2, #tire_price_3, #tire_price_4,#tire_price_5, #tire_price_6").blur(function () {
        if (parseFloat($(this).val()) < 40.00) {
            $(this).val('');
            alert('Tire Price must be at least $40.00');


        }
        else {
            var price = formatAsMoney($(this).val());
            $(this).val(price);
        }
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

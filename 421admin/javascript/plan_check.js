function addTire(cloneId) {

    var currentId = cloneId.replace('add_', '');
    var nextId = parseInt(currentId) + 1
    var currentDiv = '#tire_' + currentId;

    var newDiv = '<div id="tire_' + currentId + '">' + $(currentDiv).html() + '</div>';

    /*update code for new id */

    var stringToReplace = 'Tire ' + currentId;
    var replacement = 'Tire ' + nextId;

    newDiv = newDiv.replace(stringToReplace, replacement);
    stringToReplace = new RegExp('_' + currentId, 'gi');

    replacement = '_' + nextId;

    newDiv = newDiv.replace(stringToReplace, replacement);

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

    $(currentDiv).after(newDiv);

    $("#tire_make_" + nextId).autocomplete({
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

    $("#tire_model_" + nextId).autocomplete({
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
    var currentDiv = '#tire_' + currentId;
    $(currentDiv).remove();

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

        return false;


    });

    $("#remove_tire").click(function () {
        removeTire($(this).attr('name'));
        return false;


    });

    $(function () {
        $("#tire_make_1").autocomplete({
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

        $("#tire_model_1").autocomplete({
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

var nextId = 0;

function addTire(cloneId) {

    var currentId = cloneId.replace('add_', '');
    nextId = parseInt(currentId) + 1;
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
    $("#tire_make_" + nextId).val($("#tire_make_" + currentId).val());
    $("#tire_model_" + nextId).val($("#tire_model_" + currentId).val());
    $("#tire_size_" + nextId).val($("#tire_size_" + currentId).val());
    $("#tire_price_" + nextId).val($("#tire_price_" + currentId).val());
    $("#tire_price_" + nextId).blur(function () {
        var price = formatAsMoney($(this).val());
        $(this).val(price);

    });
    $("#tire_make_" + nextId).autocomplete({
        minLength: 2,
        source: "php/show_tire_makes.php",
        select: function (event, ui) {
            if (ui.item.value == 'No Suggestions Found') {
                ui.item.value = '';
            }
            $(this).val(ui.item.value);
            $("#tire_make_id").text(ui.item.make_id);
            vmid = ui.item.make_id;
        }


    });

    $("#tire_model_" + nextId).autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
                "vmid": $("#tire_make_id").text(),
                "term": $("#tire_model_" + nextId).val()
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


    $("#add_tire").click(function () {
        addTire($(this).attr('name'));
        return false;


    });

    $("#remove_tire").click(function () {
        removeTire($(this).attr('name'));
        return false;


    });
    $("#tire_price_1").blur(function () {
        var price = formatAsMoney($(this).val());
        $(this).val(price);


    });
    $(function () {
        $("#tire_make_1").autocomplete({
            minLength: 2,
            source: "php/show_tire_makes.php",
            select: function (event, ui) {
                if (ui.item.value == 'No Suggestions Found') {
                    ui.item.value = '';
                }
                $(this).val(ui.item.value);
                $("#tire_make_id").text(ui.item.make_id);
                vmid = ui.item.make_id;
            }


        });

        $("#tire_model_1").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("php/show_tire_models.php", {
                    "vmid": $("#tire_make_id").text(),
                    "term": $("#tire_model_1").val()
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


    $("#new_plan_2").submit(function () {

        $(".plan_header").css("color", "#000000");
        var error = 0;

        if ($("#tire_make_1").length != 0) {
            if ($("#tire_make_1").val() == '') {

                error = 1;
                $("#t_make_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_make_2").length != 0) {
            if ($("#tire_make_2").val() == '') {

                error = 1;
                $("#t_make_2").css("color", "#ff0000");

            }
        }
        if ($("#tire_make_3").length != 0) {
            if ($("#tire_make_3").val() == '') {

                error = 1;
                $("#t_make_3").css("color", "#ff0000");

            }
        }

        if ($("#tire_make_4").length != 0) {
            if ($("#tire_make_4").val() == '') {

                error = 1;
                $("#t_make_4").css("color", "#ff0000");

            }
        }

        if ($("#tire_make_5").length != 0) {
            if ($("#tire_make_5").val() == '') {

                error = 1;
                $("#t_make_5").css("color", "#ff0000");

            }
        }

        if ($("#tire_make_6").length != 0) {
            if ($("#tire_make_6").val() == '') {

                error = 1;
                $("#t_make_6").css("color", "#ff0000");

            }
        }


        if ($("#tire_model_1").length != 0) {
            if ($("#tire_model_1").val() == '') {

                error = 1;
                $("#t_model_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_model_2").length != 0) {
            if ($("#tire_model_2").val() == '') {

                error = 1;
                $("#t_model_2").css("color", "#ff0000");

            }
        }
        if ($("#tire_model_3").length != 0) {
            if ($("#tire_model_3").val() == '') {

                error = 1;
                $("#t_model_3").css("color", "#ff0000");

            }
        }

        if ($("#tire_model_4").length != 0) {
            if ($("#tire_model_4").val() == '') {

                error = 1;
                $("#t_model_4").css("color", "#ff0000");

            }
        }

        if ($("#tire_model_5").length != 0) {
            if ($("#tire_model_5").val() == '') {

                error = 1;
                $("#t_model_5").css("color", "#ff0000");

            }
        }

        if ($("#tire_model_6").length != 0) {
            if ($("#tire_model_6").val() == '') {

                error = 1;
                $("#t_model_6").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_1").length != 0) {
            if ($("#tire_size_1").val() == '') {

                error = 1;
                $("#t_size_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_2").length != 0) {
            if ($("#tire_size_2").val() == '') {

                error = 1;
                $("#t_size_2").css("color", "#ff0000");

            }
        }
        if ($("#tire_size_3").length != 0) {
            if ($("#tire_size_3").val() == '') {

                error = 1;
                $("#t_size_3").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_4").length != 0) {
            if ($("#tire_size_4").val() == '') {

                error = 1;
                $("#t_size_4").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_5").length != 0) {
            if ($("#tire_size_5").val() == '') {

                error = 1;
                $("#t_size_5").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_6").length != 0) {
            if ($("#tire_size_6").val() == '') {

                error = 1;
                $("#t_size_6").css("color", "#ff0000");

            }
        }


        if ($("#tire_dot_1").length != 0) {
            if ($("#tire_dot_1").val() == '') {

                error = 1;
                $("#t_dot_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_dot_2").length != 0) {
            if ($("#tire_dot_2").val() == '') {

                error = 1;
                $("#t_dot_2").css("color", "#ff0000");

            }
        }
        if ($("#tire_dot_3").length != 0) {
            if ($("#tire_dot_3").val() == '') {

                error = 1;
                $("#t_dot_3").css("color", "#ff0000");

            }
        }

        if ($("#tire_dot_4").length != 0) {
            if ($("#tire_dot_4").val() == '') {

                error = 1;
                $("#t_dot_4").css("color", "#ff0000");

            }
        }

        if ($("#tire_dot_5").length != 0) {
            if ($("#tire_dot_5").val() == '') {

                error = 1;
                $("#t_dot_5").css("color", "#ff0000");

            }
        }

        if ($("#tire_dot_6").length != 0) {
            if ($("#tire_dot_6").val() == '') {

                error = 1;
                $("#t_dot_6").css("color", "#ff0000");

            }
        }
        if ($("#tire_make_1").length != 0) {
            if ($("#tire_price_1").val() == '') {

                error = 1;
                $("#t_price_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_price_2").length != 0) {
            if ($("#tire_price_2").val() == '') {

                error = 1;
                $("#t_price_2").css("color", "#ff0000");

            }
        }
        if ($("#tire_price_3").length != 0) {
            if ($("#tire_price_3").val() == '') {

                error = 1;
                $("#t_price_3").css("color", "#ff0000");

            }
        }

        if ($("#tire_price_4").length != 0) {
            if ($("#tire_price_4").val() == '') {

                error = 1;
                $("#t_price_4").css("color", "#ff0000");

            }
        }

        if ($("#tire_price_5").length != 0) {
            if ($("#tire_price_5").val() == '') {

                error = 1;
                $("#t_price_5").css("color", "#ff0000");

            }
        }

        if ($("#tire_price_6").length != 0) {
            if ($("#tire_price_6").val() == '') {

                error = 1;
                $("#t_price_6").css("color", "#ff0000");

            }
        }


        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });


});

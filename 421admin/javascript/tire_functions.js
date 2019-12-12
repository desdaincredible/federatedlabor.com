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
});

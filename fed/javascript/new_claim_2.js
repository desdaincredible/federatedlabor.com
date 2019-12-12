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

    $("#num_tires").change(function () {
        tire_count = $("#num_tires").val();
        $("#send_message").show();
        $(".tire_divs").hide();
        for (i = 1; i <= tire_count; i++) {
            $("#tire_" + i).show();
        }
        unused = parseInt(tire_count) + 1;
        for (j = unused; j <= 6; j++) {
            $("#tire_make_" + j + ", #tire_model_" + j + ", #tire_size_" + j + ", #tire_dot_" + j + ", #tire_price_" + j).val('');
        }

    });


    $(function () {
        $("#tire_make_1, #tire_make_2").autocomplete({
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

        $("#tire_model_2").autocomplete({
            minLength: 2,
            source: function (request, response) {

                $.getJSON("php/show_tire_models.php", {
                    "vmid": $("#tire_make_id").text(),
                    "term": $("#tire_model_2").val()
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
    $("#tire_make_1").blur(function () {

        if ($("#tire_make_1").val() && $("#tire_make_2").val() == '') {

            $("#tire_make_2").val($("#tire_make_1").val());
        }

    });

    $("#tire_model_1").blur(function () {

        if ($("#tire_model_2").val() == '') {

            $("#tire_model_2").val($("#tire_model_1").val());
        }

    });

    $("#tire_size_1").blur(function () {

        if ($("#tire_size_2").val() == '') {

            $("#tire_size_2").val($("#tire_size_1").val());
        }

    });

    $("#tire_dot_1").blur(function () {

        if ($("#tire_dot_2").val() == '') {

            $("#tire_dot_2").val($("#tire_dot_1").val());
        }

    });

    $("#original_part_number_1").blur(function () {

        if ($("#original_part_number_2").val() == '') {

            $("#original_part_number_2").val($("#original_part_number_1").val());
        }

    });

    $("#claim_part_number_1").blur(function () {

        if ($("#claim_part_number_2").val() == '') {

            $("#claim_part_number_2").val($("#claim_part_number_1").val());
        }

    });

    $("#original_tire_price_1").blur(function () {

        if ($("#original_tire_price_2").val() == '') {

            $("#original_tire_price_2").val($("#original_tire_price_1").val());
        }

    });

    $("#claim_tire_price_1").blur(function () {

        if ($("#claim_tire_price_2").val() == '') {

            $("#claim_tire_price_2").val($("#claim_tire_price_1").val());
        }

    });

    $("#original_tread_depth_1").blur(function () {

        if ($("#original_tread_depth_2").val() == '') {

            $("#original_tread_depth_2").val($("#original_tread_depth_1").val());
        }

    });

    $("#remaining_tread_depth_1").blur(function () {

        if ($("#remaining_tread_depth_2").val() == '') {

            $("#remaining_tread_depth_2").val($("#remaining_tread_depth_1").val());
        }

    });


    $("#new_claim_2").submit(function () {
        $(".plan_header").css("color", "#000000");
        var error = 0;
        if ($("#original_tread_depth").val() == '') {
            error = 1;
            $("#o_tread_depth").css("color", "#ff0000");

        }
        if ($("#current_vehicle_mileage").val() == '') {
            error = 1;
            $("#v_mileage").css("color", "#ff0000");
        }
        if ($("#remaining_tread_depth").val() == '') {
            error = 1;
            $("#r_tread_depth").css("color", "#ff0000");
        }
        if ($("#tire_damage_desc").val() == '') {
            error = 1;
            $("#t_damage_desc").css("color", "#ff0000");
        }

        /* If they aren't using the second tire, clear the values that were auto-added */
        if ($('#tire_2').css('display') == 'none') {

            $("#tire_make_2").val('');

            $("#tire_model_2").val('');

            $("#tire_size_2").val('');

            $("#tire_dot_2").val('');

            $("#original_part_number_2").val('');

            $("#claim_part_number_2").val('');

            $("#original_tire_price_2").val('');

            $("#claim_tire_price_2").val('');

            $("#original_tread_depth_2").val('');

            $("#remaining_tread_depth_2").val('');
        }


        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });

});

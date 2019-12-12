$(document).ready(function () {
    $("#repair").click(function () {
        $("#replace_info").hide();


    });
    $("#replace").click(function () {
        $("#replace_info").show();


    });

    $("#replace_same").click(function () {

        if ($("#replace_same").attr("checked") == false) {
            $("#replacement_tire_make").val('');

            $("#replacement_tire_model").val('');

            $("#replacement_tire_size").val('');


        }


        else {
            $("#replacement_tire_make").val($("#c_tire_make").html());

            $("#replacement_tire_model").val($("#c_tire_model").html());

            $("#replacement_tire_size").val($("#c_tire_size").html());


        }

    });


    $("#service_price").blur(function () {
        if ($("#repair").attr("checked") == false) {
            $("#service_price").val(formatAsMoney($("#service_price").val()));

        }
        else {

            if (parseFloat($("#service_price").val()) > 20) {
                $("#service_price").val('20.00');
                alert("Repairs are limited to $20.00");
            }
            else {
                $("#service_price").val(formatAsMoney($("#service_price").val()));
            }

        }


    });


    $("#new_claim_2").submit(function () {
        $(".plan_header").css("color", "#000000");
        var error = 0;
        if ($("#original_tread_depth").val() == '') {
            error = 1;
            $("#o_tread_depth").css("color", "#ff0000");

        }
        if ($("#remaining_tread_depth").val() == '') {
            error = 1;
            $("#r_tread_depth").css("color", "#ff0000");
        }
        if ($("#tire_damage_desc").val() == '') {
            error = 1;
            $("#t_damage_desc").css("color", "#ff0000");
        }

        if ($("#service_price").val() == '') {
            error = 1;
            $("#s_price").css("color", "#ff0000");
        }


        if ($("#repair").attr("checked") == false) {

            if ($("#replacement_tire_make").val() == '') {
                error = 1;
                $("#r_tire_make").css("color", "#ff0000");
            }
            if ($("#replacement_tire_model").val() == '') {
                error = 1;
                $("#r_tire_model").css("color", "#ff0000");
            }
            if ($("#replacement_tire_size").val() == '') {
                error = 1;
                $("#r_tire_size").css("color", "#ff0000");
            }

        }
        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });


    $(function () {
        $("#treplacement_tire_make").autocomplete({
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

        $("#replacement_tire_model").autocomplete({
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
});

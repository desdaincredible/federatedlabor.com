$(document).ready(function () {
    $("#claim_check").submit(function () {

        return false;
    });

    $("#claim_edit").click(function () {

        $("#next_page").val('0');
        $("#claim_review").hide();
        $("#claim_complete").hide();
        $("#claim_edit").hide();
        $("#edit_claim").show();
        $("#final_check").show();
    });


    $("#claim_complete").click(function () {

        $("#next_page").val('1');
        document.claim_check.submit();

    });

    $("#final_check").click(function () {

        $("#next_page").val('0');
        document.claim_check.submit();

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

    $("#claim_check").submit(function () {
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


});

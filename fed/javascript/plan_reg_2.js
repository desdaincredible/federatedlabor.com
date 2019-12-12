$(document).ready(function () {


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


    $("#tire_make_2").autocomplete({
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

    $("#tire_make_3").autocomplete({
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

    $("#tire_model_3").autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
                "vmid": $("#tire_make_id").text(),
                "term": $("#tire_model_3").val()
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
    $("#tire_make_4").autocomplete({
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

    $("#tire_model_4").autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
                "vmid": $("#tire_make_id").text(),
                "term": $("#tire_model_4").val()
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
    $("#tire_make_5").autocomplete({
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

    $("#tire_model_5").autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
                "vmid": $("#tire_make_id").text(),
                "term": $("#tire_model_5").val()
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
    $("#tire_make_6").autocomplete({
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

    $("#tire_model_6").autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON("php/show_tire_models.php", {
                "vmid": $("#tire_make_id").text(),
                "term": $("#tire_model_6").val()
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


    $('.formcheck').keypress(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {

            if ($(this).attr('tabindex')) {

                if ($(this).attr('id').indexOf("price") != -1) {

                    var price = formatAsMoney($(this).val());
                    $(this).val(price);


                }
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

    $("#tire_make_1").blur(function () {

        tire_count = $("#num_tires").val();
        for (i = 2; i <= tire_count; i++) {

            if ($("#tire_make_" + i).val() == '') {

                $("#tire_make_" + i).val($("#tire_make_1").val());

            }

        }

    });
    $("#tire_model_1").blur(function () {

        tire_count = $("#num_tires").val();
        for (i = 2; i <= tire_count; i++) {

            if ($("#tire_model_" + i).val() == '') {

                $("#tire_model_" + i).val($("#tire_model_1").val());

            }

        }

    });

    $("#tire_size_1").blur(function () {

        tire_count = $("#num_tires").val();
        for (i = 2; i <= tire_count; i++) {

            if ($("#tire_size_" + i).val() == '') {

                $("#tire_size_" + i).val($("#tire_size_1").val());

            }

        }

    });

    $("#tire_dot_1").blur(function () {
        tire_count = $("#num_tires").val();
        for (i = 2; i <= tire_count; i++) {

            if ($("#tire_dot_" + i).val() == '') {

                $("#tire_dot_" + i).val($("#tire_dot_1").val());

            }

        }

    });

    $("#tire_price_1").blur(function () {
        if (parseFloat($(this).val()) < 40.00) {
            $(this).val('');
            alert('Tire Price must be at least $40.00');
        }
        else {
            var price = formatAsMoney($(this).val());
            $(this).val(price);

            tire_count = $("#num_tires").val();
            for (i = 2; i <= tire_count; i++) {

                if ($("#tire_price_" + i).val() == '') {

                    $("#tire_price_" + i).val(price);

                }

            }

        }

    });
    $("#tire_price_2, #tire_price_3, #tire_price_4, #tire_price_5, #tire_price_6").blur(function () {
        if (parseFloat($(this).val()) < 40.00) {
            $(this).val('');
            alert('Tire Price must be at least $40.00');


        }
        else {
            var price = formatAsMoney($(this).val());
            $(this).val(price);
        }
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

        if ($("#tire_model_1").length != 0) {
            if ($("#tire_model_1").val() == '') {

                error = 1;
                $("#t_model_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_size_1").length != 0) {
            if ($("#tire_size_1").val() == '') {

                error = 1;
                $("#t_size_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_dot_1").length != 0) {
            if ($("#tire_dot_1").val() == '') {

                error = 1;
                $("#t_dot_1").css("color", "#ff0000");

            }
        }

        if ($("#tire_price_1").length != 0) {
            if ($("#tire_price_1").val() == '') {

                error = 1;
                $("#t_price_1").css("color", "#ff0000");

            }
            if (parseFloat($("#tire_price_1").val() < 40.00)) {
                $("#t_price_1").html('Tire Price must be at least $40.00').css("color", "#ff0000");

            }
        }


        if ($("#tire_2").css("display") == "block") {

            if ($("#tire_make_2").val() == '') {

                error = 1;
                $("#t_make_2").css("color", "#ff0000");

            }

            if ($("#tire_model_2").val() == '') {

                error = 1;
                $("#t_model_2").css("color", "#ff0000");

            }

            if ($("#tire_size_2").val() == '') {

                error = 1;
                $("#t_size_2").css("color", "#ff0000");

            }

            if ($("#tire_dot_2").val() == '') {

                error = 1;
                $("#t_dot_2").css("color", "#ff0000");

            }

            if ($("#tire_price_2").val() == '') {

                error = 1;
                $("#t_price_2").css("color", "#ff0000");

            }

        }//if($("#tire_2").css("display") == "block"){


        if ($("#tire_3").css("display") == "block") {

            if ($("#tire_make_3").val() == '') {

                error = 1;
                $("#t_make_3").css("color", "#ff0000");

            }

            if ($("#tire_model_3").val() == '') {

                error = 1;
                $("#t_model_3").css("color", "#ff0000");

            }

            if ($("#tire_size_3").val() == '') {

                error = 1;
                $("#t_size_3").css("color", "#ff0000");

            }

            if ($("#tire_dot_3").val() == '') {

                error = 1;
                $("#t_dot_3").css("color", "#ff0000");

            }

            if ($("#tire_price_3").val() == '') {

                error = 1;
                $("#t_price_3").css("color", "#ff0000");

            }

        }//if($("#tire_3").css("display") == "block"){


        if ($("#tire_4").css("display") == "block") {

            if ($("#tire_make_4").val() == '') {

                error = 1;
                $("#t_make_4").css("color", "#ff0000");

            }

            if ($("#tire_model_4").val() == '') {

                error = 1;
                $("#t_model_4").css("color", "#ff0000");

            }

            if ($("#tire_size_4").val() == '') {

                error = 1;
                $("#t_size_4").css("color", "#ff0000");

            }

            if ($("#tire_dot_4").val() == '') {

                error = 1;
                $("#t_dot_4").css("color", "#ff0000");

            }

            if ($("#tire_price_4").val() == '') {

                error = 1;
                $("#t_price_4").css("color", "#ff0000");

            }

        }//if($("#tire_4").css("display") == "block"){


        if ($("#tire_5").css("display") == "block") {

            if ($("#tire_make_5").val() == '') {

                error = 1;
                $("#t_make_5").css("color", "#ff0000");

            }

            if ($("#tire_model_5").val() == '') {

                error = 1;
                $("#t_model_5").css("color", "#ff0000");

            }

            if ($("#tire_size_5").val() == '') {

                error = 1;
                $("#t_size_5").css("color", "#ff0000");

            }

            if ($("#tire_dot_5").val() == '') {

                error = 1;
                $("#t_dot_5").css("color", "#ff0000");

            }

            if ($("#tire_price_5").val() == '') {

                error = 1;
                $("#t_price_5").css("color", "#ff0000");

            }

        }//if($("#tire_5").css("display") == "block"){

        if ($("#tire_6").css("display") == "block") {

            if ($("#tire_make_6").val() == '') {

                error = 1;
                $("#t_make_6").css("color", "#ff0000");

            }

            if ($("#tire_model_6").val() == '') {

                error = 1;
                $("#t_model_6").css("color", "#ff0000");

            }


            if ($("#tire_size_6").length != 0) {
                if ($("#tire_size_6").val() == '') {

                    error = 1;
                    $("#t_size_6").css("color", "#ff0000");

                }
            }

            if ($("#tire_dot_6").length != 0) {
                if ($("#tire_dot_6").val() == '') {

                    error = 1;
                    $("#t_dot_6").css("color", "#ff0000");

                }


            }

            if ($("#tire_price_6").length != 0) {
                if ($("#tire_price_6").val() == '') {

                    error = 1;
                    $("#t_price_6").css("color", "#ff0000");

                }
            }

        }//if($("#tire_6").css("display") == "block"){

        if (error == 1) {
            alert("Please fill in the missing fields");
            return false;

        }
    });


});

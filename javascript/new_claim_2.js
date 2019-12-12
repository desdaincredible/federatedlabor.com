$(document).ready( function(){
	$('.formcheck').keypress(function(event){

		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){

			if($(this).attr('tabindex')){

				if($(".formcheck[rel='last']").attr('tabindex') != $(this).attr('tabindex')){

					var newtab  = parseInt($(this).attr('tabindex')) + 1;

					$(".formcheck[tabindex='" + newtab +"']").focus();

				}

			}

			return false;




		}

	});

     $("#num_tires").change(function(){
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




		$(function() {
			$("#tire_make_1, #tire_make_2").autocomplete({
				minLength: 2,
				source: "php/show_tire_makes.php",
				select: function(event, ui) {
					if(ui.item.value == 'No Suggestions Found'){
						ui.item.value = '';
					}
					$(this).val(ui.item.value);
					$("#tire_make_id").text(ui.item.make_id);
					vmid = ui.item.make_id;
			}




			});

			$("#tire_model_1").autocomplete({
				minLength: 2,
				source: function(request,response) {

					$.getJSON("php/show_tire_models.php",{"vmid":$("#tire_make_id").text(), "term": $("#tire_model_1").val()},function(data) {
					response(data);
					} );

				},
				select: function(event, ui) {

					if(ui.item.value == 'No Suggestions Found'){
						ui.item.value = '';
					}
					$(this).val(ui.item.value);
				}




			});

			$("#tire_model_2").autocomplete({
				minLength: 2,
				source: function(request,response) {

					$.getJSON("php/show_tire_models.php",{"vmid":$("#tire_make_id").text(), "term": $("#tire_model_2").val()},function(data) {
					response(data);
					} );

				},
				select: function(event, ui) {

					if(ui.item.value == 'No Suggestions Found'){
						ui.item.value = '';
					}
					$(this).val(ui.item.value);
				}




			});

		});
	$("#tire_make_1").blur(function(){

		if($("#tire_make_1").val() && $("#tire_make_2").val() == ''){

			$("#tire_make_2").val($("#tire_make_1").val());
		}

	});

	$("#tire_model_1").blur(function(){

		if($("#tire_model_2").val() == ''){

			$("#tire_model_2").val($("#tire_model_1").val());
		}

	});

	$("#tire_size_1").blur(function(){

		if($("#tire_size_2").val() == ''){

			$("#tire_size_2").val($("#tire_size_1").val());
		}

	});

	$("#tire_dot_1").blur(function(){

		if($("#tire_dot_2").val() == ''){

			$("#tire_dot_2").val($("#tire_dot_1").val());
		}

	});

	$("#original_part_number_1").blur(function(){

		if($("#original_part_number_2").val() == ''){

			$("#original_part_number_2").val($("#original_part_number_1").val());
		}

	});

	$("#claim_part_number_1").blur(function(){

		if($("#claim_part_number_2").val() == ''){

			$("#claim_part_number_2").val($("#claim_part_number_1").val());
		}

	});

	$("#original_tire_price_1").blur(function(){

		if($("#original_tire_price_2").val() == ''){

			$("#original_tire_price_2").val($("#original_tire_price_1").val());
		}

	});

	$("#claim_tire_price_1").blur(function(){

		if($("#claim_tire_price_2").val() == ''){

			$("#claim_tire_price_2").val($("#claim_tire_price_1").val());
		}

	});

	$("#original_tread_depth_1").blur(function(){

		if($("#original_tread_depth_2").val() == ''){

			$("#original_tread_depth_2").val($("#original_tread_depth_1").val());
		}

	});

	$("#remaining_tread_depth_1").blur(function(){

		if($("#remaining_tread_depth_2").val() == ''){

			$("#remaining_tread_depth_2").val($("#remaining_tread_depth_1").val());
		}

	});







	$("#new_claim_2").submit(function(){
		$(".plan_header").css("color","#000000");
		var error = 0;
		if($("#original_tread_depth").val() == ''){
			error = 1;
			$("#o_tread_depth").css("color","#ff0000");

		}
		if($("#current_vehicle_mileage").val() == ''){
			error = 1;
			$("#v_mileage").css("color","#ff0000");
		}
		if($("#remaining_tread_depth").val() == ''){
			error = 1;
			$("#r_tread_depth").css("color","#ff0000");
		}
		if($("#tire_damage_desc").val() == ''){
			error = 1;
			$("#t_damage_desc").css("color","#ff0000");
		}

	/* If they aren't using the second tire, clear the values that were auto-added */
	if($('#tire_2').css('display') == 'none'){

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



		if(error == 1){
		 alert("Please fill in the missing fields");
		 return false;

		}
	});

    /* Calculate Credit */
    $('.formcheck.tdepth_1').bind("keyup change", function (e) {
        var cost = +$("#original_tire_price_1").val();
        var original = +$("#original_tread_depth_1").val();
        var replace = +$("#remaining_tread_depth_1").val();
        console.log(cost, original, replace);
        var percent = (replace / original) * 100;
        var percent_after = (replace / (original - 2)) * 100;
        var total = (cost * percent_after) / 100;
        if (original != '' && replace != '' && cost != '' && percent <= 75 && replace > 2) {
            $('#percent_1').html(percent_after.toFixed(2) + '%');
            $('input[name="percent_1"]').val(percent_after.toFixed(2));
            $('#coverage_1').html('$' + total.toFixed(2));
            $('input[name="coverage_1"]').val(total.toFixed(2));
        }
        else if (original != '' && replace != '' && cost != '' && percent > 75 && replace > 2) {
            $('#percent_1').html('100%');
            $('input[name="percent_1"]').val('100');
            $('#coverage_1').html('$' + cost.toFixed(2));
            $('input[name="coverage_1"]').val(cost.toFixed(2));
        }
        else if (original != '' && replace != '' && cost != '' && replace <= 2) {
            $('#percent_1').html('N/A');
            $('#coverage_1').html('<strong>Tire is not eligible for coverage.</strong>');
        }

        else {
            $('#percent_1').html('');
            $('#coverage_1').html("<span class='small-print'>Enter all values above to calculate</span>");
        }

    });

    $('.formcheck.tdepth_2').bind("keyup change", function (e) {
        var cost = +$("#original_tire_price_2").val();
        var original = +$("#original_tread_depth_2").val();
        var replace = +$("#remaining_tread_depth_2").val();
        var percent = (replace / original) * 100;
        var percent_after = (replace / (original - 2)) * 100;
        var total = (cost * percent_after) / 100;
        $('.formcheck.tdepth_2').each(function () {
            if (original != '' && replace != '' && cost != '' && percent <= 75 && replace > 2) {
                $('#percent_2').html(percent_after.toFixed(2) + '%');
                $('input[name="percent_2"]').val(percent_after.toFixed(2));
                $('#coverage_2').html('$' + total.toFixed(2));
                $('input[name="coverage_2"]').val(total.toFixed(2));
            }
            else if (original != '' && replace != '' && cost != '' && percent > 75 && replace > 2) {
                $('#percent_2').html('100%');
                $('input[name="percent_2"]').val('100');
                $('#coverage_2').html('$' + cost.toFixed(2));
                $('input[name="coverage_2"]').val(cost.toFixed(2));
            }
            else if (original != '' && replace != '' && cost != '' && replace <= 2) {
                $('#percent_2').html('N/A');
                $('#coverage_2').html('<strong>Tire is not eligible for coverage.</strong>');
            }
            else {
                $('#percent_2').html('');
                $('#coverage_2').html("<span class='small-print'>Enter all values above to calculate</span>");
            }
        })
    });
});
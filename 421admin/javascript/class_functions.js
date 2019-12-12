function addQuestion(cloneId) {
    var currentId = cloneId.replace('add_', '');
    nextId = parseInt(currentId) + 1;
    var currentDiv = '#question_' + currentId;


    var newDiv = '<div id="question_' + currentId + '">' + $(currentDiv).html() + '</div>';

    /*update code for new id */

    var stringToReplace = 'Question ' + currentId;
    var replacement = 'Question ' + nextId;

    newDiv = newDiv.replace(stringToReplace, replacement);
    stringToReplace = new RegExp('_' + currentId, 'gi');

    replacement = '_' + nextId;

    newDiv = newDiv.replace(stringToReplace, replacement);
    if (currentId == 19) {

        $("#newQuestion").hide();
        $("#divider").hide();
        $("#remove_question").attr("name", "remove_" + nextId);

    }
    else {
        $("#minusQuestion").show();
        $("#divider").show();
        $("#remove_question").attr("name", "remove_" + nextId);
        $("#add_question").attr("name", "add_" + nextId);


    }

    $(currentDiv).after(newDiv);


    currentId = null;
    nextId = null;
    currentDiv = null;
    newDiv = $(currentDiv).html();
    stringToReplace = null;
    replacement = null;


}

function removeQuestion(cloneId) {

    var currentId = cloneId.replace('remove_', '');
    var prevId = parseInt(currentId) - 1;
    var currentDiv = '#question_' + currentId;
    $(currentDiv).remove();

    if (currentId == 2) {

        $("#minusQuestion").hide();
        $("#divider").hide();
        $("#add_question").attr("name", "add_" + prevId);


    }
    else {
        $("#newQuestion").show();
        $("#divider").show();
        $("#remove_question").attr("name", "remove_" + prevId);
        $("#add_question").attr("name", "add_" + prevId);


    }

}

$(document).ready(function () {


    $("#add_question").click(function () {
        addQuestion($(this).attr('name'));
        return false;


    });

    $("#remove_question").click(function () {
        removeQuestion($(this).attr('name'));
        return false;


    });


    $('#new_test_questions').submit(function () {
        var error = '';

        if ($('#question_1').length) {
            if ($('#answer_1_a_correct').attr('checked') == false && $('#answer_1_b_correct').attr('checked') == false && $('#answer_1_c_correct').attr('checked') == false && $('#answer_1_d_correct').attr('checked') == false && $('#answer_1_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 1\n";

            }

        }
        if ($('#question_2').length) {
            if ($('#answer_2_a_correct').attr('checked') == false && $('#answer_2_b_correct').attr('checked') == false && $('#answer_2_c_correct').attr('checked') == false && $('#answer_2_d_correct').attr('checked') == false && $('#answer_2_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 2\n";

            }

        }
        if ($('#question_3').length) {
            if ($('#answer_3_a_correct').attr('checked') == false && $('#answer_3_b_correct').attr('checked') == false && $('#answer_3_c_correct').attr('checked') == false && $('#answer_3_d_correct').attr('checked') == false && $('#answer_3_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 3\n";

            }

        }
        if ($('#question_4').length) {
            if ($('#answer_4_a_correct').attr('checked') == false && $('#answer_4_b_correct').attr('checked') == false && $('#answer_4_c_correct').attr('checked') == false && $('#answer_4_d_correct').attr('checked') == false && $('#answer_4_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 4\n";

            }

        }
        if ($('#question_5').length) {
            if ($('#answer_5_a_correct').attr('checked') == false && $('#answer_5_b_correct').attr('checked') == false && $('#answer_5_c_correct').attr('checked') == false && $('#answer_5_d_correct').attr('checked') == false && $('#answer_5_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 5\n";

            }

        }
        if ($('#question_6').length) {
            if ($('#answer_6_a_correct').attr('checked') == false && $('#answer_6_b_correct').attr('checked') == false && $('#answer_6_c_correct').attr('checked') == false && $('#answer_6_d_correct').attr('checked') == false && $('#answer_6_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 6\n";

            }

        }
        if ($('#question_7').length) {
            if ($('#answer_7_a_correct').attr('checked') == false && $('#answer_7_b_correct').attr('checked') == false && $('#answer_7_c_correct').attr('checked') == false && $('#answer_7_d_correct').attr('checked') == false && $('#answer_7_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 7\n";

            }

        }
        if ($('#question_8').length) {
            if ($('#answer_8_a_correct').attr('checked') == false && $('#answer_8_b_correct').attr('checked') == false && $('#answer_8_c_correct').attr('checked') == false && $('#answer_8_d_correct').attr('checked') == false && $('#answer_8_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 8\n";

            }

        }
        if ($('#question_9').length) {
            if ($('#answer_9_a_correct').attr('checked') == false && $('#answer_9_b_correct').attr('checked') == false && $('#answer_9_c_correct').attr('checked') == false && $('#answer_9_d_correct').attr('checked') == false && $('#answer_9_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 9\n";

            }

        }
        if ($('#question_10').length) {
            if ($('#answer_10_a_correct').attr('checked') == false && $('#answer_10_b_correct').attr('checked') == false && $('#answer_10_c_correct').attr('checked') == false && $('#answer_10_d_correct').attr('checked') == false && $('#answer_10_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 10\n";

            }

        }
        if ($('#question_11').length) {
            if ($('#answer_11_a_correct').attr('checked') == false && $('#answer_11_b_correct').attr('checked') == false && $('#answer_11_c_correct').attr('checked') == false && $('#answer_11_d_correct').attr('checked') == false && $('#answer_11_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 11\n";

            }

        }
        if ($('#question_12').length) {
            if ($('#answer_12_a_correct').attr('checked') == false && $('#answer_12_b_correct').attr('checked') == false && $('#answer_12_c_correct').attr('checked') == false && $('#answer_12_d_correct').attr('checked') == false && $('#answer_12_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 12\n";

            }

        }

        if ($('#question_13').length) {
            if ($('#answer_13_a_correct').attr('checked') == false && $('#answer_13_b_correct').attr('checked') == false && $('#answer_13_c_correct').attr('checked') == false && $('#answer_13_d_correct').attr('checked') == false && $('#answer_13_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 13\n";

            }

        }
        if ($('#question_14').length) {
            if ($('#answer_14_a_correct').attr('checked') == false && $('#answer_14_b_correct').attr('checked') == false && $('#answer_14_c_correct').attr('checked') == false && $('#answer_14_d_correct').attr('checked') == false && $('#answer_14_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 14\n";

            }

        }

        if ($('#question_15').length) {
            if ($('#answer_15_a_correct').attr('checked') == false && $('#answer_15_b_correct').attr('checked') == false && $('#answer_15_c_correct').attr('checked') == false && $('#answer_15_d_correct').attr('checked') == false && $('#answer_15_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 15\n";

            }

        }
        if ($('#question_16').length) {
            if ($('#answer_16_a_correct').attr('checked') == false && $('#answer_16_b_correct').attr('checked') == false && $('#answer_16_c_correct').attr('checked') == false && $('#answer_16_d_correct').attr('checked') == false && $('#answer_16_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 16\n";

            }

        }

        if ($('#question_17').length) {
            if ($('#answer_17_a_correct').attr('checked') == false && $('#answer_17_b_correct').attr('checked') == false && $('#answer_17_c_correct').attr('checked') == false && $('#answer_17_d_correct').attr('checked') == false && $('#answer_17_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 17\n";

            }

        }
        if ($('#question_18').length) {
            if ($('#answer_18_a_correct').attr('checked') == false && $('#answer_18_b_correct').attr('checked') == false && $('#answer_18_c_correct').attr('checked') == false && $('#answer_18_d_correct').attr('checked') == false && $('#answer_18_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 18\n";

            }

        }
        if ($('#question_19').length) {
            if ($('#answer_19_a_correct').attr('checked') == false && $('#answer_19_b_correct').attr('checked') == false && $('#answer_19_c_correct').attr('checked') == false && $('#answer_19_d_correct').attr('checked') == false && $('#answer_19_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 19\n";

            }

        }
        if ($('#question_20').length) {
            if ($('#answer_20_a_correct').attr('checked') == false && $('#answer_20_b_correct').attr('checked') == false && $('#answer_20_c_correct').attr('checked') == false && $('#answer_20_d_correct').attr('checked') == false && $('#answer_20_e_correct').attr('checked') == false) {
                error += "Please check a correct answer for question 20\n";

            }

        }


        if (error != '') {
            alert(error);
            return false;

        }

    });


});

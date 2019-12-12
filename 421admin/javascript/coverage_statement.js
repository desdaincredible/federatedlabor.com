/* The actual print function */
var VBS = false;

function prePrint() {
    if (window.print) window.print();
    else alert('This script does not work in your browser');
}

function finish() {
    var answer = confirm("Are you sure you are finished (did the coverage statement print correctly)?");
    if (answer) {
        parent.location = 'dealer_index';
    }
    else {
        return false;
    }
}

function printAlert() {
    alert('Print this page and attach it to your sales invoice to give to the customer\nBe sure to click the Finish button at the bottom of the page when done.');
}

$(document).ready(function () {


    $("#printPlan").click(function () {
        prePrint();


    });

    $("#printFinish").click(function () {

        finish();

    });
    printAlert();

});

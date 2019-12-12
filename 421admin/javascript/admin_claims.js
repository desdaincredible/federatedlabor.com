/* The actual print function */
var VBS = false;

function prePrint() {
    if (window.print) window.print();
    else alert('This script does not work in your browser');
}

function finish(pageRef) {
    parent.location = 'admin_index';
}

function printAlert() {
    alert('This can be printed as a PDF file and attached to an email if the dealer needs the claim page re-sent.');
}

$(document).ready(function () {

    $("#printClaim").click(function () {
        prePrint();


    });
    $("#printFinish").click(function () {
        finish();

    });

    printAlert();
});

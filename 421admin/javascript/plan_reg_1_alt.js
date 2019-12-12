function getModel(pageName, inputBox) {

    var queryItem = inputBox.val();
    if (pageName == 'show_models.php') {
        queryItem = queryItem + '&make_id=' + $("#make_id").text();

    }


    $.getJSON('php/' + pageName + '?term=' + queryItem,
        function (data) {
            $.each(data.items, function (i, item) {
                alert(data.value);
                //$("<img/>").attr("src", item.media.m).appendTo("#images");
                //if ( i == 3 ) return false;
            });
        });


}


$(document).ready(function () {

    $("#comm_ag_use_yes").click(function () {
        alert("Commercial or Agricultural vehicles are excluded from the road hazard warranty. You cannot register these types of vehicles.");
        $("#comm_ag_use_no").attr('checked', true);
    });

    $("#customer_phone").blur(function () {
        var phoneString = $(this).val();
        if (phoneString != '') {
            phoneString = phoneString.replace(/[\(\)\.\-\ ]/g, '');
            if (isNaN(parseInt(phoneString))) {
                alert("The phone number contains illegal characters");
                $(this).val('');
                return;
            }

            if (!(phoneString.length == 10)) {
                alert("Please enter the full phone number including area code");
                $(this).val('');
                return;

            }

            var areaCode = phoneString.substring(0, 3);
            var locality = phoneString.substring(3, 6);
            var digits = phoneString.substring(6, 10);

            $(this).value = '(' + areaCode + ') ' + locality + '-' + digits

            areaCode = null;
            locality = null;
            digits = null;

        }


    });
    $(function () {
        $("#vehicle_make").keyup(function () {

            if ($(this).val().length >= 2) {
                getModel('show_makes.php', $(this))

            }


        });

        /*$("#vehicle_model").autocomplete({
            source: "php/show_makes.php",
            minLength: 2,).
            select: function(event, ui) {

                if(ui.item.value == 'No Suggestions Found'){
                    ui.item.value = '';
                }

                $(this).val(ui.item.value);


            }
        });
        */
    });


});

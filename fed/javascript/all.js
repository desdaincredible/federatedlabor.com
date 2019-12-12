function formatAsMoney(mnt) {
    mnt = mnt.replace('$', '');

    mnt -= 0;
    mnt = (Math.round(mnt * 100)) / 100;
    return (mnt == Math.floor(mnt)) ? mnt + '.00'
        : ( (mnt * 10 == Math.floor(mnt * 10)) ?
            mnt + '0' : mnt);
}


$(document).ready(function () {

    $("#about, #login, #how_it_works,#sign_up, #contact").hover(
        function () {
            $(this).attr('src', $(this).attr('src').replace('.', '_h.'));


        },
        function () {
            $(this).attr('src', $(this).attr('src').replace('_h.', '.'));
        }
    );

    $("#original_repair_date").datepicker({dateFormat: 'yy-mm-dd'});
    $("#sub_repair_date").datepicker({dateFormat: 'yy-mm-dd'});

    $(function () {
        $("#vehicle_model").autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.getJSON("show_models", {
                    "vmid": $("#vehicle_make").val(),
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

});

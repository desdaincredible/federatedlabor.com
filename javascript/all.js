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

});

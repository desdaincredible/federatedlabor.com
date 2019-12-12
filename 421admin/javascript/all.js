function formatAsMoney(mnt) {
    mnt -= 0;
    mnt = (Math.round(mnt * 100)) / 100;
    return (mnt == Math.floor(mnt)) ? mnt + '.00'
        : ( (mnt * 10 == Math.floor(mnt * 10)) ?
            mnt + '0' : mnt);
}


$(document).ready(function () {

    $("#about, #training, #email, #products_services, #contact").hover(
        function () {
            $(this).attr('src', $(this).attr('src').replace('.', '_h.'));


        },
        function () {
            $(this).attr('src', $(this).attr('src').replace('_h.', '.'));
        }
    );


    // Fazalrasel
    // claim search
    $('#search_type').on('change', function () {
        const classes = ['date', 'dealer_phone', 'claim_number'];
        classes.forEach(function (cName) {
            const className = '.' + cName;
            $(className).hide();
        });
        const thisClassName = '.' + this.value;
        $(thisClassName).show();
    });

    $(".date").datepicker({dateFormat: 'yy-mm-dd'});
    $('.search_btn').on('click', function () {
        const typeSelected = $('#search_type').val();
        let inputValue = '';
        if (typeSelected === 'date') {
            inputValue = $(".date").val();
        } else {
            inputValue = $(`input[name=${typeSelected}]`).val();
        }
        // let's make some ajax call
        // console.log(inputValue);
        $.get('./admin_claim_search_ajax', {type: typeSelected, value: inputValue}, {
            xhrFields: {
                withCredentials: true
            }
        })
            .then(res => {
                if (res.length <= 0) {
                    $('#claim_search_result tbody').text('No Claim Found.');
                } else {
                    const tbody = $('<tbody>');
                    res.forEach(c => {
                        const tr = $('<tr>');
                        // http://ntwclaims.net/421admin/admin_claims?cid=113
                        const anchor = $('<a>').attr('href', './admin_claims?cid=' + c.claim_id).text(c.claim_id);
                        tr.append($('<td>').html(anchor));
                        tr.append($('<td>').text(c.claim_date));
                        tr.append($('<td>').text(c.dealer_id));
                        tbody.append(tr);
                    });
                    $('#claim_search_result tbody').remove();
                    $('#claim_search_result').append(tbody);
                }
            })

    });
    $('.search_btn_labor').on('click', function () {
        const typeSelected = $('#search_type').val();
        let inputValue = '';
        if (typeSelected === 'date') {
            inputValue = $(".date").val();
        } else {
            inputValue = $(`input[name=${typeSelected}]`).val();
        }
        // let's make some ajax call
        // console.log(inputValue);
        $.get('./admin_claim_labor_search_ajax', {type: typeSelected, value: inputValue}, {
            xhrFields: {
                withCredentials: true
            }
        })
            .then(res => {
                if (res.length <= 0) {
                    $('#claim_search_result tbody').text('No Claim Found.');
                } else {
                    const tbody = $('<tbody>');
                    res.forEach(c => {
                        const tr = $('<tr>');
                        // http://ntwclaims.net/421admin/admin_claims?cid=113
                        const anchor = $('<a>').attr('href', './admin_claims_labor?cid=' + c.claim_id).text(c.claim_id);
                        tr.append($('<td>').html(anchor));
                        tr.append($('<td>').text(c.original_repair_date));
                        tr.append($('<td>').text(c.dealer_id));
                        tbody.append(tr);
                    });
                    $('#claim_search_result tbody').remove();
                    $('#claim_search_result').append(tbody);
                }
            })
    });

    // end FazalRasel
});

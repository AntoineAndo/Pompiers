$(function () {
    $('table.records_list td.td').dblclick(function (e) {
        e.stopPropagation();
        var currentEle = $(this);
        var value = $(this).html();
        updateVal(currentEle, value);
    });
});

function updateVal(currentEle, value) {

    $form = '<div style="position: fixed; top: 0px; left: 0px;background-color: rgba(40,63,60,0.4);width: 100%; height: 100%;">';
    $form += '<p class="instruction">Entrée pour valider</p>';
    $form += '<p class="instruction">Echap pour annuler</p>';
    $form += '</div>';
    $form += '<form action="" method="PUT" style="position:relative;">';
    $form += '<input class="edit" type="text" value="' + value + '" />';
    $form += '<button class="submit" >✓</button>';
    $form += '</form>';

    currentEle = currentEle[0];

    if (currentEle.firstElementChild) {
    }
    else {
        $(currentEle).html($form);
        $(".edit").focus().keyup(function (event) {
            if (event.keyCode == 13) {
                $(currentEle).html($(".edit").val().trim());
            }
            if (event.keyCode == 27) {
                $(currentEle).html(value);
            }
        });

        $('button.submit').click(function () {
            $(currentEle).html($(".edit").val().trim());

            var parentRow = currentEle.parentElement;
            var values = {};
            var arr = $(parentRow).find('td').toArray();
            arr.pop();

            $.each(arr, function (index, cell) {
                var key = getColumnName(cell);
                values[key] = $(cell).html();
            });

            var url = parentRow.baseURI + '/' + parentRow.id;

            $.ajax({
                url: url,
                type: 'PUT',
                data: values,
                success: function () {
                },
                statusCode: {
                    500: function () {
                        $(currentEle).html(value);
                        alert("Cette modification ne peut être prise en compte");
                    },
                    200: function () {
                        $(currentEle).html(value);
                        alert("Vous avez été déconnectés, veuillez vous reconnecter pour pouvoir effectuer cette opération");
                    }
                }
            });
        });
    }
}

function getColumnName(currentEle) {
    var th = $('th').eq(currentEle.cellIndex);
    th = th[0];
    return th.innerHTML;
}
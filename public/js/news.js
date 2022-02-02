$(document).ready(function () {
    getTemplate({ url: '/admin' });
});
function templateCreate() {
    $('#divInfo').load('/news/template/create', function () { });
}
function savePost() {
    $(`#guardar_post`).attr('disabled', true).text('Guardando...');
    const form = objectifyForm($('form').serializeArray());
    $.post('/news/create', { data: form }, function ({ process, error }) {
        $(`#guardar_post`).attr('disabled', false).text('Guardar Post');
        if (process) {
            createAlert({ text: 'Muy bien! Se creo la noticia con exito', id: 'message' });
        } else {
            createAlert({ text: error, id: 'message', color: 'danger' });
        }
    });
}

function modalEditar({ id }) {
    $('#editPost').load(`/news/form/edit/${id}`, function () { });
}

function getTemplate({ url }) {
    $('#divInfo').load(`/news${url}`, function () { });
}

function editarPost() {
    $(`#btn_editar`).attr('disabled', true).text('Editando...');
    const { id, ...rest } = objectifyForm($('form').serializeArray());
    $.post(`/news/edit/${id}`, { data: rest }, function ({ process, error }) {
        $(`#btn_editar`).attr('disabled', false).text('Editar');
        if (process) {
            createAlert({ text: 'Muy bien, se edito con exito el post', id: 'alert_edit' });
            $('#editModal').on('hidden.bs.modal', function () {
                getTemplate({ url: '/admin' });
            });
        } else {
            createAlert({ text: 'Upss!, hubo un error al editar el post', id: 'alert_edit', color: 'danger' });
        }
    });
}

function createAlert({ text, id, color = 'primary' }) {
    $(`#${id}`).empty();
    const div = $('<div>', {
        'class': [
            'alert',
            `alert-${color}`,
            'text-center'
        ].join(' '),
        text
    });
    $(`#${id}`).append([div]);
}

function objectifyForm(formArray) {
    //serialize data function
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}
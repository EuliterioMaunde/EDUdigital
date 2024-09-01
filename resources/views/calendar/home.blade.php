<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Calendar</title>
    {{--Inportação do bootstrap--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{--Inportação do plugin de calendário--}}
    <link href='https://unpkg.com/fullcalendar@6.1.5/main.min.css' rel='stylesheet' />
    {{--Plugins que mostra os alertas de erro ou sucesso--}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

</head>
<body>
{{--Botão auxiliar para manipulação do Modal--}}
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" id="btn_show_modal" data-bs-whatever="@mdo" hidden="hidden">Open modal for @mdo</button>
    <div id='calendar'></div>
{{--Modal de edição e remoção de eventos--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Anotações</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add_notes_form" method="post" action="{{route('calendar.store')}}">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Data seleccionada</label>
                            <input type="date" class="form-control" id="selected_date" readonly required>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Título</label>
                            <input class="form-control" type="text" id="title" required maxlength="100"></input>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Descrição</label>
                            <textarea class="form-control" id="anotation_info" required maxlength="500"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="dismiss_modal" data-bs-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary" >Adicionar notas</button>
                            <button type="button" class="btn btn-danger" id="remove_btn" onclick="removeItem()" hidden="hidden">Remover</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
{{--Inportação do plugin de calendário--}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
{{--Inportação do bootstrap--}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
{{--Importação do Jquery--}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{--Plugins que mostra os alertas de erro ou sucesso--}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    var calendar = null;// essa variavel recebe um atributo do tipo calendário
    var checkedDates = {!! json_encode($notes) !!};// Os dados dos eventos previamente criados
    var selectedIndex = -1;// essa variavel serve para indicar se o evento foi ou não seleccionado
    var deteleHasClicked = false;

    document.addEventListener('DOMContentLoaded', function() {
        //Inicializa o plugin de calendário
        var calendarEl = document.getElementById('calendar');
        checkedDates.forEach(function(event, index) {
            event.index = index;  // Adiciona o índice ao objeto do evento
            event.id = index;  // Adiciona o ID ao objeto do evento
        });
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            editable: true,
            selectable: true,
            // Recuperando o conteudo do ficheiro Json previamente criado com os dados da primeira marcação no calendário
            events:checkedDates,
            dateClick: function(info) {// Evento acionado no caso de se clicar numa data
                document.getElementById('remove_btn').setAttribute('hidden','hidden')

                console.log(info);
                selectedIndex = -1;
                document.getElementById('anotation_info').value = '';
                document.getElementById('selected_date').value = '';
                document.getElementById('title').value = '';
                $('#selected_date').val(info.dateStr)
                $('#btn_show_modal').click();
            },
            eventClick: function (info) {// Evento acionado no caso de se clicar num evento existente
                document.getElementById('remove_btn').removeAttribute('hidden')

                // alert(info.event.id)
                console.log(info.event.extendedProps.index);
                selectedIndex = info.event.extendedProps.index;
                // Mostrar descrição da nota ao clicar no evento
                $('#anotation_info').val(info.event.extendedProps.description || '');
                $('#selected_date').val(info.event.startStr);
                $('#title').val(info.event.title || '');
                $('#event_id').val(info.event.id);  // Armazena o ID do evento para remoção
                $('#btn_show_modal').click();
            },
            eventDidMount: function (info) {
                var description = info.event.extendedProps.description;
                if (description) {
                    var tooltip = document.createElement('div');
                    tooltip.innerHTML = description;
                    tooltip.classList.add('tooltip');
                    info.el.appendChild(tooltip);

                    info.el.addEventListener('mouseenter', function () {
                        tooltip.style.display = 'block';
                    });
                    info.el.addEventListener('mouseleave', function () {
                        tooltip.style.display = 'none';
                    });
                }
            }
        });

        calendar.render();
    });
    $("form#add_notes_form").submit(async function(event) {
        event.preventDefault();
        const anotationInfo = document.getElementById('anotation_info').value;
        const selectedDate = document.getElementById('selected_date').value;
        const title = document.getElementById('title').value;
        const formData = new FormData();
        var eventCalendar = calendar.getEventById(selectedIndex);  // Recupera o evento pelo ID

        if(parseInt(selectedIndex)>=0){
            checkedDates.splice(selectedIndex, 1);//Remove o elemento do array
        }
        //Adiciona o elemento no array
        checkedDates.push({
            title: title,
            start: selectedDate,
            description: anotationInfo
        });
        var token = "{{csrf_token()}}";
        formData.append('notes',JSON.stringify(checkedDates))
        //Requisição para actualização do ficheiro de teste no backend
        $.ajax({
            url:"{{ route('calendar.store') }}",
            dataType: 'json',
            method: 'POST',
            headers: {
                'X-CSRF-Token': token,
            },
            data: formData,
            beforeSend: function () {
                // showLoader("A processar o pedido...");
            },
            complete: function () {
                // hideLoader();
            },
            success: function (data) {
                if (parseInt(data.status) == 201) {
                    if (eventCalendar) {
                        // Se o evento existir remove isso nos casos de se clicar um evento já existente
                        eventCalendar.remove();
                    }
                    // Adiciona o envento no plugin
                    calendar.addEvent({
                        title: title,
                        index: checkedDates.length-1,
                        id: checkedDates.length-1,
                        start: selectedDate,
                        description: anotationInfo
                    });
                    $('#dismiss_modal').click();
                    Toastify({
                        text: data.message,
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #0d6efd, #83abe5)",
                        }
                    }).showToast();
                } else {
                    $('#dismiss_modal').click();
                    Toastify({
                        text: data.message,
                        className: "info",
                        style: {
                            background: "linear-gradient(to right,#ed0734, #df6d83)",
                        }
                    }).showToast();

                }
                return false;
            },
            error: function (error) {
                console.log(error.responseJSON);
                // $('#info_error_modal').text(error.responseJSON.message)
                // $('#submit_form_errorModal').click();
                $('#dismiss_modal').click();
                Toastify({
                    text: error.responseJSON.message,
                    className: "info",
                    style: {
                        background: "linear-gradient(to right,#ed0734, #df6d83)",
                    }
                }).showToast();
                return false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    // Esse médoo remove um evento do calendário e actualiza o ficheiro de texto
    function removeItem(){
        const formData = new FormData();

        var eventCalendar = calendar.getEventById(selectedIndex);  // Recupera o evento pelo ID
        if(parseInt(selectedIndex)>=0){

            checkedDates.splice(selectedIndex, 1);//Remove o elemento do array
        }
        var token = "{{csrf_token()}}";
        formData.append('notes',JSON.stringify(checkedDates))
        //Requisição para actualização do ficheiro de teste no backend
        $.ajax({
            url:"{{ route('calendar.store') }}",
            dataType: 'json',
            method: 'POST',

            headers: {
                'X-CSRF-Token': token,
            },
            data: formData,
            beforeSend: function () {
                // showLoader("A processar o pedido...");
            },
            complete: function () {
                // hideLoader();
            },
            success: function (data) {
                if (parseInt(data.status) == 201) {

                    if (eventCalendar) {
                        // Se o evento existir
                        eventCalendar.remove();
                    }

                    $('#dismiss_modal').click();
                    Toastify({
                        text: 'Removido com sucesso',
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #0d6efd, #83abe5)",
                        }
                    }).showToast();
                } else {
                    $('#dismiss_modal').click();
                    Toastify({
                        text: data.message,
                        className: "info",
                        style: {
                            background: "linear-gradient(to right,#ed0734, #df6d83)",
                        }
                    }).showToast();

                }
                return false;
            },
            error: function (error) {
                console.log(error.responseJSON);
                // $('#info_error_modal').text(error.responseJSON.message)
                // $('#submit_form_errorModal').click();
                $('#dismiss_modal').click();
                Toastify({
                    text: error.responseJSON.message,
                    className: "info",
                    style: {
                        background: "linear-gradient(to right,#ed0734, #df6d83)",
                    }
                }).showToast();
                return false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
</script>

</html>

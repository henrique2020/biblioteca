<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="mensagem-aviso"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    function emiteAviso(message, type = 'danger') {
        let div = $('#liveToast');
        let body = $('#mensagem-aviso');

        div.attr('class', `toast align-items-center text-white bg-${type} border-0`);
        body.text(message);
        
        let toast = bootstrap.Toast.getOrCreateInstance(div);
        toast.show();
    }

    function logout() {
        $.ajax({
            url: '/api/logout',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            complete: function() { 
                location.reload();
            }
        });
    }
</script>
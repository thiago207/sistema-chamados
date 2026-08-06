<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
    @if(session('sucesso'))
        <div class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('sucesso') }}</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    @endif

    @if(session('erro'))
        <div class="toast text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('erro') }}</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    @endif
</div>

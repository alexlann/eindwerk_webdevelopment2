@if (session('status'))
    <div class="fixed p-3 bottom-0 right-0 bg-green text-white" id="status">
        {{ session('status') }}
    </div>
@endif
@if (session('status-warning'))
    <div class="fixed p-3 bottom-0 right-0 bg-red text-white" id="status-warning">
        {{ session('status-warning') }}
    </div>
@endif

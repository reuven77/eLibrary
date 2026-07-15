@if (session('success') || session('error'))
    <div class="rb-container space-y-3 pt-6">
        @if (session('success'))
            <div class="rb-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rb-flash-error" role="alert">{{ session('error') }}</div>
        @endif
    </div>
@endif

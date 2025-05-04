<div>
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @php
        $errorsBag = session('errors') ? session('errors')->getBag('default') : null;
    @endphp

    @if ($errorsBag && $errorsBag->any())
        <ul>
            @foreach ($errorsBag->messages() as $field => $messages)
                @if (str_starts_with($field, 'items'))
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                        @foreach ($messages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </ul>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
</div>

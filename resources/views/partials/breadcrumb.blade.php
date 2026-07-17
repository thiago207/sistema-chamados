@if(!empty($items))
    <ul class="breadcrumb-nav">
        @foreach($items as $item)
            <li>
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    {{ $item['label'] }}
                @endif
            </li>
        @endforeach
    </ul>
@endif

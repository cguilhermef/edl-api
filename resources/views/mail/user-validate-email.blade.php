<h2>{{ $user->name }}</h2>

<a href="{{ route('user.activate', ["token" => $user->valid_token]) }}" target="_blank">
    {{ route('user.activate', ["token" => $user->valid_token]) }}
</a>

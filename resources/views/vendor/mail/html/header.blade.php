<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'waza')
<img src="https://dashboard.waza.fun/assets/images/logo-waza-colored.svg" class="logo" alt="waza">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>

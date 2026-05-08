@foreach ($clicks as $click)
    <tr>
        <td>{{ $click->id }}</td>
        <td>{{ $click->clicker_ip }}</td>
        <td>{{ $click->region }}</td>
        <td>{{ $click->country }}</td>
        <td>{{ $click->created_at->format('Y-m-d H:i:s') }}</td>
    </tr>
@endforeach

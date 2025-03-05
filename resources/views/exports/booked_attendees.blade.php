<!DOCTYPE html>
<html>
<head>
    <title>Booked Attendees</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <table>
        <caption style="caption-side:top;text-align:left;border:none; margin-bottom: 5px;">
            Event: {{ $event['name'] }} <br>
            Location: {{ $event['location']['name'] }} <br>
            Start Date: {{ $event['start_time'] }} <br>
            End Date: {{ $event['end_time'] }}
        </caption>
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Company</th>
                <th>Attendee Type</th>
                <th>Table</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tables as $table)
                @foreach($table['attendees'] as $attendee)
                    <tr>
                        <td>{{ $attendee['id'] }}</td>
                        <td>{{ $attendee['name'] }}</td>
                        <td>{{ $attendee['company']['name'] }}</td>
                        <td>{{ substr($attendee['attendee_group']['name'], 0, -1) }}</td>
                        <td>{{ $table['name'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>

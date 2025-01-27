<!DOCTYPE html>
<html lang="en">
<head>
  <title>Registered Attendees</title>
  <style> 
    body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; } 
    th, td { border: 1px solid black; padding: 8px; text-align: left; } 
    th { background-color: #f2f2f2; } 
  </style>
</head>
<body>
    <div class="container mt-5">
        <table class="table table-striped table-bordered">
            <caption style="caption-side:top;text-align:left;border:none; margin-bottom: 5px;">
                Event: {{ $event['name'] }} <br>
                Location: {{ $event['location']['name'] }} <br>
                Start Date: {{ $event['start_time'] }} <br>
                End Date: {{ $event['end_time'] }}
            </caption>
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Company</th>
                    <th scope="col">Attendee Type</th>
                    <th scope="col">Confirmed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendees as $attendee)
                    <tr>
                        <td>{{ $attendee['id'] }}</td>
                        <td>{{ $attendee['name'] }}</td>
                        <td>{{ $attendee['company']['name'] }}</td>
                        <td>{{ substr($attendee['attendee_group']['name'], 0, -1) }}</td>
                        <td>
                            @if (!empty($attendee['events']) && isset($attendee['events'][0]['pivot']['confirmed']))
                                {{ $attendee['events'][0]['pivot']['confirmed'] == 1 ? 'Yes' : 'No' }}
                            @else
                                No
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>


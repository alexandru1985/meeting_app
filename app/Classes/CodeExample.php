<?php

$location = Location::find(1);
$events = $location->events;
$tables = $location->tables;

attendees
    id - integer
    name - string

events
    id - integer
    name - string

attendee_event
    attendee_id - integer
    event_id - integer
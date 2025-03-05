<?php

$location = Location::find(1);
$events = $location->events;
$tables = $location->tables;
<?php

$timezone_offset_minutes = $_GET['offset'];

$timezone_name = timezone_name_from_abbr("", $timezone_offset_minutes * 60, false);

echo 'Your current timezone is ' . $timezone_name;
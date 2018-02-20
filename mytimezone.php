<?php
if (isset($_GET['offset'])) {
    echo 'Your current timezone is ' . timezone_name_from_abbr('', $_GET['offset'] * 60, false);
    exit;
}
?>

<html>
<head>
    <title>MY TIMEZONE</title>
</head>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var timezone_offset_minutes = new Date().getTimezoneOffset();
        timezone_offset_minutes = timezone_offset_minutes === 0 ? 0 : -timezone_offset_minutes;
        $.ajax({
            type: 'GET',
            url: window.location.href + '?offset=' + timezone_offset_minutes,
            success: function (response) {
                $('#timezone').html(response);
            },
            error: function (e) {
                alert('fail');
                console.log(e.responseText);
            }
        });
    });

</script>
<body>
<div id="timezone"></div>
</body>
</html>
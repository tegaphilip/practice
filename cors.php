<html>
    <head>
        <title>CORS</title>
    </head>

<script src="script/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
       $('#click').on('click', function () {
           $.ajax({
               type: 'GET',
//               url: 'https://apidev.hocaboo.com/v1.0/api/jobs/documents/zip?matching_id=38',
               url: 'http://docker.api.hocaboo:8080/v1.0/api/b2b/jobs/documents/zip?matching_id=38',
//               url: 'http://apidev.hocaboo.com/v1.0/api/b2b/hotel/team?hotel_id=1',
//               url: 'http://139.59.148.137/files/api/v1.0/documents/YA5I9MCXFWHUBTSI0R828Y1478688442',
               headers: {
                   'API-KEY':'b2792f49d1f03c93886775be1e28919d9f750cea8667e808d63fc8a681911ddf',
                   'LANG':'en',
                   'TOKEN' : 'isn5ec596M0Qm6i0D5pG3h82Y2e5091aHla2G48040Q55vOv23kVuF30S4Y1atj1',
                   'HASH' : '4P6wuE6nqmTr7Md9iF10XrtMEM0v1l32',
                   'NEIGHBORHOOD' : 'isn5ec596M0Qm6i0D5pG3h82Y2e5091aHla2G48040Q55vOv23kVuF30S4Y1atj1',
                   'VERSION' : '2.4.3',
               },
               data: {test: true},
               dataType: 'json',
               success: function(response) {
//                   alert(JSON.parse(response));
                   alert(response);
                   console.log(response);
                   if (response.status === false) {
                       alert(response.message);
                   }
               },
               error: function(e) {
                   alert('fail');
                   console.log(e.responseText);
               }
           });
       });
    });

</script>
<body>
    <button id="click">Click me</button>
</body>
</html>
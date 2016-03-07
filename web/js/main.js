
$( document ).ready(function() {

    var slug = window.location.href.split("/").pop();

    function getData(callback) {
        $.getJSON("ando-antoine/json", function (data) {
            callback(data);
        });
    }
    getData(function(results) {
        console.log(results);
        results = JSON.parse(results);
        console.log(results);
        $('#calendar').fullCalendar({
            events: results
        });

        $(".fc-event-container").click(function(e){
        });

    });

});
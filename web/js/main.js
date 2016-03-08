/*

$( document ).ready(function() {

    var slug = window.location.href.split("/").pop();

    console.log(slug);

    function getData(callback) {
        $.getJSON("ando-antoine/json", function (data) {
            console.log(data);
            callback(data);
        });
    }
    getData(function(results) {
        console.log(results);
        results = JSON.parse(results);
        console.log(results);
        $('#calendar').fullCalendar({
            events: results,
            eventClick: function(event) {
                var date = new Date(event.start);
                date = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay();
        }
        });

        $(".fc-event-container").click(function(e){
        });

    });

});
*/
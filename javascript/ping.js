/**
 * Ping utility
 */
(function ($) {
    $(function () {
        var interval;
        var PingIntervalSeconds = 5 * 60;
        var onSessionLost = function (xmlhttp, status) {
            if (xmlhttp.status > 400 || xmlhttp.responseText == 0) {
                stopTimer();
                if (window.open('Security/login')) {
                    alert('Please log in and then try again');
                } else {
                    var res = confirm('Please enable pop-ups for this site');
                    if (res) {
                        if (window.open('Security/login')) {
                            alert('Please log in and then try again');
                        }
                    }
                }
            }
        };

        function initTimer() {
            interval = setInterval(function () {
                $.ajax({
                    url: 'Security/ping',
                    global: false,
                    type: 'POST',
                    complete: onSessionLost
                });
            }, PingIntervalSeconds * 1000);
        }
        initTimer();

        function stopTimer() {
            clearInterval(interval);
        }
    });
})(jQuery);
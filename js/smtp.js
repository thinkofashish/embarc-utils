$(window).load(init);

function init() {
    switch (window.eu.id) {
        case "smtp_check":
            smtp_check.initialize();
            break;
    }
}

var smtp_check = {
    initialize: function () {
        var self = this;

        $("#hideConsole").on("click", function () {
            $("#console").slideUp();
        });

        $("#clearConsole").on("click", function () {
            $("#log").html("");

            // update number of attempts
            self.attempts = 0;
            $("#attempts").text(self.attempts);
        });

        // set validation
        $("#smtpForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                // button in loading state
                $("#receiveMailButton").button('loading');

                self.receive(function (log) {
                    // show the console
                    $("#console").slideDown();

                    // reset button
                    $("#receiveMailButton").button('reset');

                    // print log in console
                    $("#log").append("<p>" + log + "</p>");

                    // update number of attempts
                    $("#attempts").text(++self.attempts);
                });

                return false;
            }
        });
    },

    // receive an email
    receive: function (receiveCallback) {
        var self = this,
            jsdata = createObject(["smtpForm"]);

        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=smtp&fx=sendMail",
            data: jsdata,
            success: function (data, textStatus, jqXHR) {
                if (receiveCallback) receiveCallback.apply(self, [data]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to do receive mail " + errorThrown);
            }
        });
    },

    attempts: 0
};

/**
* Contributions
*
*
* jQuery
* URL: http://jquery.com/
* GitHub: https://github.com/jquery/jquery
*
* Bootstrap
* URL: http://getbootstrap.com/
* GitHub: https://github.com/twbs/bootstrap/
*
* jQuery.validate
* URL: http://jqueryvalidation.org/
* GitHub: https://github.com/jzaefferer/jquery-validation
*/
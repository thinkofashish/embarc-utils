$(window).load(init);

function init() {
    switch (window.eu.id) {
        case "login_home":
            login_home.initialize();
            break;
    }
}

var login_home = {
    initialize: function () {
        var self = this,
            username = getCookieValue("username");

        // if username is found in cookies
        if (username) $("#username").val(username);

        // set appropriate focus
        if (!$("#username").val()) {
            $("#username").focus();
        } else {
            $("#hash").focus();
        }

        // set validation
        $("#loginForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                // hide error messages
                $("#errorMessage-1").slideUp();

                // submit form - All is well!
                self.send();

                return false;
            }
        });
    },

    // send login request
    send: function (receiveCallback) {
        var self = this,
            jsdata = createObject(["loginForm"]);
        jsdata.hash = CryptoJS.SHA256(jsdata.hash).toString(CryptoJS.enc.Hex);

        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=login&fx=login",
            data: jsdata,
            success: function (result, textStatus, jqXHR) {
                if (strncmp(result, "success", 7)) {
                    window.location = "/embarc-utils/dashboard.php";
                } else {
                    switch (result) {
                        case "in":
                            $("#errorMessage-1").slideDown();
                            break;

                        default:
                            console.log("Unknown error occured while login.");
                            break;
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to login " + errorThrown);
            }
        });
    }
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
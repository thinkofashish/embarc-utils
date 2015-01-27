$(window).load(init);

function init() {
    switch (window.eu.id) {
        case "aNr_schedule":
            schedule.initialize();
            break;
    }
}

var schedule = {
    initialize: function () {
        var self = this;

        // fetch details of modules, alerts and reminders
        this.getMAR(function (modulesList) {
            // fill up alerts and reminders
            self._fillAlertsAndReminders(modulesList);            

            // fetch existing schedule information
            self.fetch(function (data) {
                self._onScheduleFetch(data);
            });
        });

        $("#alerts,#reminders").select2();

        // form validation and submit handler
        $("#scheduleForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                // method to submit this form
                self.save();

                return false;
            },
            invalidHandler: function (ev, validator) {
                if (validator.numberOfInvalids()) {
                    $("#errorMessage-1").show();
                } else {
                    $("#errorMessage-1").hide();
                }
            }
        });
    },

    // fill up list of alerts and reminders
    _fillAlertsAndReminders: function (mar) {
        var $alerts = $("#alerts"),
            $reminders = $("#reminders"),
            $alertOptgroup,
            $reminderOptgroup;

        $.each(mar, function (key, value) {
            if (value.alerts.length > 0) {
                $alertOptgroup = createElement("<optgroup/>", $alerts, { 'label': value.name });
                fillDropDown($alertOptgroup, value.alerts, "name", "id");
            }

            if (value.reminders.length > 0) {
                $reminderOptgroup = createElement("<optgroup/>", $reminders, { 'label': value.name });
                fillDropDown($reminderOptgroup, value.reminders, "name", "id");
            }
        });
    },

    // method to execute when a schedule is fetched
    _onScheduleFetch: function (scheduleDetails) {
        this.id = scheduleDetails['id'];

        scheduleDetails.alerts = this.getCSV_fromBinDec(scheduleDetails.alerts);
        scheduleDetails.reminders = this.getCSV_fromBinDec(scheduleDetails.reminders);

        $.each(scheduleDetails, function (key, value) {
            switch(key) {
                case "alerts":
                    $("#alerts").select2("val", value.split(","));
                    break;

                case "reminders":
                    $("#reminders").select2("val", value.split(","));
                    break;

                default:
                    key = $("#" + key);

                    if (key.is("input[type='checkbox']")) {
                        key.prop("checked", +value);
                    } else {
                        key.val(value);
                    }
                    break;
            }
        });
    },

    id: null,

    // get a list of modules with their respective alerts and reminders
    getMAR: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=anr&fx=getMAR",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid list of alerts & reminders received from server");
                    return;
                }

                if (callback) callback.apply(self, [data]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to fetch list of modules, alerts and reminders " + errorThrown);
            }
        });
    },

    // fetch existing schedule for current user
    fetch: function (fetchCallback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=anr&fx=get",
            success: function (data, textStatus, jqXHR) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid schedule received from server");
                    return;
                }

                if (fetchCallback) fetchCallback.apply(self, [data]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to fetch schedule from server " + errorThrown);
            }
        });

    },

    // save a schedule
    save: function () {
        // hide error & success messages
        $("#successMessage-1").hide();
        $("#errorMessage-1").hide();

        var jsdata = createObject(["scheduleForm"]);
        
        jsdata.alerts = this.getBinDec_fromCSV(jsdata.alerts);
        jsdata.reminders = this.getBinDec_fromCSV(jsdata.reminders);
        
        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=anr&fx=save" + (this.id?("&id=" + this.id): ""),
            success: function (data, textStatus, jqXHR) {
                if (data == "SUCCESS") {
                    $("#successMessage-1").show();
                } else {
                    $("#errorMessage-1").show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to save schedule " + errorThrown);
            }
        });

    },

    // convert a list of comma seperated values to binary number
    getBinDec_fromCSV: function (csvstr) {
        var temp = 0,
            i = 0;

        csvstr = csvstr.split(",");
        for (; i < csvstr.length; i++) {
            temp |= +csvstr[i];
        }

        return temp;
    },

    // convert a binary number to a list of comma separated values
    getCSV_fromBinDec: function (number) {
        number = parseInt(number, 10);

        var temp = [],
            c = 1;

        while (number) {
            if (number & 1) temp.push(c);
            number = number >> 1;
            c++;
        }

        return temp.join(",");
    },
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
* select2.js
* URL: http://ivaynberg.github.io/select2/
* GitHub: https://github.com/ivaynberg/select2
*
* jQuery.validate
* URL: http://jqueryvalidation.org/
* GitHub: https://github.com/jzaefferer/jquery-validation
*/
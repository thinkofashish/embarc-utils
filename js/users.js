$(window).load(init);

function init() {
    switch (location.pathname) {
        case "/embarc-utils/users/users_add.php":
            user_add.initialize();
            break;

        case "/embarc-utils/users/users_list.php":
            user_list.initialize();
            break;
    }
}

var user_list = {
    initialize: function () {
        var self = this;

        user_add.getModules(function (modules) {
            self.modules = createMapFromArray(modules, "id");
            
            // fetch list of users - after modules have been fetched
            self.fetch(self.fill);
        });
    },

    modules: null,

    // fill up list of users
    fill: function (listOfUsers) {
        for (var i = 0; i < listOfUsers.length; i++) {
            this.add(listOfUsers[i]);
        }
    },

    // fetch list of users from server
    fetch: function (fetchCallback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=user&fx=list",
            success: function (data, textStatus, jqXHR) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid list of users received form server.");
                    return;
                }

                if (fetchCallback) fetchCallback.apply(self, [data]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to fetch users " + errorThrown);
            }
        });
    },

    // add a user row
    add: function (userDetails) {
        $("#usersList").prepend(this.makeRow(userDetails, this.edit, this.remove));
    },

    // edit a user
    edit: function (userDetails) {
        window.location = "users_add.php?edit=1&username=" + encodeURIComponent(userDetails.username);
    },

    // delete a user - permanently
    remove: function (userDetails, $userRow) {
        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=user&fx=remove&username=" + userDetails.username,
            success: function (data, textStatus, jqXHR) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("user was not removed from server.");
                    return;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to delete user " + errorThrown);
            }
        });
    },

    // get initials of each module, from a list of comma seperated modules
    getModulesInitials: function (modules_ids) {
        if (!modules_ids) return "";

        modules_ids = modules_ids.split(",");

        var modulesInitials = [];
        for (var i = 0, l = modules_ids.length; i < l; i++) {
            if (!modulesInitials[i]) modulesInitials[i] = "";
            var module = this.modules[modules_ids[i]].name.split(" ");

            for (var j = 0; j < module.length; j++) modulesInitials[i] += module[j].substr(0, 1);

            modulesInitials[i] = "<span class='label label-" + this.labelColors[Math.floor(Math.random() * 6)] + "' title='" + this.modules[modules_ids[i]].name + "'>" + modulesInitials[i] + "</span>";
        }

        return modulesInitials.join("&nbsp;");
    },

    labelColors: ["primary", "success", "warning", "danger", "info", "default"],

    // make a row of user
    makeRow: function (userDetails, editCallback, removeCallback) {
        var self = this;

        var $row = createElement("<tr/>", null, { });

        createElement("<td/>", $row, { 'html': userDetails.name });
        createElement("<td/>", $row, { 'html': userDetails.username });
        createElement("<td/>", $row, { 'html': this.getModulesInitials(userDetails.modules) });
        createElement("<td/>", $row, { 'html': userDetails.email });
        createElement("<td/>", $row, { 'html': userDetails.phone });        
        createElement("<td/>", $row, { 'html': userDetails.dob });
        var $editButton = createElement("<i/>", null, { 'class': "fa fa-pencil-square-o", 'style': "cursor: pointer;", 'title': "Edit" }).appendTo(createElement("<td/>", $row, null));
        $editButton.on("click", function () {
            if (editCallback) editCallback.apply(self, [userDetails, $row]);
        });

        var $removeButton = createElement("<i/>", null, { 'class': "fa fa-times", 'style': "color: red; cursor: pointer;", 'title': "Remove" }).appendTo(createElement("<td/>", $row, null));
        $removeButton.on("click", function () {
            if (!confirm("Are you sure you want to remove this user? Warning: a user once removed will be deleted permanently.")) return;

            // remove this row
            $row.remove();

            // call the callback function
            if (removeCallback) removeCallback.apply(self, [userDetails, $row]);
        });

        return $row;
    }
};

var user_add = {
    initialize: function () {
        var self = this;

        //initialize datepicker
        $('.datepicker').datepicker({
            'autoclose': true,
            'format': "dd/mm/yyyy",
            'startView': 2
        });

        // fill up list of modules
        this.getModules(function (modules) {
            fillDropDown2("#modules", modules, "name", "id");

            /******************MODULES FETCHED - proceed******************/

            //check for edit mode
            if (getURLParameter("edit") == "1") {

                // get details of this user and fill it up
                this.get(getURLParameter("username"), this.fill);
            }
        });

        $("#modules").select2({
            'placeholder': "select a module"
        });
        
        //validate and submit form when done
        $("#userAddForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveUserButton").button('loading');
                
                //call method to submit this form
                self.save();

                //reset state of submit button
                $("#saveUserButton").button('reset');

                //focus on serial number to start again quickly
                $("#name").focus();

                return false;
            }
        });
    },

    //get details of a server of specified ID
    get: function (username, callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=user&fx=get&username=" + username,
            success: function (data) {
                try {
                    data = getJSONFromString(data)[0];
                } catch (ex) {
                    console.log("unable to get data");
                    return;
                }

                if (callback) callback.apply(self, [data]);
            }
        });
    },

    //fill server details
    fill: function (details) {
        $.each(details, function (key, value) {
            switch (key) {
                case "dob":
                    value = getDisplayDate(value);
                    $("#" + key).val(value);
                    break;

                case "modules":                    
                    $("#modules").select2("val", value.split(","));
                    break;

                default:
                    $("#" + key).val(value);
                    break;
            }
        });
    },

    // save this user
    save: function () {
        $("#errorMessage-1").hide();
        $("#errorMessage-2").hide();
        $("#successMessage-1").hide();

        var jsdata = createObject(["userAddForm"]);
        jsdata.dob = getFormattedDate(jsdata.dob);
        
        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=user&fx=add" + (getURLParameter("edit") == "1" ? "&username=" + jsdata.username : ""),
            success: function (result) {
                if (result == "SUCCESS") {
                    $("#successMessage-1").show();

                    window.setTimeout(function () {
                        $("#successMessage-1").hide();
                    }, 8000);
                } else if (result == "EXISTS") {
                    $("#errorMessage-2").show();
                } else {
                    $("#errorMessage-1").show();
                }
            }
        });
    },

    // get list of modules
    getModules: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=misc&fx=listModules",
            success: function (data) {
                if (strncmp(data, "ERROR", 5)) {
                    alert("No modules allowed");
                } else {
                    data = getJSONFromString(data);
                    callback.apply(self, [data]);
                }
            }
        });
    }
};
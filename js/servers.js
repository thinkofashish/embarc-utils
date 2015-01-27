$(window).load(init);

function init() {
    switch (location.pathname) {
        case "/embarc-utils/servers/server_add.php":
            root_password_AES.getPreferences();
            server_add.initialize();
            break;

        case "/embarc-utils/servers/server_list.php":
            root_password_AES.getPreferences();
            server_list.initialize();
            break;

        case "/embarc-utils/servers/server_pref.php":
            server_pref.initialize();
            break;

        case "/embarc-utils/servers/server_datacenter.php":
            server_hosting_locations.initialize();
            break;
    }
}

var server_hosting_locations = {

    initialize: function () {
        var self = this;

        // focus on name to start quickly
        $("#name").focus();

        //remove independent 'required' messages
        jQuery.validator.messages.required = "";

        // validate and submit form when done
        $("#datacentresForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                // call method to submit this form
                self.save();

                // clear the form
                form.reset();

                // focus on name to start again quickly
                $("#name").focus();

                return false;
            },
            invalidHandler: function (ev, validator) {
                if (validator.numberOfInvalids()) {
                    $("#errorMessage-2").show();
                } else {
                    $("#errorMessage-2").hide();
                }
            }
        });
    },

    // save a new hosting location
    save: function () {
        $("#errorMessage-1").hide();
        $("#successMessage-1").hide();

        var jsdata = createObject(["datacentresForm"]);

        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=servers&fx=addDatacentre",
            success: function (result) {
                if (result == "SUCCESS") {
                    $("#successMessage-1").show();

                    window.setTimeout(function () {
                        $("#successMessage-1").hide();
                    }, 10000);
                } else {
                    $("#errorMessage-1").show();
                }
            }
        });
    },

    // get hosting locations
    get: function (callback) {
        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=servers&fx=getDatacentres",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch(ex) {
                    console.log("no hosting locations found");
                    return;
                }

                if(callback) callback.apply(self, [data]);
            }
        });
    }
};

var server_log_regexp = {
    "frontend.log": /Initialization Complete|Client is Authenticated Successfully/,
    //"mailer.log": "connecting to the server",
    //"geofence.log": "successful",
    //"udp_server.log": "ops server has been started",
    //"rfid.log": "server is running now"
};

var server_add = {
    initialize: function () {
        var self = this;

        //focus on company to start quickly
        $("#company").focus();

        //fill list of countries
        fillDropDown2("#country", countriesMap, "name", null);

        //fill hosting locations
        server_hosting_locations.get(function (h_locations) {
            fillDropDown("#hosted_at", h_locations, "name", "id");
        });
        

        //validate and submit form when done
        $("#serverAddForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveServerButton").button('loading');

                //call method to submit this form
                self.save();

                //reset state of submit button
                $("#saveServerButton").button('reset');

                //focus on company to start again quickly
                $("#company").focus();

                return false;
            }
        });

        //check for edit mode
        if (getURLParameter("edit") == "1") {
            this.id = getURLParameter("id");

            this.get(this.fill);
        } else {
            root_password_AES.prompt();

            // fill up preferences
            server_pref.get(function (preferences) {
                self.preferences = preferences;

                // fill up the preferences
                $.each(preferences, function (key, value) {
                    $("#" + key).val(value);
                });
            });
        }

    },

    id: null,

    preferences: null,

    //get details of a server of specified ID
    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=servers&fx=get&id=" + this.id,
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
            if (key == "root_password") { // decrypt password here
                value = root_password_AES.decrypt(value);
            }

            $("#" + key).val(value);
        });
    },

    //save a new server
    save: function () {
        //hide error message if shown already
        $("#errorMessage-1").hide();

        var jsdata = createObject(["serverAddForm"]);
        if (root_password_AES.key) {
            jsdata["root_password"] = root_password_AES.encrypt(jsdata["root_password"]);
        } else {
            delete jsdata["root_password"];
        }

        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=servers&fx=add" + (getURLParameter("edit") == "1"?"&id=" + this.id:""),
            success: function (result) {
                if (strncmp(result, "SUCCESS", 7)) {
                    $("#successMessage-1").show();
                    window.setTimeout(function () {
                        $("#successMessage-1").hide();
                    }, 10000);
                } else {
                    $("#errorMessage-1").show();
                }
            }
        });
    }
};

var root_password_AES = {
    preferences: null,

    getPreferences: function (callback) {
        var self = this;

        server_pref.get(function (preferences) {
            self.preferences = preferences;

            if (callback) callback.apply(self);
        });
    },

    // AES passphrase
    key: false,

    // ask user for a SECRET key (Stop looking, you won't find it here!)
    prompt: function (callback) {
        if (!this.preferences) {
            this.getPreferences();
            return;
        } else {
            // go back if password prompt is disabled
            if (this.preferences.noKey == "1") return;

            this.key = window.prompt("Enter you SECRET key");
            if (!this.key) return;

            this.verify_key();

            return this.key;
        }
    },

    // verify the key entered with the key stored on server - (yes the key in on the server, PLAINTEXT!)
    verify_key: function () {
        var self = this,
            hash = CryptoJS.SHA256(this.key).toString();

        $.ajax({
            type: "POST",
            async: false, // ha ha ha
            data: { "hash": hash },
            url: "/embarc-utils/php/main.php?util=servers&fx=checkSecret",
            success: function (result) {
                if (result != "SUCCESS") {
                    self.key = false;
                }
            }
        });
    },

    // encrypt given plaintext
    encrypt: function (plaintext) {
        if (!this.key && !this.prompt()) return;

        var ciphertext = "";
        try {
            ciphertext = CryptoJS.AES.encrypt(plaintext, this.key).toString();
        } catch (ex) {
            console.log("Unable to encrypt using the key provided");
        }
        return ciphertext;
    },

    // decrypt given ciphertext
    decrypt: function (ciphertext) {
        if (!this.key && !this.prompt()) return;

        var plaintext = "";
        try {
            plaintext = CryptoJS.enc.Utf8.stringify(CryptoJS.enc.Hex.parse(CryptoJS.AES.decrypt(ciphertext, this.key).toString()));
        } catch (ex) {
            console.log("Unable to decrypt using the key provided");
        }

        return plaintext;
    }
};

var server_list = {
    initialize: function () {
        var self = this;

        server_hosting_locations.get(function (h_locations) {
            if (h_locations) {
                for (var i = 0, l = h_locations.length; i < l; i++) {
                    self.hostingLocations[h_locations[i]["id"]] = h_locations[i];
                }
            }

            // get preferences
            server_pref.get(function (preferences) {
                self.preferences = preferences || { 'rInt': 0, 'cSort': 0 };

                //get list of servers and display
                self.get(self.printList);

                // set auto refresh if required
                if (preferences.rInt && parseInt(preferences.rInt, 10)) { // zero disables auto refresh
                    window.setInterval(function () {
                        // trigger refresh event on single server refresh buttons
                        $(".refreshBtn").trigger("srefresh");
                    }, parseInt(preferences.rInt, 10) * 60 * 1000);
                }
            });
        });

        // hide #back-top initially
        $("#back-top").hide();

        // fade in #back-top
        $(function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 100) {
                    $('#back-top').fadeIn();
                } else {
                    $('#back-top').fadeOut();
                }
            });

            // scroll body to 0px on click
            $('#back-top a').click(function () {
                $('body,html').animate({
                    scrollTop: 0
                }, 500);
                return false;
            });
        });
    },

    hostingLocations: {},

    preferences: null,

    //get list of servers
    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=servers&fx=list",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("no server available");
                    return;
                }
                
                if (callback) callback.apply(self, [data]);
            }
        });
    },

    // sort list of servers by server name
    sortListByName: function (a, b) {
        a = a["company"].toLowerCase();
        b = b["company"].toLowerCase();
        return (a < b ? -1 : (a > b ? 1 : 0));
    },

    //print list of servers
    printList: function (serversList) {
        // sort list of servers - if required
        if (+this.preferences.cSort) {
            serversList.sort(this.sortListByName);
        }

        for (var i = 0, l = serversList.length; i < l; i++) {
            this.addRow(serversList[i]);
        }
    },

    //reset a row to non-working state
    resetRow: function ($row) {
        //remove working and part working options
        $row.find(".panel-heading").removeClass("panel-heading-working");
        $row.find(".panel-heading").removeClass("panel-heading-partworking");

        //reset left status column
        $row.find(".left_status").html('<div class="col-lg-2 margin-bottom"></div>');
    },

    //update server row
    updateRow: function ($row, statusObject) {
        var totalProcesses = getObjectLength(statusObject.process || ""),
            stoppedProcesses = this.checkProceses(statusObject.process || ""),
            totalLogs = getObjectLength(statusObject.logs || ""),
            errorsomeLogs = this.checkLogs(statusObject.logs || ""),
            $left_status = $row.find(".left_status"),
            showNonWorkingProcesses = function (html) {
                createElement("<div/>", $left_status, { 'class': "col-lg-2 margin-bottom", 'html': html });
            };
        
        //remove working and part working options
        $row.find(".panel-heading").removeClass("panel-heading-working");
        $row.find(".panel-heading").removeClass("panel-heading-partworking");

        //clean left status column
        $left_status.html("");

        //if no data is received from server
        if (!statusObject) {
            //show error message
            createElement("<div/>", $left_status, { 'class': "col-lg-2 margin-bottom", 'html': "unable to connect" });
            return;
        }
        
        if (stoppedProcesses.count > 0) { // all processes are stopped
            if (stoppedProcesses.count < totalProcesses) {
                //color it amber
                $row.find(".panel-heading").addClass("panel-heading-partworking");
            }
            // else default is red

            //show non working processes
            showNonWorkingProcesses(stoppedProcesses.html);
        } else if (statusObject.updating && statusObject.updating.status == -1) { // trackers are not updating
            // default is red - no need to color

            // line deprecated - since updating interval is not always available
            //createElement("<div/>", $left_status, { 'class': "col-lg-2 margin-bottom", 'html': "no AVL data received since " + parseInt(statusObject.updating.interval, 10) / 60 + " minutes" });

            // show error message
            createElement("<div/>", $left_status, { 'class': "col-lg-2 margin-bottom", 'html': "no AVL data received since " + parseInt(this.preferences.tcInt, 10) + " minutes" });
        } else if (errorsomeLogs.count > 0) { // error is found in log files
            if (errorsomeLogs.count < totalLogs) {
                //color it amber
                $row.find(".panel-heading").addClass("panel-heading-partworking");
            }
            //else default is red
            
            //show non working processes
            showNonWorkingProcesses(errorsomeLogs.html);
        } else if (statusObject.gStats && +statusObject.gStats.rC > 250) { // reservoir count is high
            //color it amber
            $row.find(".panel-heading").addClass("panel-heading-partworking");

            // display the error
            createElement("<div/>", $left_status, { 'class': "col-lg-2 margin-bottom", 'html': '<span class="badge" title="High reservoir count">rC:' + statusObject.gStats.rC + '</span>' });
        } else if (stoppedProcesses.count == 0 && errorsomeLogs.count == 0) { // disk or memory usage is high or normal
            //calculate Disk and Memory usage
            var diskUsage = this.getPrimaryDiskUsage(statusObject.disk),
                memoryUsage = this.getPrimaryMemoryUsage(statusObject.mem);

            if (diskUsage > 80 || memoryUsage > 80) {// if disk usage or memory usage is above 80%
                //color it amber
                $row.find(".panel-heading").addClass("panel-heading-partworking");
            } else {
                //color it green
                $row.find(".panel-heading").addClass("panel-heading-working");
            }

            //show HDD and RAM status
            createElement("<div/>", $left_status, { 'class': "col-lg-1 margin-bottom", 'html': '<i class="fa fa-hdd" title="Disk Usage" style="font-size:20px;"></i><span>&nbsp;' + diskUsage + '%</span>' });
            createElement("<div/>", $left_status, { 'class': "col-lg-1 margin-bottom", 'html': '<i class="fa fa-tasks" title="Memory Usage" style="font-size:19px;"></i><span>&nbsp;' + memoryUsage + '%</span>' });
        }

        /*
        * fill addtitional details since they are available here
        */
        // fill HDD usage
        if (statusObject.disk) {
            var $hddDetailsContainer = $row.find(".hddDetailsContainer");
            $hddDetailsContainer.html(this.getDisksUsageDetails(statusObject.disk));
        }

        // fill RAM usage
        if (statusObject.mem) {
            var $ramDetailsContainer = $row.find(".ramDetailsContainer");
            $ramDetailsContainer.html(this.getMemoryUsageDetails(statusObject.mem));
        }

        // fill AVL data status
        if (statusObject.updating) {
            var $avlDetailsContainer = $row.find(".avlDetailsContainer");
            $avlDetailsContainer.html(this.getAVLResult(statusObject.updating));
        }

        // fill server logs
        if (statusObject.logs) {
            var $logFilesContainer = $row.find(".logFilesContainer");
            $logFilesContainer.html(this.getServerLogs(statusObject.logs, $row.IP));
        }

        // fill gStatistics
        if (statusObject.gStats) {
            var $gStatsContainer = $row.find(".gStatsContainer");
            $gStatsContainer.html(this.getgStats(statusObject.gStats));
        }
    },

    //create a server row
    createRow: function (details) {
        var self = this;

        var panel = createElement("<div/>", null, { 'class': "panel panel-default" });
        var panelTitle = createElement("<h4/>", null, { 'class': "panel-title" }).appendTo(createElement("<div/>", panel, { 'class': "panel-heading panel-heading-notworking" }));
        var row = createElement("<div/>", panelTitle, { 'class': "row" });

        //left status column
        createElement("<div/>", null, { 'class': "col-lg-2 margin-bottom" }).appendTo(createElement("<div/>", row, {'class': "left_status"}));

        //company name
        createElement("<div/>", row, { 'class': "col-lg-2 margin-bottom", 'html': details.company || details.contact });

        //IP address
        var $ip_add = createElement("<code/>", null, { 'html': details.ip_address }).appendTo(createElement("<div/>", row, { 'class': "col-lg-2 margin-bottom" }));
        $ip_add.on("click", function () {
            //since only IE allows copy to clipboard automatically, due to security concrens we force user to manually copy password (but quickly)
            window.prompt("Press Ctrl+C followed by Enter", details.ip_address);

            //for flash based copy method visit: https://github.com/zeroclipboard/zeroclipboard
        });

        //root password
        var $root_pass = createElement("<code/>", null, { 'html': "copy password" }).appendTo(createElement("<div/>", row, { 'class': "col-lg-2 margin-bottom" }));
        $root_pass.on("click", function () {
            //since only IE allows copy to clipboard automatically, due to security concrens we force user to manually copy password (but quickly)
            var rootPassword = root_password_AES.decrypt(details.root_password);
            if (rootPassword) window.prompt("Press Ctrl+C followed by Enter", rootPassword);
            else alert("You are not authorized to access this area. Go away!");

            //for flash based copy method visit: https://github.com/zeroclipboard/zeroclipboard
        });

        //URL
        createElement("<div/>", row, { 'class': "col-lg-3 margin-bottom", 'html': '<a href="http://' + details.url + '" target="_blank">' + details.url + '</a>' });

        //collapse button
        var buttonsContainer = createElement("<div/>", row, { 'class': "col-lg-1 margin-bottom" });
        createElement("<button/>", buttonsContainer, { 'type': "button", 'class': "close", 'aria-hidden': "true", 'data-toggle': "collapse", 'data-target': "#col" + details.id, 'html': '<i class="fa fa-chevron-circle-down icon-size"></i>' });

        //refresh button
        var $refreshButton = createElement("<button/>", buttonsContainer, { 'type': "button", 'class': "close refreshBtn", 'aria-hidden': "true", 'html': '<i class="fa fa-refresh icon-size"></i>&nbsp;' });
        $refreshButton.on("click srefresh", function () {
            self.resetRow(panel);
            self.getServerStatus(panel, details.ip_address, self.updateRow);
        });

        var panelCollapse = createElement("<div/>", panel, { 'class': "panel-collapse collapse", 'id': "col" + details.id });

        var hddDetailed = '<div class="col-lg-4 col-sm-4">\
        	<div class="well well-sm">\
            	<ul class="list-group">\
                  <li class="list-group-item active"><i class="fa fa-hdd" title="Disks Usage" style="font-size:20px;"></i><span>&nbsp;&nbsp;HDD</span></li>\
                    <div class="hddDetailsContainer"></div>\
                  <li class="list-group-item active"><i class="fa fa-tasks" title="Memory Usage" style="font-size:20px;"></i><span>&nbsp;&nbsp;RAM</span></li>\
                    <div class="ramDetailsContainer"></div>\
                  <li class="list-group-item active"><i class="fa fa-ambulance" title="AVL Data" style="font-size:20px;"></i><span>&nbsp;&nbsp;Trackers</span></li>\
                    <div class="avlDetailsContainer"></div>\
                  <li class="list-group-item active"><i class="fa fa-bar-chart-o" title="gStatistics" style="font-size:20px;"></i><span>&nbsp;&nbsp;udp Statistics</span></li>\
                    <div class="gStatsContainer"></div>\
                </ul>\
            </div>\
        </div>';

        var logFilesDetailed = '<div class="col-lg-4 col-sm-4">\
        	<div class="well well-sm">\
            	<ul class="list-group">\
                  <li class="list-group-item active"><i class="fa fa-file-text-o" title="Server Logs" style="font-size:20px;"></i><span>&nbsp;&nbsp;Log Files</span></li>\
                    <div class="logFilesContainer"></div>\
                </ul>\
            </div>\
        </div>';

        var serverDetails = '<div class="col-lg-4 col-sm-4">\
        	<div class="well well-sm">\
            <ul class="list-group">\
                <li class="list-group-item active"><i class="fa fa-info" title="Server Details" style="font-size:20px;"></i><span>&nbsp;&nbsp;Information</span></li>\
                <div class="informationContainer">' +
                this.getServerDetails(details) +
                '</div>\
            </ul>\
            </div>\
        </div>';

        var panelBody = createElement("<div/>", panelCollapse, { 'class': "panel-body" });
        createElement("<div/>", panelBody, { 'class': "row", 'html': hddDetailed + logFilesDetailed + serverDetails });
        
        var buttonsContainer = createElement("<div/>", null, { 'class': "col-lg-4" }).appendTo(createElement("<div/>", panelBody, { 'class': "row" }));

        //edit button
        createElement("<a/>", buttonsContainer, { 'class': "btn btn-default", 'html': "Edit", 'target': "_blank", 'href': "server_add.php?edit=1&id=" + details.id });
        
        // delete button - if required
        if (+this.preferences.showDel) {
            var $deleteButton = createElement("<button/>", buttonsContainer, { 'type': "button", 'class': "btn btn-danger margin-left", 'html': "Delete" });
            $deleteButton.on("click", function () {
                self.deleteServer(details.id, details.ip_address, panel);
            });
        }
        
        return panel;
    },

    //delete a server
    deleteServer: function (id, ip, $row) {
        var confirm_ip = window.prompt("Are you ABSOLUTELY sure?\nThis action CANNOT be undone.\n\nPlease type in the IP address of this server to confirm.");
        if (confirm_ip != ip) {
            return;
        }

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=servers&fx=delete&id=" + id,
            success: function (result) {
                if (result == "SUCCESS") {
                    $row.remove();
                } else {
                    alert("Unable to delete server");
                }
            }
        });
    },

    //add a server row
    addRow: function (details) {
        var $row = this.createRow(details);
        $("#workingServersList").append($row);
        if (!(+this.preferences.noStatus)) {
            this.getServerStatus($row, details.ip_address, this.updateRow);
        }
    },

    //check if all processes are working or not
    checkProceses: function (proc) {
        var result = { count: 0, html: "" };

        $.each(proc, function (key, value) {
            if (value == -1) {
                result.count++;
                result.html += '<span class="badge" title="' + key + '">' + key.substr(0, 1) + '</span> ';
            }
        });

        return result;
    },

    //check if all log files have correct data
    checkLogs: function (logs) {
        var result = { count: 0, html: "" };

        $.each(logs, function (key, value) {
            if (!value) value = "";
            if (server_log_regexp[key] && (value.toLowerCase().match(server_log_regexp[key]) < 0)) {
                result.count++;
                result.html += '<span class="badge" title="' + key + '">' + key.substr(0, 1) + key.substr(key.indexOf("."), 2) + '</span> ';
            }
        });

        return result;
    },

    // get formatted AVL data status
    getAVLResult: function (avl) {
        return '<li class="list-group-item"><span class="badge" title="Total Trackers">' + avl.count + '</span>AVL data was' + (avl.status == -1 ? " <b>NOT</b> " : " ") + 'received in last ' + parseInt(avl.interval, 10) / 60 + ' minutes</li>';
    },

    //get formatted usage of memory
    getMemoryUsageDetails: function (mems) {
        if (Array.isArray(mems)) {
            for (var i = 0, l = mems.length; i < l; i++) {
                if (mems[i][0] == "-/+") {//match -/+
                    return '<li class="list-group-item"><span class="badge" title="of ' + (+mems[i][2] + +mems[i][3]) + ' MB">' + Math.floor((+mems[i][2] / (+mems[i][2] + +mems[i][3])) * 100) + '%</span>RAM</li>';
                }
            }
        }
    },

    //get main memory usage
    getPrimaryMemoryUsage: function (mems) {
        if (Array.isArray(mems)) {
            for (var i = 0, l = mems.length; i < l; i++) {
                if (mems[i][0] == "-/+") {//match -/+
                    return Math.floor((+mems[i][2] / (+mems[i][2] + +mems[i][3])) * 100);
                }
            }
        }

        return "∞";
    },

    //get formatted usage of disks
    getDisksUsageDetails: function (disks) {
        var html = "";
        
        if (Array.isArray(disks)) {
            for (var i = 0, l = disks.length; i < l; i++) {
                html += '<li class="list-group-item"><span class="badge" title="of ' + parseInt(disks[i][1], 10) + ' MB">' + parseInt(disks[i][4], 10) + '%</span>' + disks[i][5] + '</li>';
            }
        }

        return html;
    },

    //get usage of /root disk
    getPrimaryDiskUsage: function (disks) {
        if (Array.isArray(disks)) {
            for (var i = 0, l = disks.length; i < l; i++) {
                if (disks[i][5] == "/") {//check for root partition
                    //return usage percent
                    return parseInt(disks[i][4], 10);
                }
            }
        }

        return "∞";
    },

    //get formatted server logs
    getServerLogs: function (logs, ip) {
        var html = "";

        if (logs) {
            $.each(logs, function (key, value) {
                html += '<li class="list-group-item"><a href="http://' + ip + '/status/status.php?fp=' + key + '" target="_blank">' + key + '</a>&nbsp;' + value + '</li>';
            });
        }

        return html;
    },

    // get formatted statistics of UDP server - gStatistics
    getgStats: function (gStats) {
        var html = "";

        $.each(gStats, function (key, value) {
            if (key == "cT") return;

            switch (key) {
                case "tpR":
                    html += '<li class="list-group-item"><span class="badge">' + value + '</span>' + "TCP Packets";
                    break;

                case "upR":
                    html += '<li class="list-group-item"><span class="badge">' + value + '</span>' + "UDP Packets";
                    break;

                case "pI":
                    html += '<li class="list-group-item"><span class="badge">' + value + '</span>' + "Packets Inserted";
                    break;

                case "rC":
                    var color = "#5CB85C"; // success
                    if (value > 250 && value < 500) { // warning
                        color = "#F0AD4E";
                    } else if(value > 500) { // danger
                        color = "#D9534F";
                    }
                    html += '<li class="list-group-item"><span class="badge" style="background-color: ' + color + ';">' + value + '</span>' + "Reservoir Count";
                    break;

                case "sT":
                    html += '<li class="list-group-item"><span class="badge">' + moment.unix(value).add({"hours": 5, "minutes": "30"}).format("lll") + '</span>' + "UDP started at";
                    break;

                case "eT":
                    html += '<li class="list-group-item"><span class="badge">' + parseInt(value / 93600, 10) + "d " + parseInt((value%93600)/3600) + "h " + parseInt((value%93600%3600)/60, 10) + 'm</span>' + "Duration since now";
                    break;
            }

            html += '</li>';
        });

        return html;
    },

    //get formatted server details
    getServerDetails: function (info) {
        var self = this,
            html = "";

        if (info) {
            $.each(info, function (key, value) {
                //skip some fields here
                if (key == "root_password" || key == "ip_address" || key == "url" || key == "id")
                    return;

                //label other fields or format accordingly
                if (value) {
                    switch (key) {
                        case "country":
                            html += '<li class="list-group-item">' + countriesMap[value]["name"] + '</li>';
                            break;

                        case "port":
                            html += '<li class="list-group-item">Find\'n\'Secure port ' + value + '</li>';
                            break;

                        case "hosted_at":
                            if (self.hostingLocations[value]) {
                                html += '<li class="list-group-item">Server is hosted @ <a href="http://' + self.hostingLocations[value].url + '" target="_blank">' + self.hostingLocations[value].name + '</a></li>';
                            }
                            break;

                        case "server_name":
                            html += '<li class="list-group-item">Server is named ' + value + '</li>';
                            break;

                        case "sw_version":
                            html += '<li class="list-group-item">Running Find\'n\'Secure v' + value + '</li>';
                            break;

                        case "user2_username":
                            html += '<li class="list-group-item">Sec. User username - ' + value + '</li>';
                            break;

                        case "user2_password":
                            html += '<li class="list-group-item">Sec. User password - ' + value + '</li>';
                            break;

                        case "email":
                            //iterate multiple email IDs, separated by semicolon, and span over multiple lines
                            var emails = value.split(";");
                            for (var i = 0, l = emails.length; i < l; i++)
                                html += '<li class="list-group-item"><a href="mailto:' + emails[i] + '">' + emails[i] +' </a></li>';
                            break;

                        default:
                            html += '<li class="list-group-item">' + value + '</li>';
                            break;
                    }
                }
            });
        }

        return html;
    },

    //get status of server
    getServerStatus: function ($row, ip, callback) {
        var self = this;

        $row.IP = ip;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=servers&fx=status&ip=" + ip + "&tcInt=" + (parseInt(self.preferences.tcInt || 0, 10) * 60),
            success: function (result) {
                // test code
                /*if (ip == "71.19.240.175") {
                    debugger;
                }*/

                try {
                    result = getJSONFromString(result);
                } catch (ex) {
                    console.log("server " + ip + " not working");
                    result = "";
                }

                if (callback) callback.apply(self, [$row, result]);
            }
        });
    }
};

var server_pref = {
    initialize: function () {
        var self = this;

        // attach form submit handler
        $("#preferencesForm").on("submit", function () {
            self.save();

            return false;
        });

        //fill hosting locations
        server_hosting_locations.get(function (h_locations) {
            fillDropDown("#hosted_at", h_locations, "name", "id");

            // get preferences and fill 'em up
            self.get(self.fill);
        });
    },

    // fill preferences
    fill: function (preferences) {
        $.each(preferences, function (key, value) {
            key = $("#" + key);
            if (key.is("input[type='checkbox']")) {
                key.prop("checked", +value);
            } else {
                key.val(value);
            }
        });
    },

    // save preferences
    save: function () {
        $("#errorMessage-1").hide();
        $("#successMessage-1").hide();

        var jsdata = createObject(["preferencesForm"]);

        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=misc&fx=savePreferences&module=4",
            data: jsdata,
            success: function (result) {
                if (strncmp(result, "success", 7)) {
                    $("#successMessage-1").show();

                    window.setTimeout(function () {
                        $("#successMessage-1").hide();
                    }, 10000);
                } else {
                    $("#errorMessage-1").show();
                }
            }
        });
    },

    // fetch preferences back
    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: false,
            url: "/embarc-utils/php/main.php?util=misc&fx=getPreferences&module=4",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console("Server Status: preferences not found");
                    return;
                }

                if (callback) callback.apply(self, [data]);
            }
        });
    },
};
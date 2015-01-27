$(window).load(init);

function init() {
    switch (location.pathname) {
        case "/embarc-utils/inventory/stock_in.php":
            stock_in.initialize();
            break;

        case "/embarc-utils/inventory/stock_out.php":
            stock_out.initialize();
            break;

        case "/embarc-utils/inventory/preferences.php":
            preferences.initialize();
            break;

        case "/embarc-utils/inventory/stock_finder.php":
            stock_finder.initialize();
            break;

        case "/embarc-utils/inventory/clients.php":
            clients.initialize();
            break;

        case "/embarc-utils/inventory/trackers.php":
            trackers.initialize();
            break;
    }
}

var stock_in = {
    initialize: function () {
        var self = this;

        //initialize datepicker
        $('.datepicker').datepicker({
            'autoclose': true,
            'format': "dd/mm/yyyy"
        });

        //add change listener on model drop down
        $("#model").on('change', function (ev) {
            self.fillModelDetails($(this).val());
        });

        //validate and submit form when done
        $("#stockInForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveStockButton").button('loading');

                window.setTimeout(function () {
                    //call method to submit this form
                    self.saveStock();

                    //reset state of submit button
                    $("#saveStockButton").button('reset');

                    //focus on serial number to start again quickly
                    $("#serial").focus();
                }, (self.defaults.waitTime * 1000));
                return false;
            }
        });

        var $in_invoice = $("#in_invoice"),
            $serial = $("#serial")

        //set focus to invoice number by default
        $in_invoice.focus();

        //prevent form submit on enter press on invoice number (useful when scanned via barcode scanner) also set focus to serial number if required
        $($in_invoice).keypress(function (ev) {
            if (ev.which === 13) {
                ev.preventDefault();
                $serial.focus();
            }
        });

        //prevent form submit on enter press on serial number (useful when scanned via barcode scanner) also set focus to IMEI if required
        $($serial).keypress(function (ev) {
            if (ev.which === 13) {
                ev.preventDefault();
                $("#imei").focus();
            }
        });

        //fetch list of trackers from server
        this.getTrackersList();

        //fetch preferences and update defaults
        preferences.get.apply(this, [this.updateDefaults]);
    },

    getTrackersList: function () {
        var self = this;

        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getTrackers",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);
                
                //save list of trackers
                self.trackers = data;

                //fill trackers in model select drop down
                fillDropDown("#model", self.trackers, "model", "model");

                //set default values
                if (self.setDefaults) self.setDefaults();
            }
        });
    },

    fillModelDetails: function (model) {
        for (var i = 0; i < this.trackers.length; i++) {
            if (model == this.trackers[i].model) {
                $("#vendor").val(this.trackers[i].vendorName);
                $("#warranty").val(this.trackers[i].warranty);
            }
        }
    },

    defaults: {
        'serial': "",
        'imei': "",
        'model': "VT-62",
        'dateOfPurchase': "today",
        'invoice_no': ""
    },

    trackers: [],

    updateDefaults: function (preferences) {
        if (!preferences) preferences = {};

        this.defaults['autoSave'] = + preferences.autoSaveIN || 0;
        this.defaults['model'] = preferences.model || this.defaults.model;
        this.defaults['waitTime'] = + preferences.waitTimeIN || 1;

        //disable auto save if required
        if (!this.defaults.autoSave) {
            //prevent form submit on enter press on IMEI number (useful when scanned via barcode scanner)
            $("#imei").keypress(function (ev) {
                if (ev.which === 13) {
                    ev.preventDefault();
                }
            });
        }
    },

    //set default values for each form field
    setDefaults: function () {
        var self = this;

        $.each(this.defaults, function (key, value) {
            //set today's date
            if (key === "dateOfPurchase" && value === "today") {
                $('.datepicker').datepicker('setDate', new Date());
            } else {
                if (key == "model") self.fillModelDetails(value);

                //set all other values
                $("#" + key).val(value);
            }
        });
    },

    saveStock: function () {
        var jsn = createObject(["stockInForm"]),
            self = this;
        dropElements(jsn, ["warranty", "vendor"]);
        jsn.dateOfPurchase = getFormattedDate(jsn.dateOfPurchase);
        
        $.ajax({
            type: "POST",
            url: "/embarc-utils/php/main.php?util=inventory&fx=saveStockItem",
            async: true,
            data: jsn,
            success: function (result) {
                var $errorMsg = $("#errorMessage-1"),
                    $successMsg = $("#successMessage-1"),
                    $imeiExistsMsg = $("#errorMessage-2");
                
                if (strncmp(result, "SUCCESS", 7)) {
                    //hide error message and show success message
                    $errorMsg.hide();
                    $imeiExistsMsg.hide();
                    $successMsg.show();

                    //clear quick fill fields
                    self.clearToQuickFill();

                    //hide success message after 5 seconds
                    window.setTimeout(function () {
                        $successMsg.hide();
                    }, 5000);
                } else if (strncmp(result, "IMEI_EXISTS", 11)) {
                    $successMsg.hide();
                    $errorMsg.hide();
                    $imeiExistsMsg.show();
                } else {
                    //show error message and hide success message
                    $successMsg.hide();
                    $imeiExistsMsg.hide();
                    $errorMsg.show();
                }
            }
        });
    },

    clearToQuickFill: function () {
        $("#imei,#serial").val("");
    }
};

var stock_out = {
    initialize: function () {
        var self = this;

        //initialize datepicker
        $('.datepicker').datepicker({
            'autoclose': true,
            'format': "dd/mm/yyyy"
        });

        var $out_invoice = $("#out_invoice"),
            $imei = $("#imei");

        //initially, set focus to invoice number
        $out_invoice.focus();

        $imei.on("blur", function () {
            self.checkForItemInStock($(this).val());
        });

        $imei.keypress(function (ev) {
            if (ev.which === 13) {
                ev.preventDefault();
                $imei.blur();
            }
        });

        //validate and submit form when done
        $("#stockOutForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveStockButton").button('loading');

                //call method to submit this form after a few wait seconds
                window.setTimeout(function () {
                    self.saveStockOut();

                    //reset button loading state
                    $("#saveStockButton").button('reset');

                    //focus on serial number to start again quickly
                    $imei.focus();
                }, (self.defaults.waitTime * 1000));

                return false;
            }
        });

        //get list of clients
        this.getClients();

        //fetch preferences and update defaults
        preferences.get.apply(this, [this.updateDefaults]);
    },

    checkForItemInStock: function (value) {
        var self = this,
            $errorMsg = $("#errorMessage-1"),
                    $imei_no_exist = $("#errorMessage-2"),
                    $successMsg = $("#successMessage-1");

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=inventory&fx=getItemInStock&prop=imei&val=" + value,
            success: function (data) {
                if (strncmp(data, "ERROR", 5)) {
                    //show an error
                    $errorMsg.hide();
                    $imei_no_exist.show();
                    $successMsg.hide();

                    //clean up
                    $("#serial").val("");
                    $("#model").val("");
                    $("#id").val("");
                } else {
                    //hide the error message, if visible, or anyways
                    $errorMsg.hide();
                    $imei_no_exist.hide();
                    $successMsg.hide();

                    //fill it up
                    data = getJSONFromString(data);
                    $("#serial").val(data["serial"]);
                    $("#model").val(data["model"]);
                    $("#id").val(data["id"]);
                    
                    //submit form if preference allows
                    if(self.defaults.autoSave) $("#stockOutForm").submit();
                }
            }
        });
    },

    getClients: function () {
        var self = this;

        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getClients",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);
                
                //fill clients list in client select drop down
                fillDropDown("#clientID", data, "name", "id");
            }
        });
    },

    defaults: {
        'serial': "",
        'imei': "",
        'out_warranty': "12",
        'dateOfSale': "today",
        'out_invoice_no': ""
    },

    updateDefaults: function (preferences) {
        if (!preferences) preferences = {};

        this.defaults['autoSave'] = +preferences.autoSaveOUT || 0;
        this.defaults['waitTime'] = +preferences.waitTimeOUT || 1;
        this.defaults['out_warranty'] = +preferences.out_warranty || this.defaults.out_warranty;

        //fill in default values
        this.setDefaults();
    },

    //set default values for each form field
    setDefaults: function () {
        $.each(this.defaults, function (key, value) {
            //set today's date
            if (key === "dateOfSale" && value === "today")
                $('.datepicker').datepicker('setDate', new Date());
            else
                //set all other values
                $("#" + key).val(value);
        });
    },

    saveStockOut: function () {
        var jsn = createObject(["stockOutForm"]),
            self = this;
        dropElements(jsn, ["model", "serial"]);
        jsn.dateOfSale = getFormattedDate(jsn.dateOfSale);
        
        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=inventory&fx=updateStockItem",
            data: jsn,
            success: function (result) {
                var $errorMsg = $("#errorMessage-1"),
                    $imei_no_exist = $("#errorMessage-2"),
                    $successMsg = $("#successMessage-1");

                if (strncmp(result, "SUCCESS", 7)) {
                    //hide error message and show success message
                    $errorMsg.hide();
                    $imei_no_exist.hide();
                    $successMsg.show();

                    //hide success message after 5 seconds
                    window.setTimeout(function () {
                        $successMsg.hide();
                    }, 5000);

                    //clear quick fill fields
                    self.clearToQuickFill();
                } else if (strncmp(result, "IMEI_NOT_STOCK", 14)) {
                    //show message that IMEI number does not exist
                    $errorMsg.hide();
                    $imei_no_exist.show();
                    $successMsg.hide();
                } else {
                    //an error occured - dunno what?
                    $errorMsg.show();
                    $imei_no_exist.hide();
                    $successMsg.hide();
                }
            }
        });
    },

    clearToQuickFill: function () {
        $("#imei,#serial,#model").val("");
    }
};

var preferences = {
    initialize: function () {
        var self = this;

        stock_in.getTrackersList.apply(this);

        //remove independent 'required' messages
        jQuery.validator.messages.required = "";

        $("#preferencesForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                self.savePreferences();

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

    setDefaults: function () {
        //fill default preferences
        this.get(this.fill);
    },

    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=misc&fx=getPreferences&module=2",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console("Inventory: preferences not found");
                    return;
                }

                if (callback) callback.apply(self, [data]);
            }
        });
    },

    savePreferences: function () {
        $("#errorMessage-1").hide();
        $("#successMessage-1").hide();

        var jsdata = createObject(["preferencesForm"]);
        
        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=misc&fx=savePreferences&module=2",
            success: function (result) {
                if (strncmp(result, "success", 7)) {
                    $("#successMessage-1").show();

                    window.setTimeout(function () {
                        $("#successMessage-1").hide();
                    }, 5000);
                } else {
                    $("#errorMessage-1").show();
                }
            }
        });
    },

    trackers: [],

    fill: function (prefs) {
        var formFiller = new FormFiller(document.getElementById("preferencesForm", ""));

        formFiller.fillData(prefs);
    }
};

var stock_finder = {
    initialize: function () {
        var self = this;

        //attach change event on search criteria
        $("#stockSearchForm input[type=radio]").on("change", function () {
            self.changeCriteria(this.value);
        });

        //start with search textbox focussed
        $("#searchTextbox").focus();
        
        //get list of trackers
        this.getTrackersList(function (trackersList) {
            //save list of trackers
            self.trackers = trackersList;

            //if trackers list is scheduled to be filled on load
            if (self.doFillOnLoad.models) {
                self.fillTrackersList();
                self.doFillOnLoad.models = false;
            }
        });

        //get list of clients
        this.getClientsList(function (clientsList) {
            //save list of clients
            self.clients = clientsList;

            //fill clients mapper
            for (var i = 0, l = self.clients.length; i < l; i++) {
                self.clientsMap[self.clients[i].id] = self.clients[i].name;
            }

            //if clients list is scheduled to be filled on load
            if (self.doFillOnLoad.clients) {
                self.fillClientsList();
                self.doFillOnLoad.clients = false;
            }
        });

        //find something in stock
        $("#searchStockButton").on("click", function () {
            self.find(self.fillResultsInTable);
        });
    },

    changeCriteria: function (criteria) {
        var $searchDropdown = $("#searchDropdown"),
            $searchTextbox = $("#searchTextbox");

        $searchDropdown.val("");
        $searchTextbox.val("");
        $searchTextbox.datepicker("remove");

        switch (criteria) {
            case "model":
                //show dropdown and hide textbox
                $searchDropdown.show();
                $searchTextbox.hide();

                //populate list of trackers if available otherwise schedule for later
                if (this.trackers.length > 0) {
                    this.fillTrackersList();
                } else {
                    this.doFillOnLoad.models = true;
                    this.doFillOnLoad.clients = false;
                }
                break;

            case "clientID":
                //show dropdown and hide textbox
                $searchDropdown.show();
                $searchTextbox.hide();

                //populate list of clients if available otherwise schedule for later
                if (this.clients.length > 0) {
                    this.fillClientsList();
                } else {
                    this.doFillOnLoad.models = false;
                    this.doFillOnLoad.clients = true;
                }
                break;

            case "dateOfSale":
                //hide dropdown and show textbox
                $searchDropdown.hide();
                $searchTextbox.show();

                //initialize datepicker
                $("#searchTextbox").datepicker({
                    'autoclose': true,
                    'format': "yyyy-mm-dd"
                });
                //show datepicker and set todays date to current date
                $searchTextbox.datepicker("show");
                $searchTextbox.datepicker("setDate", new Date());
                break;

            default:
                //hide dropdown and show textbox
                $searchDropdown.hide();
                $searchTextbox.show();
                break;
        }
    },

    doFillOnLoad: {
        models: false,
        clients: false
    },

    trackers: [],

    getTrackersList: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getTrackers",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);

                if (callback) callback(data);
            }
        });
    },

    fillTrackersList: function () {
        $("#searchDropdown").html("");

        //fill trackers in model select drop down
        fillDropDown("#searchDropdown", this.trackers, "model", "model");
    },

    clients: [],

    clientsMap: {},

    getClientsList: function (callback) {
        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getClients",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);

                if (callback) callback(data);
            }
        });
    },

    fillClientsList: function () {
        $("#searchDropdown").html("");

        //fill clients list in client select drop down
        fillDropDown("#searchDropdown", this.clients, "name", "id");
    },

    find: function (callback) {
        var self = this,
            jsdata = createObject(["stockSearchForm"]);
        if (jsdata.query1 === "" && jsdata.query2 === "") return;

        jsdata.query = jsdata.query1 || jsdata.query2;
        delete jsdata.query1;
        delete jsdata.query2;
        
        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=inventory&fx=search",
            success: function (data) {
                data = getJSONFromString(data);
                
                if (callback) callback.apply(self, [data, +jsdata.count]);
            }
        });
    },

    fillResultsInTable: function (results, isCount) {
        var $tableResult = $("#tableResult"),
            $countResult = $("#countResult");

        //clear all previous results
        $tableResult.html("");
        $countResult.html("");

        //hide all containers
        $("#tableResultContainer").hide();
        $("#countResultContainer").hide();
        $("#noResults").hide();

        //populate new results
        if (isCount) {
            $("#countResultContainer").show();            

            $countResult.html(results);
        } else {
            
            var l = results.length;
            if (l > 0) {
                $("#tableResultContainer").show();
            }
            else {                
                $("#noResults").show();
            }

            for (var i = 0; i < l; i++) {
                $tableResult.append(this.createResultsRow(results[i]));
            }
        }
    },

    createResultsRow: function (rowData) {
        var remainingWarranty = "";
        if (rowData.dateOfSale) {
            remainingWarranty = this.getRemainingWarranty(rowData.dateOfSale, +rowData.out_warranty);
            if (remainingWarranty < 0) remainingWarranty = "Expired " + Math.abs(remainingWarranty) + " days ago";
            else remainingWarranty += " days";
        }

        return '<tr>'+
                    (+rowData.inStock ? "<td class=\"stock\">In Stock" : "<td class=\"sold\">Sold") + '</td>\
                    <td>' + rowData.imei + '</td>\
                    <td>' + rowData.serial + '</td>\
                    <td>' + rowData.model + '</td>\
                    <td>' + dateStampToString(rowData.dateOfPurchase) + '</td>\
                    <td>' + rowData.in_invoice + '</td>\
                    <td>' + rowData.in_username + '</td>\
                    <td>' + (rowData.dateOfSale ? dateStampToString(rowData.dateOfSale) : "") + '</td>\
                    <td>' + (rowData.clientID ? this.clientsMap[rowData.clientID] : "") + '</td>\
                    <td>' + rowData.out_invoice + '</td>\
                    <td>' + remainingWarranty + '</td>\
                    <td>' + rowData.out_username + '</td>\
                </tr>';
    },

    //get remaining warranty, using sales date and warranty provided
    getRemainingWarranty: function (salesDate, warrantyProvided) {
        var today = new Date(),
            salesDate = new Date(salesDate),
            difference = dayDiff(salesDate, today),
            warrantyEnd = new Date(salesDate);
        warrantyEnd.setMonth(warrantyEnd.getMonth() + warrantyProvided);

        return Math.floor(dayDiff(salesDate, warrantyEnd) - difference);
    }
};

var clients = {
    initialize: function () {
        var self = this;

        //focus on company name - default
        $("#name").focus();

        //validate and submit form when done
        $("#addClientForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveClientButton").button('loading');

                //call method to submit this form
                self.save();

                //reset state of submit button
                $("#saveClientButton").button('reset');

                //focus on company name to start again quickly
                $("#name").focus();

                //clear form when done
                form.reset();

                return false;
            }
        });

        //populate list of clients
        this.get(function (clientsList) {
            self.checkNfill(clientsList);
        });
    },

    checkNfill: function (clientsList) {
        var self = this,
            l = clientsList.length,
            i = 0;

        if (l > 0) {
            //hide no clients message
            $("#noClients").hide();

            //show clients list
            $("#clientsListContainer").show();

            for (; i < l; i++) self.addClient2List(clientsList[i]);
        } else {
            //show no clients message
            $("#noClients").show();

            //hide clients list
            $("#clientsListContainer").hide();
        }
    },

    //get list of clients
    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getClients",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);

                if (callback) callback(data);
            }
        });
    },

    //save a client
    save: function () {
        var self = this,
            jsdata = createObject(["addClientForm"]);

        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=inventory&fx=saveClient",
            data: jsdata,
            success: function (result) {
                if (strncmp(result, "SUCCESS", 7)) {
                    self.checkNfill([jsdata]);
                }
            }
        });
    },

    //add a client to list of clients
    addClient2List: function (clientDetails) {
        $("#clientsList").append(this.createClientRow(clientDetails));
    },

    //create a row for list of clients
    createClientRow: function (rowData) {
        return '<tr>\
                    <td>' + rowData.name + '</td>\
                    <td>' + rowData.person + '</td>\
                    <td>' + rowData.email + '</td>\
                </tr>';
    }
};

var trackers = {
    initialize: function () {
        var self = this;

        //focus model
        $("#model").focus();

        //validate and submit form when done
        $("#addTrackerForm").validate({
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass("has-error");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                //set current state of submit button to loading
                $("#saveTrackerButton").button('loading');

                //call method to submit this form
                self.save();

                //reset state of submit button
                $("#saveTrackerButton").button('reset');

                //focus on company name to start again quickly
                $("#model").focus();

                //clear form when done
                form.reset();

                return false;
            }
        });

        //populate list of trackers
        this.get(function (trackersList) {
            self.checkNfill(trackersList);
        });
    },

    checkNfill: function (trackersList) {
        var self = this,
            l = trackersList.length,
            i = 0;

        if (l > 0) {
            //hide no trackers message
            $("#noTrackers").hide();

            //show trackers list
            $("#trackersListContainer").show();

            for (; i < l; i++) self.addTracker2List(trackersList[i]);
        } else {
            //show no trackers message
            $("#noTrackers").show();

            //hide trackers list
            $("#trackersListContainer").hide();
        }
    },

    //get list of trackers
    get: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            url: "/embarc-utils/php/main.php?util=inventory&fx=getTrackers",
            async: true,
            success: function (data) {
                data = getJSONFromString(data);

                if (callback) callback(data);
            }
        });
    },

    //save a tracker
    save: function () {
        var self = this,
            jsdata = createObject(["addTrackerForm"]);

        $.ajax({
            type: "POST",
            async: true,
            url: "/embarc-utils/php/main.php?util=inventory&fx=saveTracker",
            data: jsdata,
            success: function (result) {
                if (strncmp(result, "SUCCESS", 7)) {
                    self.checkNfill([jsdata]);
                }
            }
        });
    },

    //add a client to list of trackers
    addTracker2List: function (details) {
        $("#trackersList").append(this.createTrackerRow(details));
    },

    //create a row for list of trackers
    createTrackerRow: function (rowData) {
        return '<tr>\
                    <td>' + rowData.model + '</td>\
                    <td>' + rowData.vendorName + '</td>\
                    <td>' + rowData.vendorModel + '</td>\
                    <td>' + rowData.warranty + '</td>\
                </tr>';
    }
};
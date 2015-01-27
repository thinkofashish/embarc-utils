/*
* courier.js - 2.0.2
* All JavaScript methods related to courier system
*
* 25-Jan-2014 Tushar Agarwal niftytushar@gmail.com
* Bug Fixes: #2
* 
*/

$(window).load(init);

function init() {
    switch (window.eu.id) {
        case "courier_dhl":
            dhl.initialize();
            break;

        case "courier_preferences":
            preferences.initialize();
            break;
    }
}

var dhl = {
    initialize: function () {
        var self = this;

        // Fill type
        fillDropDown("#type", this.types, "name", "value");
        $("#type").val("NDOC");

        //Fill countries
        this.fetchCountries(function (countriesList) {
            fillDropDown("#country", countriesList, "name", "code");
        });
        
        // Get Preferences
        preferences.fetch.apply(self, [function (prefs) {
            self.preferences = prefs;
        }]);

        jQuery.validator.messages.required = "";
        jQuery.validator.messages.number = "";

        $("#packageDetailsForm").validate({
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass("has-error");
            },
            submitHandler: function (form) {
                self.calculate();

                return false;
            },
            rules: {
                weight: {
                    required: true,
                    number: true
                }
            },
            messages: {
                weight: {
                    required: "Please enter <strong>weight</strong> of package",
                    number: "Weight of package should be <strong>numeric</strong>"
                }
            },
            showErrors: function (errorMap, errorList) {
                $("#errorMessage-1").hide();
                var errorMessage = "There are error in your submission.<ul>";
                
                // create a list of errors
                $.each(errorList, function (key, value) {
                    if (value.message) errorMessage += "<li>" + value.message + "</li>";

                    // mark errorsome fields in red
                    $(value.element).parent().addClass("has-error");
                });

                $("#errorMessage-1").html(errorMessage);

                if (errorList.length > 0) {
                    $("#errorMessage-1").show();
                }
            },
            focusCleanup: true,
            focusInvalid: false,
            onkeyup: false,
            onfocusout: false
        });        
    },

    // fetch list of countries
    fetchCountries: function (fetchCallback) {
        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=courier&fx=getCountries",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid list of countries received from server");
                    return;
                }

                if (fetchCallback) fetchCallback.apply(self, [data]);
            }
        });
    },

    // calculate cost
    calculate: function () {
        var self = this,
            jsdata = createObject(["packageDetailsForm"]);

        $.ajax({
            type: "POST",
            async: true,
            data: jsdata,
            url: "/embarc-utils/php/main.php?util=courier&fx=dhlCalculate",
            success: function (data) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid calculation result received from server.");
                    return;
                }

                var resultEl = document.getElementById("result");
                resultEl.innerHTML = "";

                for (var i = 0; i < data.length; i++) {
                    if (i == 1) append = "alt";
                    var result = data[i];

                    result.price = parseFloat(result.price);

                    resultEl.innerHTML += "Weight: <strong>" + result.weight + "</strong> kgs<br />";

                    //fill computed result
                    resultEl.innerHTML += "The Best Price for this package of type <strong>" + self.typesMapper[result.type] + "</strong> is <strong>₹" + result.price + "</strong> using DHL courier, Account Number <strong>" + result.account + "</strong>.";

                    //show additional charges
                    resultEl.innerHTML += self.showAdditionalCharges(result.price);

                    //show final price
                    var totalPrice = result.price + parseFloat(self.getFuelSurcharge(result.price)) + parseFloat(self.getClearanceCost(result.price)) + parseFloat(self.getMiscCharges(result.price));
                    resultEl.innerHTML += self.showFinalPrice(totalPrice);

                    resultEl.innerHTML += "<br />";
                    //document.getElementById("altResult").innerHTML = "Increase the weight of the package to " + result.weight + "kgs. This " + typesMapper[result.type] + " type package will cost ₹" + result.price + " using DHL courier, Account Number " + result.account;
                }

            }
        });
    },

    // display additional charges
    showAdditionalCharges: function (price) {
        var str = "<table class='table'>\
            <thead>\
                <tr>\
                    <th>Charge</th>\
                    <th>Rate</th>\
                    <th>Price @ ₹" + price + "</th>\
                </tr>\
            </thead>\
    <tbody>";

        str += '<tr>\
                <td style="min-width: 25%;">Fuel Surcharge</td>\
                <td>' + this.preferences.fuelSurcharge + '%</td>\
                <td>₹' + this.getFuelSurcharge(price) + '</td>\
            </tr>';
        str += '<tr>\
                <td style="min-width: 25%;">Miscellaneous</td>\
                <td>' + this.preferences.misc + '%</td>\
                <td>₹' + this.getMiscCharges(price) + '</td>\
            </tr>';
        str += '<tr>\
                <td style="min-width: 25%;">Clearance Cost</td>\
                <td>₹' + this.preferences.clearanceCost + '</td>\
                <td>₹' + this.getClearanceCost(price) + '</td>\
            </tr>';
        str += '</tbody>\
        </table>';

        return str;
    },

    // display final price in desired format
    showFinalPrice: function (price) {
        //converting all values to float - convenience
        this.preferences.handlingCharges_USD = parseFloat(this.preferences.handlingCharges_USD),
        this.preferences.handlingCharges_EUR = parseFloat(this.preferences.handlingCharges_EUR),
        this.preferences.minBilling_USD = parseFloat(this.preferences.minBilling_USD),
        this.preferences.minBilling_EUR = parseFloat(this.preferences.minBilling_EUR);

        /*
        * Fix #1: Removed charges in INR
        * Requestd by shailendra.bansal@findnsecure.com on August 1st, 2013
        *
        * To display charges in INR again, include the following line:
        * '<div class="col-lg-4 breadcrumb">₹' + price.toFixed(2) + '</div>'
        *
        * Fix #2: Final price has been split into two, Handling Charges & Freight Charges
        *
        * Note: Final Total Price is not displayed, as indicated via email communication with shailendra.bansal@findnsecure.com on August 1st, 2013
        */

        var handlingCharges_INR = +this.preferences.handlingCharges_USD * +this.preferences.dollarValue,
            handlingChargesRow = '<div class="row align_center">\
                <div class="col-sm-3"><div class="breadcrumb"><strong>Handling Charges</strong></div></div>\
                <div class="col-sm-3"><div class="breadcrumb">₹' + handlingCharges_INR.toFixed(2) + '</div></div>\
                <div class="col-sm-3"><div class="breadcrumb">$' + this.preferences.handlingCharges_USD + '</div></div>\
                <div class="col-sm-3"><div class="breadcrumb">&euro;' + this.preferences.handlingCharges_EUR + '</div></div>\
            </div>';
        var freightCharge_INR = Math.ceil(+price - handlingCharges_INR),
            freightCharge_USD = Math.ceil(parseFloat(price / parseFloat(this.preferences.dollarValue)) - this.preferences.handlingCharges_USD),
            freightCharge_EUR = Math.ceil(parseFloat(price / parseFloat(this.preferences.euroValue)) - this.preferences.handlingCharges_EUR);

        // check for total minimum charges - USD
        if ((freightCharge_USD + this.preferences.handlingCharges_USD) < this.preferences.minBilling_USD) {
            freightCharge_INR = +this.preferences.minBilling_USD * +this.preferences.dollarValue - handlingCharges_INR;
            freightCharge_USD = this.preferences.minBilling_USD - this.preferences.handlingCharges_USD;
        }

        // check for total minimum charges - EUR
        if ((freightCharge_EUR + this.preferences.handlingCharges_EUR) < this.preferences.minBilling_EUR) {
            freightCharge_EUR = this.preferences.minBilling_EUR - this.preferences.handlingCharges_EUR;
        }

        var freightChargesRow = '<div class="row align_center">\
                <div class="col-sm-3"><div class="breadcrumb"><strong>Freight Charges</strong></div></div>\
                <div class="col-sm-3"><div class="breadcrumb">₹' + freightCharge_INR + '</div></div>\
                <div class="col-sm-3"><div class="breadcrumb">$' + freightCharge_USD + '</div></div>\
                <div class="col-sm-3"><div class="breadcrumb">&euro;' + freightCharge_EUR + '</div></div>\
            </div>';

        return (handlingChargesRow + freightChargesRow);
    },

    getFuelSurcharge: function (price) {
        return (price * parseFloat(this.preferences.fuelSurcharge) / 100).toFixed(2);
    },

    getClearanceCost: function (price) {
        return (parseFloat(this.preferences.clearanceCost)).toFixed(2);
    },

    getMiscCharges: function (price) {
        return (price * parseFloat(this.preferences.misc) / 100).toFixed(2);
    },

    types: [
        { 'name': 'Document', 'value': 'DOC' },
        { 'name': 'Non-Document', 'value': 'NDOC' }
    ],

    typesMapper: {
        "DOC": "Document",
        "DOCMUL": "Document (Multiplied)",
        "NDOC": "Non-Document",
        "NDOCMUL": "Non-Document (Multiplied)"
    },

    preferences: null
};

var preferences = {
    initialize: function () {
        var self = this;

        // fetch previously saved preferences and save them
        this.fetch(this.fill);

        $("#saveButton").on("click", function () {
            self.save();
        });
    },

    // fetch list of preferences
    fetch: function (fetchCallback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=courier&fx=getSettings",
            success: function (data, textStatus, jqXHR) {
                try {
                    data = getJSONFromString(data);
                } catch (ex) {
                    console.log("Invalid preferences received from server");
                    return;
                }
                if (fetchCallback) fetchCallback.apply(self, [data]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to fetch preferences " + errorThrown);
            }
        });
    },

    // fill preferences
    fill: function (prefsObject) {
        $.each(prefsObject, function (key, value) {
            $("#" + key).val(value);
        });
    },

    // save preferences
    save: function () {
        var jsdata = createObject(["settingsForm"]);

        $.ajax({
            type: "POST",
            async: true,
            data: { 'pref': JSON.stringify(jsdata) },
            url: "/embarc-utils/php/main.php?util=courier&fx=setSettings",
            success: function (data) {
                if (data == "SUCCESS") {
                    $("#successMessage-1").slideDown();
                    window.setTimeout(function () {
                        $("#successMessage-1").slideUp();
                    }, 10000);
                } else {
                    $("#errorMessage-1").slideDown();
                    window.setTimeout(function () {
                        $("#errorMessage-1").slideUp();
                    }, 10000);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("unable to save preferences " + errorThrown);
            }

        });
    }
};
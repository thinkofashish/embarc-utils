$(window).load(init);

function init() {
    switch (location.pathname) {
        case "/embarc-utils/dashboard.php":
            dashboard.initialize();
            break;
    }
}

var dashboard = {
    initialize: function () {
        this.getModules(this.displayModules);
    },

    getModules: function (callback) {
        var self = this;

        $.ajax({
            type: "GET",
            async: true,
            url: "/embarc-utils/php/main.php?util=misc&fx=getModules",
            success: function (data) {
                if (strncmp(data, "ERROR", 5)) {
                    alert("No modules allowed");
                } else {
                    self.modules = getJSONFromString(data);
                    callback.apply(self);
                }
            }
        });
    },

    displayModules: function () {
        var modulesContainer = document.getElementById("modulesContainer");
        $.each(this.modules, function (key, value) {
            modulesContainer.innerHTML += '<div id="job-thumb" style="opacity: 1">\
             <div id="job-image">\
             <a href="/embarc-utils/' + value.href + '" class="">\
             <img width="600" height="600" src="/embarc-utils/images/' + value.image + '" class="lazy" style="visibility: visible; opacity: 1;">\
             <noscript><img src="/embarc-utils/images/courier.png" width="600" height="600"></noscript></a></div>\
             <div id="job-heading"><h2 class="thumb-heading"><a href="/embarc-utils/' + value.href + '">' + value.name + '<br />\
             <p class="f12" style="margin-top: 8px;">' + value.description + '</p>\
             </a></h2></div></div>';
        });
    },

    modules: {}
};
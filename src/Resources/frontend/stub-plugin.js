define(function () {
    return {
        load: function (name, req, onLoad, config) {
            if (config.isBuild) {
                onLoad(null);
            } else {
                throw "error";
            }
        },

        normalize: function (name, normalize) {
            return normalize(name);
        },
    };
});

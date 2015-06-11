define([
    'lodash',
    'module',
    'assetic',
], function (_, module, asset) {
    'use strict';

    var config = _.extend({
        script: asset('bundles/fosjsrouting/js/router.js'),
        data:   '/js/routing.json',
    }, module.config());

    return {
        load: function (name, req, onLoad) {
            requirejs([
                'json!' + config.data,
                config.script,
            ], function (data) {
                window.fos.Router.setData(data);

                var router = window.Routing;
                delete window.Routing;
                onLoad(router);
            });
        },
    };
});
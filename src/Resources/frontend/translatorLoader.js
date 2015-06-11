define([
    'lodash',
    'module',
    'assetic',
    'router',
], function (_, module, asset, router) {
    'use strict';

    var config = _.extend({
        script:  asset('bundles/bazingajstranslation/js/translator.min.js'),
        domains: [{
            name:    'messages',
            locales: ['en'],
        }],
    }, module.config());

    config.domains = _.map(config.domains, function (row) {
        return router.generate('bazinga_jstranslation_js', {
            domain:  row.name,
            locales: row.locales.join(', '),
        });
    });

    return {
        load: function (name, req, onLoad) {
            requirejs([
                config.script,
            ], function () {
                requirejs(config.domains, function () {
                    // TODO: delete window.Translator

                    onLoad(window.Translator);
                });
            });
        },
    };
});
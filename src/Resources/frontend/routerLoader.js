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

        normalize : function (name, normalize) {
            // resolve any relative paths
            return normalize(name);
        },

        //write method based on RequireJS official text plugin by James Burke
        //https://github.com/jrburke/requirejs/blob/master/text.js
        write : function(pluginName, moduleName, write){
            if(moduleName in buildMap){
                var content = buildMap[moduleName];
                write('define("'+ pluginName +'!'+ moduleName +'", function(){ return '+ content +';});\n');
            }
        }
    };
});
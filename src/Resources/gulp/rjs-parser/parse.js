'use strict';

var _ = require('lodash'),
    requirejs = require('requirejs'),
    path = require('path');

requirejs.config({
    paths: {
        'rjs-parser': __dirname + '/lib'
    }
});

var parse = requirejs('rjs-parser/parse');

module.exports = function (moduleName, data) {
    var modules = parse(moduleName, null, data, {
        'findNestedDependencies': true,
    });
    if (modules === null) {
        modules = [];
    } else {
        modules = modules.replace(/^.*?(\[.+?\]).*$/g, '$1');
        modules = JSON.parse(modules);
    }

    modules = _.unique(_.map(_.unique(_.filter(modules, function (module) {
        return module.indexOf('!') === -1;
    })), function (module) {
        if (module[0] === '.') {
            module = moduleName + '/../' + module;
            module = path.normalize(module);
        }

        return module;
    }));

    return modules;
};
module.exports = function (name, args) {
    'use strict';

    var execSync = require('exec-sync'),
        data = execSync('app/console ' + name + (args ? args.join(' ') : ''));

    return JSON.parse(data);
};
module.exports = function (name, args) {
    'use strict';

    var execSync = require('spawn-sync'),
        result   = execSync('php', ['app/console', name + (args ? args.join(' ') : '')]);
    if (result.status !== 0) {
        process.stderr.write(result.stderr);
        process.exit(result.status);
    }
    process.stderr.write(result.stderr);

    return JSON.parse(result.stdout);
};
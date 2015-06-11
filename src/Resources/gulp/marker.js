'use strict';

var through = require('through2');

module.exports = function(clb) {
    return through.obj(function(file, enc, callback) {
        clb(file);

        callback(null, file);
    });
};
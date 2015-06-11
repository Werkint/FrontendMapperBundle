'use strict';

var sourcemaps = require('gulp-sourcemaps'),
    _ = require('underscore'),
    merge = require('multipipe'),
    marker = require('./marker'),
    gulpif = require('gulp-if'), // https://github.com/robrich/gulp-if
    uglify = require('gulp-uglify'), // github.com/terinjokes/gulp-uglify
    gutil = require('gulp-util');

module.exports = function (config) {
    return function () {
        if (!config.minify) {
            return gutil.noop();
        }

        return merge(
            sourcemaps.init(),
            gulpif('*.js', uglify().on('error', gutil.log)),
            sourcemaps.write('.', {
                sourceRoot: '/',
            })
        );
    };
};
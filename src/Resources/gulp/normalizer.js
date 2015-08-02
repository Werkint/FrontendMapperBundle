'use strict';

var gutil = require('gulp-util'),
    gulpif = require('gulp-if'),
    merge = require('multipipe'),
    normalizer = require('gulp-bower-normalize'),
    Path = require('path'),
    rename = require('./rename2');

function getComponents(file) {
    var relativePath = file.relative;
    var pathParts = Path.dirname(relativePath).split(Path.sep);

    var ret = {
        ext:         Path.extname(relativePath).substr(1), // strip dot
        filename:    Path.basename(relativePath),
        packageName: pathParts[0],
    };
    pathParts.shift();
    ret.path = pathParts.join('/');

    return ret;
}

module.exports = function (config) {
    return function () {
        var normalizerOptions = {
            "bowerJson": './' + config.bower.mainFile,
            "flatten":   true,
        };

        // TODO: проверка на существование
        var renames = config.bower.data.overrides.renames || {},
            ignored = config.bower.data.overrides.ignored || [];

        // TODO: tmp
        var defaultPrefix = 'js';

        var notInIgnored = function (file) {
            file.pathComponents = getComponents(file);

            return !ignored[file.pathComponents.packageName];
        };

        return merge(
            gulpif(notInIgnored, normalizer(normalizerOptions), rename(function (path, file) {
                // Переносим пакеты целиком
                path.dirname = Path.join(
                    defaultPrefix,
                    file.pathComponents.path
                );
            })),
            rename(function (path) {
                if (renames[path.basename]) {
                    path.basename = renames[path.basename];
                }
            })
        );
    };
};

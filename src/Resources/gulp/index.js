'use strict';

// global process variable. поставь в баш рц или в файле который запускает таск
require('events').EventEmitter.prototype._maxListeners = process.env.NODE_MAX_LISTENER || 400;

var gulp = require('gulp'),
    _ = require('lodash'),
    merge = require('merge-stream'),
    gutil = require('gulp-util'),
    multipipe = require('multipipe'),
    fs = require('fs'),
    change = require('gulp-change'),
    mark = require('gulp-mark'),
    marker = require('./marker'),
    rename = require('./rename2'),
    watch = require('gulp-watch'),
    globule = require('globule'),
    clean = require('gulp-clean'),
    notify = require("gulp-notify"),
    gulpIgnore = require('gulp-ignore'),
    plumber = require('gulp-plumber'),
    config = require('./symfony-task')('werkint:frontendmapper:config'),
    concat = require('gulp-concat'),
    gulpif = require('gulp-if'),
    coffee = require('gulp-coffee'),
    Path = require('path'),
    babel = require('gulp-babel'),
    requirejs = require('requirejs'),
    parseRjs = require('./rjs-parser/parse');

// Task-helpers
var symfonyMapper = require('./symfony-mapper')(config),
    bower = require('./bower')(config.bower),
    normalizer = require('./normalizer')(config),
    minify = require('./minify')(config);

// Список источников
var streams = {
    bower:   function () {
        var src = gulp.src(bower(), {
            base: config.bower.target,
        });

        return src
            .pipe(gulpIgnore.exclude('**/' + config.bower.renamesConfig))
            .pipe(gulpIgnore.exclude('**/bower.json'));
    },
    bundles: function () {
        var bundles = symfonyMapper();

        var list = _.map(bundles, function (resource) {
            return gulp.src(resource.path)
                .pipe(marker(function (file) {
                    file.resource = resource;
                }));
        });

        return merge.apply(undefined, list);
    },
};

// Меняет dest в зависимости от бандла
var bundleRename = function (path, file) {
    // TODO: Path.join
    path.dirname = config.path + '/' + file.resource.dest + '/' + path.dirname;
};

module.exports = function () {
    var getPipe = function (pipeName) {
        var list = streams;
        if (pipeName) {
            list = _.object(_.filter(_.pairs(streams), function (row) {
                return row[0] === pipeName;
            }));
        }

        var src = merge.apply(undefined, _.map(list, function (source, name) {
            return source().pipe(mark.set(name));
        }));

        /** es6 support */
        var exts = _.map(config.es6.extensions, function (ext) {
            return '.' + ext;
        }), options = _.pick(config.es6, ['modules']);
        src.pipe(gulpif(function (file) {
            return _.contains(exts, Path.extname(file.path));
        }, babel(options)));

        /** coffee support */
        _.each(config.coffee.extensions, function (ext) {
            src.pipe(gulpif('*.' + ext, coffee({
                "bare": true
            })).on('error', gutil.log));
        });

        return src
            .pipe(mark.if('bower', multipipe(
                normalizer(),
                rename(function (path) {
                    path.dirname = config.path + '/' + path.dirname;
                })
            )))
            .pipe(mark.if('bundles', rename(bundleRename)));
    };

    gulp.task('clean', function () {
        return gulp.src(config.root + config.path, {read: false})
            .pipe(clean());
    });

    gulp.task('dump', ['clean', 'bower'], function () {
        return getPipe()
            .pipe(minify())
            .pipe(gulp.dest(config.root));
    });

    gulp.task('dump-merged-bundles', ['dump'], function () {
        // TODO: async execution
        var Q = require('q');
        var deferred = Q.defer();

        var modules = [],
            bundles = symfonyMapper();
        _.each(bundles, function (bundle) {
            var files = globule.find(bundle.path);
            var prefix = bundle.name ? bundle.name + '/' : '';
            _.each(files, function (file) {
                var module = prefix + Path.relative(bundle.prefix, file);
                module = module.substr(0, module.lastIndexOf('.'));

                modules.push(module);
            });
        });

        var buildConfig = function (name, stubModules, optsIn) {
            var m = function (a, b) {
                if (_.isArray(a)) {
                    return a.concat(b);
                }
            };

            var opts = _.cloneDeep(require('./rjs-build'));
            if (optsIn) {
                _.merge(opts, optsIn, m);
            }

            var fileSrc = opts.baseUrl + '/' + name + '.js',
                fileDest = opts.baseUrl + '/' + name + '-built.js';

            _.merge(opts, {
                name: name,
                out:  fileDest,

                excludeShallow: [
                    name,
                    opts.__stubPluginName,
                ],

                __src: fileSrc,
            }, m);

            if (stubModules) {
                _.merge(opts, {
                    excludeShallow: stubModules,

                    paths: _.object(_.map(stubModules, function (module) {
                        return [module, opts.__stubPluginName];
                    })),
                }, m);
            }

            return opts;
        };
        var filterConfig = function (config) {
            return _.pick(config, function (val, key) {
                return key.indexOf('__') !== 0;
            });
        };

        var writeSrcFile = function (options, modulesIn) {
            var modules = _.difference(
                modulesIn,
                options.excludeShallow
            );

            var data = 'requirejs(["' + modules.join('", "') + '"], function(){})';
            var bundlesFile = [
                process.cwd(),
                options.__src,
            ].join('/');
            fs.writeFileSync(Path.normalize(bundlesFile), data);
        };
        var getDeps = function (modules) {
            var parseRjs = require('./rjs-parser/parse');

            var deps = [];
            _.each(modules, function (module) {
                var path = [
                    process.cwd(),
                    config.root,
                    config.path,
                    'js/' + module + '.js',
                ].join('/');

                var content = fs.readFileSync(path);

                deps = deps.concat(parseRjs(module, content));
            });

            deps = _.difference(_.unique(deps), modules);

            return deps;
        };

        var stubModules = [
            'json',
            'text',
            'routerLoader',
            'translatorLoader',
            'template',
        ];

        var buildBundles = function(callback){
            var opts = buildConfig('bundles', stubModules);
            opts.findNestedDependencies = true;
            writeSrcFile(opts, modules);
            requirejs.optimize(filterConfig(opts), function(){
                callback(opts);
            }, function (err) {
                console.log(err); // TODO: invoke error
            });
        };

        var buildDeps = function(callback){
            var optsLoaders = buildConfig('bundlesLoaders', [
                'router',
            ], {
                'exclude': getDeps(stubModules),
            });
            writeSrcFile(optsLoaders, stubModules);
            requirejs.optimize(filterConfig(optsLoaders), function(){
                callback(optsLoaders);
            }, function (err) {
                console.log(err); // TODO: invoke error
            });
        };

        buildBundles(function(opts){
            buildDeps(function(optsLoaders){
                var data = [
                    fs.readFileSync(process.cwd() + '/' + opts.out),
                    fs.readFileSync(process.cwd() + '/' + optsLoaders.out),
                ];
                fs.writeFileSync(process.cwd() + '/' + opts.out, data.join("\n"));

                deferred.resolve();
            });
        });

        return deferred.promise;
    });

    gulp.task('default', ['dump', 'dump-merged-bundles'], function () {
        return getPipe()
            .pipe(minify())
            .pipe(gulp.dest(config.root));
    });

    gulp.task('watch', function () {
        var list = symfonyMapper(),
            files = _.pluck(list, 'path');

        return watch(files, function (event) {
            var path = event.path,
                dest = _.find(list, function (row) {
                    return globule.isMatch(row.path, path);
                });

            var prefix = path.substr(dest.prefix.length);
            prefix = prefix.replace(/(\/[^\/]+)$/, '');

            dest = config.root + '/' + config.path + dest.dest + prefix;
            dest = dest.replace(/\/\//, '/');

            dest = process.cwd() + '/' + dest;

            var src = gulp.src(path);

            /** es6 support */
            var exts = _.map(config.es6.extensions, function (ext) {
                return '.' + ext;
            }), options = _.pick(config.es6, ['modules']);
            src.pipe(gulpif(function (file) {
                return _.contains(exts, require('path').extname(file.path));
            }, babel(options)));

            /** coffee support */
            _.each(config.coffee.extensions, function (ext) {
                src.pipe(gulpif('*.' + ext, coffee({
                    "bare": true
                })).on('error', gutil.log));
            });


            src.pipe(minify())
                .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
                .pipe(notify({
                    message:  'File changed: <%= file.relative %>',
                    notifier: function (options, callback) {
                        callback();
                    },
                }))
                .pipe(gulp.dest(dest));
        });
    });
};



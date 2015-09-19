module.exports = ({
    "baseUrl":                "./web/assets/js",
    "name":                   "bundles",
    "out":                    "./web/assets/js/bundles-built.js",
    "optimize":               "none",
    "excludeShallow":         [
        'json',
        'routerLoader',
        'translatorLoader',
        'template',
        'stub-plugin',
        'bundles',
    ],
    "findNestedDependencies": true,

    "paths": {
        'json':             'stub-plugin',
        'routerLoader':     'stub-plugin',
        'translatorLoader': 'stub-plugin',
        'template':         'stub-plugin',
    },
});
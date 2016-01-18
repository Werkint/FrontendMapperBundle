```
requirejs.config({
    "config":      {
        "translatorLoader": {
            "domains": [{
                name:    'messages',
                locales: ['en', document.documentElement.lang],
            }]
        }
    },
});
```
copy file to project: *package.json*
```
cd path/to/project
cp vendor/werkint/frontend-mapper-bundle/package.json ./
```
and run ```npm install```
may help:
also npm install --save glob
vendor/werkint/frontend-mapper-bundle/src/DependencyInjection/Compiler/JsmodelProviderPass.php

**gulpfile.js**
```javascript
(function () {
    'use strict';

    process.env.NODE_MAX_LISTENER = 500;
    require('./vendor/werkint/frontend-mapper-bundle/src/Resources/gulp/index.js')();
})();
```

**bower.json**

```bash
touch app/config/bower.json
```
```json
{
  "name": "brander-app",
  "version": "0.0.1",
  "authors": [
    "Someone <someone@gmail.com>"
  ],
  "ignore": [
    "**/.*"
  ],
  "resolutions": {
    "underscore": "~1.8.3",
    "backbone": "~1.1.2",
    "backbone.relational": "2f66cc5022"
  },
  "overrides": {
    "renames": {},
    "social": {
      "normalize": {
        "js/social": "*.js"
      }
    },
    "requirejs-domready": {
      "main": [
        "domReady.js"
      ]
    },
    "tabslet": {
      "main": [
        "jquery.tabslet.js"
      ]
    }
  }
}
```


### Usual configuration ###

0. do lines above (create gulpfile.js, npm install ...)
1. create bundle dependencies src/AppBundle/bower.json

```json
{
  "name": "sdelka-app",
  "main": [
    "bower.json"
  ],
  "dependencies": {
    "requirejs": "*",
    "jquery": "*",
    "lodash": "*",
    "backbone": "*",
    "backbone.relational": "*",
    "backbone.marionette": "*",
    "backbone.modelbinder": "*",
    "twig.js": "*",
    "backbone.radio": "*",
    "backbone.paginator": "*"
  }
}
```

2. create config src/AppBundle/Resources/public/config.js

```javascript
(function () {
    'use strict';

    var config = window.require ? window.require : {};
    config = {
        'paths': config.paths,

        'waitSeconds': 30,
        'urlArgs':     'bust=' + window.$assets_version,
        "baseUrl": "/assets/js",

        "map":    {
/*            "*":                   {
                "twig":                  "config/twig",
            },
            "config/twig":         {
                "twig": "twig",
            },
            "config/iwin-twitter": {
                "social/module.twitter": "social/module.twitter",
            },*/

        },

        "shim": {
            "jquery.elastic": {
                deps: ["jquery"]
            },
        },

        "config": {
            /*"social/api.google-loader":   window.$socials.google,*/
        },
    };
    
    require.config(config)
}());
```

4. edit app/Resources/views/base.html.twig

```twig
    <head>
        <meta charset="UTF-8" />
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        <!-- require -->
        <script src="{{ asset('assets/js/require.js') }}"></script>
        <!-- require config -->
        <script src="{{ asset('bundles/app/config.js') }}"></script>
    </head>
```
5. run ```app/console as:in``` and ```gulp``` 
6. test
```javascript
requirejs(['jquery'], function ($) { alert($); })
```
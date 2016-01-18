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

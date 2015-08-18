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
copy file to project:
    package.json
and run ```npm install```
may help:
also npm install --save exec-sync
also npm install --save glob
vendor/werkint/frontend-mapper-bundle/src/DependencyInjection/Compiler/JsmodelProviderPass.php
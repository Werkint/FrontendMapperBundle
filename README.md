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
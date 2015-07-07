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
also npm install --save exec-sync
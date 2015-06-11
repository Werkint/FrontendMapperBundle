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
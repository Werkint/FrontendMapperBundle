/**
 * Заглушка для ассетов
 *
 * TODO: конфиг require.js
 */
define(function () {
    'use strict';

    return function (path) {
        return '/' + path;
    };
});
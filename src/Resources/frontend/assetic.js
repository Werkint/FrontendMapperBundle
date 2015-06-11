/**
 * Заглушка для ассетов
 *
 * TODO: конфиг require.js
 */
define(function () {
    'use strict';

    // TODO: плохое решение
    var args = requirejs.s.contexts._.config.urlArgs;

    return function (path) {
        return '/' + path + (args ? '?' + args : '');
    };
});
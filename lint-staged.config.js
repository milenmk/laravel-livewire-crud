/* jshint esversion: 6 */
export default {
    '**/*.php*': [
        'php vendor/bin/rector process',
        'php vendor/bin/pint',
    ]
};

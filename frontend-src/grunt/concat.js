module.exports = {
    options: {
        banner: [
            '/*!',
            ' * <%= pkg.name %> frontend v<%= pkg.version %> (built: <%= grunt.template.today("yyyy-mm-dd") %>)',
            ' * http://github.com/rareloop/primer',
            ' *',
            ' * Copyright 2015 Rareloop (http://rareloop.com)',
            ' * Released under the MIT license',
            ' * http://github.com/rareloop/primer/blob/master/LICENCE.txt',
            ' */\n'
        ].join('\n'),
        separator: ';',
    },
    dist: {
        src: ['js/primer.js', 'node_modules/prismjs/prism.js'],
        dest: '../js/primer.js',
    }
};
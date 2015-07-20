module.exports = {
    options: {
        "-W099": true, // allowed mixed tabs and spaces
        curly: false,
        eqeqeq: false,
        immed: true,
        latedef: true,
        newcap: true,
        noarg: true,
        sub: true,
        undef: false,
        unused: false,
        boss: true,
        eqnull: true,
        browser: true,
        globals: {}
    },
    gruntfile: {
        src: 'Gruntfile.js'
    },
    lib_test: {
        src: ['../js/**.js']
    }
};
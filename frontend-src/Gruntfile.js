module.exports = function(grunt) {

    pkg = grunt.file.readJSON('package.json');

    require('load-grunt-config')(grunt, pkg);
    require('load-grunt-tasks')(grunt, pkg);

    // Default task(s)
    grunt.registerTask('default', ['release', 'watch']);
    grunt.registerTask('css', ['sass', 'csso']);
    grunt.registerTask('javascript', ['concat', 'uglify']);
    grunt.registerTask('release', ['css', 'javascript']);
};
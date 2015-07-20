module.exports = {
    dist: {
        options: {
            restructure: true,
            report: 'min'
        },
        files: {
            // output: input
          '../css/primer.min.css': ['../css/primer.css']
        }
    }
};

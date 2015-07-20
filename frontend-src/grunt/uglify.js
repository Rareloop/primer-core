module.exports = {
    options: {
        preserveComments: 'some'
    },
    build: {
        files: [
            {
                '../js/primer.min.js' : '../js/primer.js'
            }
        ]
    },
};
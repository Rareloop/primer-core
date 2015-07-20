module.exports = {
    options: {
        // Whether to spawn task runs in a child process. Setting this option to false speeds up the reaction time of the watch (usually 500ms faster for most)
        // and allows subsequent task runs to share the same context. Not spawning task runs can make the watch more prone to failing so please use as needed
        spawn: false,
        // As files are modified this watch task will spawn tasks in child processes. The default behavior will only spawn a new child process per target when
        // the previous process has finished. Set the interrupt option to true to terminate the previous process and spawn a new one upon later changes.
        interrupt: true
    },
    css: {
        files: ['sass/**/**/*.scss'],
        tasks: ['css']
    },
    js: {
        files: ['js/**/*.js'],
        tasks: ['javascript']
    }
};
module.exports = function(grunt) {
  grunt.initConfig({

    less: {
      production: {
        options: {
          paths: ['css'],
          yuicompress: true
        },
        files: {
          'css/application.css': 'css/application.less'
        }
      }
    }

  });

  grunt.loadNpmTasks('grunt-contrib-less');  

  grunt.registerTask('default', ['less']);
};
module.exports = function (grunt) {
    grunt.initConfig({
        concat: {
            script: {
                src: [
                    'node_modules/jquery/dist/jquery.js',
                    'node_modules/swiper/js/swiper.js',
                    'node_modules/nouislider/distribute/nouislider.js',
                    'node_modules/wnumb/wNumb.js',
                    'node_modules/jquery-mask-plugin/dist/jquery.mask.js',
                    'node_modules/jquery.scrollto/jquery.scrollto.js',
                    'node_modules/crypto-js/crypto-js.js',
                    'js_modules/variables.js',
                    'js_modules/error.js',
                    'js_modules/encrypt.js',
                    'js_modules/prototype.js',
                    'js_modules/loader.js',
                    'js_modules/helper.js',
                    'js_modules/functions.js',
                    'js_modules/manager-htaccess.js',
                    'js_modules/modal.js',
                    'js_modules/test.js',
                    'js_modules/sign-out.js',
                    'js_modules/main-side-bar.js',
                    'js_modules/client-create.js',
                    'js_modules/budget-create.js',
                    'js_modules/script.js'
                ],
                dest: 'js/script.js'
            },
        },
        compass: {
            default: {
                options: {
                    config: 'default-theme-config.rb',
                    cssDir: 'css'
                }
            },
        },
        watch: {
            options: {
                livereload: false,
                nospawn: true
            },
            watch_scss: {
                files: ['sass/**/*.scss'],
                tasks: ['compass'],
                options: {
                    spawn: false,
                }
            },
            watch_js: {
                files: ['js_modules/**/*.js'],
                tasks: ['concat'],
                options: {
                    spawn: false,
                }
            },
            watch_gruntfile: {
                files: ['Gruntfile.js'],
                tasks: ['concat'],
                options: {
                    spawn: false,
                }
            }
        },
        uglify: {
            index: {files: {'../js/script.js': ['js/script.js']}}
        },
        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            target: {
                files: {
                  '../css/style.css': ['css/style.css']
                }
              }
        }
    });

    grunt.loadNpmTasks('grunt-beep');
    //grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    //grunt.loadNpmTasks('grunt-contrib-htmlmin');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify-es');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    //grunt.loadNpmTasks('grunt-sftp-deploy');

    grunt.registerTask('default', ['compass', 'concat', 'beep']);

    grunt.registerTask('build', ['cssmin', 'uglify']);

    //grunt.registerTask('deploy', ['sftp-deploy']);
};
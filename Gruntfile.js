module.exports = function ( grunt ) {

	require( 'load-grunt-tasks' )( grunt );

    // Project configuration.
    grunt.initConfig(
        {
            pkg: grunt.file.readJSON('package.json'),

            // bump version numbers (replace with version in package.json)
            replace: {
                Version: {
                    src: [ 'readme.txt', '<%= pkg.name %>.php' ],
                    overwrite: true,
                    replacements: [
                    {
                        from: /Stable tag:.*$/m,
                        to: 'Stable tag: <%= pkg.version %>',
                    },
                    {
                        from: /Version:.*$/m,
                        to: 'Version: <%= pkg.version %>',
                    },
                    {
                        from: /public \$version = \'.*.'/m,
                        to: "public $version = '<%= pkg.version %>'",
                    },
                    {
                        from: /public \$version = \'.*.'/m,
                        to: "public $version = '<%= pkg.version %>'",
                    },
                    {
                        from: /public static \$version = \'.*.'/m,
                        to: "public static $version = '<%= pkg.version %>'",
                    },
                    {
                        from: /const VERSION = \'.*.'/m,
                        to: "const VERSION = '<%= pkg.version %>'",
                    },
                    ],
                },
            },
        } 
    );


};

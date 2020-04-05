<?php
namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'Application Name');

// Project repository
set('repository', 'https://github.com/ivanstan/fullstack-login.git');
set('git_tty', true);
set('bin_dir', 'bin');
set('http_user', 'glutenfr');
set('writable_mode', 'chmod');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['var']);
add('writable_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);

// Hosts
host('ivanstanojevic.me')
    ->user('glutenfr')
    ->port(2233)
    ->stage('stage')
    ->set('deploy_path', '~/projects/dev.ivanstanojevic.me');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');

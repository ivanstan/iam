<?php /** @noinspection ALL */

namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'Application Name');

// Project repository
set('repository', 'https://github.com/ivanstan/iam.git');
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

host('ivanstanojevic.me')
    ->user('glutenfr')
    ->port(2233)
    ->stage('prod')
    ->set('deploy_path', '~/projects/iam.ivanstanojevic.me');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

task('deploy:assets:install', function () {
    run('{{bin/php}} {{bin/console}} assets:install {{console_options}} {{release_path}}/public');
})->desc('Install bundle assets');

task('copy', function () {
    run('echo "scp -P 2233 -r ./public/build glutenfr@ivanstanojevic.me:~/projects/iam.ivanstanojevic.me/current/public"');
})->desc('Install bundle assets');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

//task('dump-autoload', function () {
//    run('{{bin/composer}} dump-env prod');
//});

set('bin/composer', '~/bin/composer.phar');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    'deploy:assets',
    'deploy:vendors',
    'deploy:assets:install',
    'deploy:assetic:dump',
    'copy',
    'deploy:cache:clear',
    'deploy:cache:warmup',
//    'dump-autoload',
    'deploy:writable',
    'database:migrate',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

//// set slack webhook
//set('slack_webhook', /* your slack webhook*/);
//// notify slack after successful deploy
//after('success', 'slack:notify:success');

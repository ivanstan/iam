<?php /** @noinspection ALL */

namespace Deployer;

require 'recipe/symfony.php';

set('application', 'IAM');

// Project repository
set('repository', 'https://github.com/ivanstan/iam.git');
set('git_tty', true);
set('bin_dir', 'bin');
set('http_user', 'glutenfr');
set('writable_mode', 'chmod');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');
set('composer_action', 'install');
set('bin/composer', '~/bin/composer.phar');

// Shared files/dirs between deploys
add('shared_files', ['.env', '.env.local']);
add('shared_dirs', ['var', 'config/secrets']);
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

task('deploy:vendors', function () {
    if (!commandExist('unzip')) {
        warning('To speed up composer installation setup "unzip" command with PHP zip extension.');
    }
    run('cd {{release_path}} && {{bin/composer}} {{composer_action}} {{composer_options}} 2>&1');
});

task('deploy:frontend', function () {
    $server = \Deployer\Task\Context::get()->getHost();
    $host = $server->getRealHostname();
    $user = $server->getUser();
    $port = $server->getPort();

    runLocally('yarn build');
    runLocally("scp -P $port -r ./public/build $user@$host:{{deploy_path}}/current/public");
    runLocally("scp -P $port -r ./public/bundles $user@$host:{{deploy_path}}/current/public");
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('deploy:dump-env', function () {
    run('cd {{release_path}} && {{bin/composer}} dump-env prod');
});

task('deploy:executable', function () {
    run('chmod +x {{release_path}}/bin/console');
});

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
    'deploy:executable',
    'deploy:assets:install',
    'deploy:assetic:dump',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:dump-env',
    'deploy:writable',
    'database:migrate',
    'deploy:symlink',
    'deploy:frontend',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

//// set slack webhook
//set('slack_webhook', /* your slack webhook*/);
//// notify slack after successful deploy
//after('success', 'slack:notify:success');

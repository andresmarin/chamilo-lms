<?php
/* For licensing terms, see /license.txt */

/**
 * This file includes all the services that are loaded via the ServiceProviderInterface
 *
 * @package chamilo.services
 */

use Silex\Application;
use Silex\ServiceProviderInterface;

// Monolog.
if (is_writable($app['sys_temp_path'])) {

    /**
     *  Adding Monolog service provider.
     *  Examples:
     *  $app['monolog']->addDebug('Testing the Monolog logging.');
     *  $app['monolog']->addInfo('Testing the Monolog logging.');
     *  $app['monolog']->addError('Testing the Monolog logging.');
     */
    $app->register(
        new Silex\Provider\MonologServiceProvider(),
        array(
            'monolog.logfile' => $app['chamilo.log'],
            'monolog.name' => 'chamilo',
        )
    );
}

//Setting HttpCacheService provider in order to use do: $app['http_cache']->run();
/*
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => $app['http_cache.cache_dir'].'/',
));*/

// Session provider
//$app->register(new Silex\ProviderSessionServiceProvider());
/*
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
            'anonymous' => true
        ),
        'secured' => array(
            'http' => true,
            'pattern' => '^/admin/.*$',
            'form'    => array(
                'login_path' => '/login',
                'check_path' => '/admin/login_check',
            ),
            'logout' => array(
                'path' => '/logout',
                'target' => '/'
            ),
            'users' => $app->share(function() use ($app) {
                $user = new Entity\User();
                $user->setEntityManager($app['orm.em']);
                //$user->loadUserByUsername('admin');
                return $user;

            }),
            'anonymous' => false
        ),
        'classic' => array(
            'pattern' => '^/.*$',
            'anonymous' => true
        )
    ),
    'security.role_hierarchy'=> array(
        'ROLE_ADMIN' => array('ROLE_TEACHER'),
        "ROLE_TEACHER" => array('ROLE_STUDENT'),
        "ROLE_STUDENT" => array('ROLE_STUDENT'),
        "ROLE_ANONYMOUS" => array("ROLE_ANONYMOUS"),
        "ROLE_RRHH" => array("ROLE_RRHH"),
        "ROLE_QUESTION_MANAGER" => array("ROLE_QUESTION_MANAGER")
    )
));

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
$app['security.encoder.digest'] = $app->share(function($app) {
    // use the sha1 algorithm
    // don't base64 encode the password
    // use only 1 iteration
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});
*/

/*
 *
 *
$app['security.access_manager'] = $app->share(function($app) {
    return new AccessDecisionManager($app['security.voters'], 'unanimous');
});*/

// Setting Controllers as services provider
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Validator provider
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Implements Symfony2 translator (needed when using forms in Twig)
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale' => 'es',
    'locale_fallback' => 'es'
));

// Handling po files (gettext)
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Finder\Finder;

$app['translator.cache.enabled'] = true;

//$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    /** @var Symfony\Component\Translation\Translator $translator  */
/*    if ($app['translator.cache.enabled']) {

        $locale = $translator->getLocale();
        //$phpFileDumper = new Symfony\Component\Translation\Dumper\PhpFileDumper();
        $dumper = new Symfony\Component\Translation\Dumper\MoFileDumper();
        $catalogue = new Symfony\Component\Translation\MessageCatalogue($locale);
        $catalogue->add(array('foo' => 'bar'));
        $dumper->dump($catalogue, array('path' => $app['sys_temp_path']));

    } else {

        $translator->addLoader('pofile', new PoFileLoader());

        $finder = new Finder();
        $files = $finder->files()->name('*.po')->in(api_get_path(SYS_PATH).'temp/langs/');
        //$language = api_get_language_interface();
        // @var SplFileInfo $entry
        foreach ($files as $entry) {
            $domain = basename($entry->getPath());
            $code = $entry->getBasename('.po');
            //$domain = $entry->getBasename('.inc.po');
            //$locale = api_get_language_isocode($language); //'es_ES';
            //if ($domain == 'admin') {
              //  var_dump($entry->getPathname());
                //$translator->addResource('pofile', $entry->getPathname(), $code, $domain);
                $translator->addResource('pofile', $entry->getPathname(), $code);
            //}
            //$translator->addResource('pofile', $entry->getPathname(), $locale, 'messages');
        }
        return $translator;
    }
}));
*/

// Form provider
$app->register(new Silex\Provider\FormServiceProvider());

// URL generator provider
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/*
use Doctrine\Common\Persistence\AbstractManagerRegistry;
class ManagerRegistry extends AbstractManagerRegistry
{
    protected $container;

    protected function getService($name)
    {
        return $this->container[$name];
    }

    protected function resetService($name)
    {
        unset($this->container[$name]);
    }

    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException('Namespace aliases not supported.');
    }

    public function setContainer(Application $container)
    {
        $this->container = $container;
    }
}

$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions, $app) {
    $managerRegistry = new ManagerRegistry(null, array(), array('orm.em'), null, null, $app['orm.proxies_namespace']);
    $managerRegistry->setContainer($app);
    $extensions[] = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($managerRegistry);
    return $extensions;
}));*/

// Setting Doctrine service provider (DBAL)
if (isset($app['configuration']['main_database'])) {

    /* The database connection can be overwritten if you set $_configuration['db.options']
       in configuration.php like this : */
    $defaultDatabaseOptions = array(
        'db_read' => array(
            'driver' => 'pdo_mysql',
            'host' => $app['configuration']['db_host'],
            'dbname' => $app['configuration']['main_database'],
            'user' => $app['configuration']['db_user'],
            'password' => $app['configuration']['db_password'],
            'charset'   => 'utf8',
            //'priority' => '1'
        ),
        'db_write' => array(
            'driver' => 'pdo_mysql',
            'host' => $app['configuration']['db_host'],
            'dbname' => $app['configuration']['main_database'],
            'user' => $app['configuration']['db_user'],
            'password' => $app['configuration']['db_password'],
            'charset'   => 'utf8',
            //'priority' => '2'
        ),
    );

    // Could be set in the $_configuration array
    if (isset($app['configuration']['db.options'])) {
        $defaultDatabaseOptions = $app['configuration']['db.options'];
    }

    $app->register(
        new Silex\Provider\DoctrineServiceProvider(),
        array(
            'dbs.options' => $defaultDatabaseOptions
        )
    );

    $mappings = array(
        array(
            /* If true, only simple notations like @Entity will work.
            If false, more advanced notations and aliasing via use will work.
            (Example: use Doctrine\ORM\Mapping AS ORM, @ORM\Entity)*/
            'use_simple_annotation_reader' => false,
            'type' => 'annotation',
            'namespace' => 'Entity',
            'path' => api_get_path(INCLUDE_PATH).'Entity',
            // 'orm.default_cache' =>
        ),
        array(
            'use_simple_annotation_reader' => false,
            'type' => 'annotation',
            'namespace' => 'Gedmo',
            'path' => api_get_path(SYS_PATH).'vendors/gedmo/doctrine-extensions/lib/Gedmo',
        )
    );

    // Setting Doctrine ORM
    $app->register(
        new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider,
        array(
            'orm.auto_generate_proxies' => true,
            'orm.proxies_dir' => $app['db.orm.proxies_dir'],
            //'orm.proxies_namespace' => '\Doctrine\ORM\Proxy\Proxy',
            'orm.ems.default' => 'db_read',
            'orm.ems.options' => array(
               'db_read' => array(
                   'connection' => 'db_read',
                   'mappings' => $mappings,
               ),
               'db_write' => array(
                   'connection' => 'db_write',
                   'mappings' => $mappings,
               ),
            ),
        )
    );
}

// Setting Twig as a service provider
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => array(
            api_get_path(SYS_CODE_PATH).'template', //template folder
            api_get_path(SYS_PLUGIN_PATH) //plugin folder
        ),
        // twitter bootstrap form twig templates
        'twig.form.templates' => array('form_div_layout.html.twig', 'default/form/form_custom_template.tpl'),
        'twig.options' => array(
            'debug' => $app['debug'],
            'charset' => 'utf-8',
            'strict_variables' => false,
            'autoescape' => false,
            'cache' => $app['debug'] ? false : $app['twig.cache.path'],
            'optimizations' => -1, // turn on optimizations with -1
        )
    )
);

// Setting Twig options
$app['twig'] = $app->share(
    $app->extend('twig', function ($twig) {
        $twig->addFilter('get_lang', new Twig_Filter_Function('get_lang'));
        $twig->addFilter('get_path', new Twig_Filter_Function('api_get_path'));
        $twig->addFilter('get_setting', new Twig_Filter_Function('api_get_setting'));
        $twig->addFilter('var_dump', new Twig_Filter_Function('var_dump'));
        $twig->addFilter('return_message', new Twig_Filter_Function('Display::return_message_and_translate'));
        $twig->addFilter('display_page_header', new Twig_Filter_Function('Display::page_header_and_translate'));
        $twig->addFilter(
            'display_page_subheader',
            new Twig_Filter_Function('Display::page_subheader_and_translate')
        );
        $twig->addFilter('icon', new Twig_Filter_Function('Template::get_icon_path'));
        $twig->addFilter('format_date', new Twig_Filter_Function('Template::format_date'));

        return $twig;
    })
);

// Developer tools

if (is_writable($app['sys_temp_path'])) {
    if ($app['debug'] && $app['show_profiler']) {
        // Adding Symfony2 web profiler (memory, time, logs, etc)
        $app->register(
            $p = new Silex\Provider\WebProfilerServiceProvider(),
            array(
                'profiler.cache_dir' => $app['profiler.cache_dir'],
            )
        );
        $app->mount('/_profiler', $p);
        // PHP errors for cool kids
        $app->register(new Whoops\Provider\Silex\WhoopsServiceProvider);
    }
}

// Pagerfanta settings (Pagination using Doctrine2, arrays, etc)
use FranMoreno\Silex\Provider\PagerfantaServiceProvider;
$app->register(new PagerfantaServiceProvider());

// Custom route params see https://github.com/franmomu/silex-pagerfanta-provider/pull/2
//$app['pagerfanta.view.router.name']
//$app['pagerfanta.view.router.params']

$app['pagerfanta.view.options'] = array(
    'routeName'     => null,
    'routeParams'   => array(),
    'pageParameter' => '[page]',
    'proximity'     => 3,
    'next_message'  => '&raquo;',
    'prev_message'  => '&laquo;',
    'default_view'  => 'twitter_bootstrap' // the pagination style
);

// Registering Menu service provider (too gently creating menus with the URLgenerator provider)
$app->register(new \Knp\Menu\Silex\KnpMenuServiceProvider());

// @todo use a app['image_processor'] setting
define('IMAGE_PROCESSOR', 'gd'); // imagick or gd strings

// Setting the Imagine service provider to deal with image transformations used in social group.
$app->register(new Grom\Silex\ImagineServiceProvider(), array(
    'imagine.factory' => 'Gd'
));

// Prompts Doctrine SQL queries using Monolog.

$app['dbal_logger'] = $app->share(function() {
    //return new Doctrine\DBAL\Logging\DebugStack();
});

if ($app['debug']) {
    /*$logger = $app['dbal_logger'];
    $app['db.config']->setSQLLogger($logger);
    $app->after(function() use ($app, $logger) {
        // Log all queries as DEBUG.
        foreach ($logger->queries as $query) {
            $app['monolog']->debug(
                $query['sql'],
                array(
                    'params' => $query['params'],
                    'types'  => $query['types'],
                    'executionMS' => $query['executionMS']
                )
            );
        }
    });*/
}

// Email service provider
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => array(
        'host' => isset($platform_email['SMTP_HOST']) ? $platform_email['SMTP_HOST'] : null,
        'port' => isset($platform_email['SMTP_PORT']) ? $platform_email['SMTP_PORT'] : null,
        'username' => isset($platform_email['SMTP_USER']) ? $platform_email['SMTP_USER'] : null,
        'password' => isset($platform_email['SMTP_PASS']) ? $platform_email['SMTP_PASS'] : null,
        'encryption' => null,
        'auth_mode' => null
    )
));

// Mailer
$app['mailer'] = $app->share(function ($app) {
    return new \Swift_Mailer($app['swiftmailer.transport']);
});

// Assetic service provider.

if ($app['assetic.enabled']) {

    $app->register(new SilexAssetic\AsseticServiceProvider(), array(
        'assetic.options' => array(
            'debug'            => $app['debug'],
            'auto_dump_assets' => $app['assetic.auto_dump_assets'],
        )
    ));

    // Less filter
    $app['assetic.filter_manager'] = $app->share(
        $app->extend('assetic.filter_manager', function($fm, $app) {
            $fm->set('lessphp', new Assetic\Filter\LessphpFilter());

            return $fm;
        })
    );

    $app['assetic.asset_manager'] = $app->share(
        $app->extend('assetic.asset_manager', function($am, $app) {
            $am->set('styles', new Assetic\Asset\AssetCache(
                new Assetic\Asset\GlobAsset(
                    $app['assetic.input.path_to_css'],
                    array($app['assetic.filter_manager']->get('lessphp'))
                ),
                new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));

            $am->get('styles')->setTargetPath($app['assetic.output.path_to_css']);

            $am->set('scripts', new Assetic\Asset\AssetCache(
                new Assetic\Asset\GlobAsset($app['assetic.input.path_to_js']),
                new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('scripts')->setTargetPath($app['assetic.output.path_to_js']);

            return $am;
        })
    );
}


// Gaufrette service provider (to manage files/dirs) (not used yet)
/*
use Bt51\Silex\Provider\GaufretteServiceProvider\GaufretteServiceProvider;
$app->register(new GaufretteServiceProvider(), array(
    'gaufrette.adapter.class' => 'Local',
    'gaufrette.options' => array(api_get_path(SYS_DATA_PATH))
));
*/

// Use Symfony2 filesystem instead of custom scripts
$app->register(new Neutron\Silex\Provider\FilesystemServiceProvider());

/** Chamilo service provider */

class ChamiloServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // Template class
        $app['template'] = $app->share(function () use ($app) {
            $template = new Template($app);
            return $template;
        });

        $app['paths'] = $app->share(function () use ($app) {
            return array(
                //'root_web' => $app['root_web'],
                'root_sys' => $app['root_sys'],
                'sys_data_path' => $app['sys_data_path'],
                'sys_config_path' => $app['sys_config_path'],
                'sys_temp_path' => $app['sys_temp_path'],
                'sys_log_path' => $app['sys_log_path']
            );
        });

        // Chamilo data filesystem
        $app['chamilo.filesystem'] = $app->share(function () use ($app) {
            $filesystem = new ChamiloLMS\Component\DataFilesystem\DataFilesystem($app['paths'], $app['filesystem']);
            return $filesystem;
        });

        // Page controller class
        $app['page_controller'] = $app->share(function () use ($app) {
            $pageController = new PageController($app);
            return $pageController;
        });

        // Mail template generator
        $app['mail_generator'] = $app->share(function () use ($app) {
            $mailGenerator = new ChamiloLMS\Component\Mail\MailGenerator($app['twig'], $app['mailer']);
            return $mailGenerator;
        });

        // Database
        $app['database'] = $app->share(function () use ($app) {
            $db = new Database($app['db'], $app['dbs']);
            return $db;
        });
    }

    public function boot(Application $app)
    {

    }
}

// Registering Chamilo service provider
$app->register(new ChamiloServiceProvider(), array());

// Controller as services definitions see
$app['pages.controller'] = $app->share(
    function () use ($app) {
        return new PagesController($app['pages.repository']);
    }
);

$app['index.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\IndexController();
    }
);

$app['legacy.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\LegacyController();
    }
);

$app['userPortal.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\UserPortalController();
    }
);

$app['learnpath.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\LearnpathController();
    }
);

$app['course_home.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\CourseHomeController();
    }
);

$app['course_home.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\CourseHomeController();
    }
);

$app['introduction_tool.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\IntroductionToolController();
    }
);

$app['certificate.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\CertificateController();
    }
);

$app['user.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\UserController();
    }
);

$app['news.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\NewsController();
    }
);

$app['editor.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\EditorController();
    }
);

$app['question_manager.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\Admin\QuestionManager\QuestionManagerController();
    }
);

$app['exercise_manager.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\ExerciseController();
    }
);

$app['role.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\Admin\Administrator\RoleController($app);
    }
);

$app['model_ajax.controller'] = $app->share(
    function () use ($app) {
        return new ChamiloLMS\Controller\ModelAjaxController();
    }
);

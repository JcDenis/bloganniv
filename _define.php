<?php
/**
 * @file
 * @brief       The plugin bloganniv definition
 * @ingroup     bloganniv
 *
 * @defgroup    bloganniv Plugin bloganniv.
 *
 * Show number of days before and after a given date.
 *
 * @author      Fran6t (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

$this->registerModule(
    'Blog Anniv',
    'Show number of days before and after a given date',
    'Fran6t, Pierre Van Glabeke and Contributors',
    '2.4',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/issues',
        'details'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/src/branch/master/README.md',
        'repository'  => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/raw/branch/master/dcstore.xml',
    ]
);

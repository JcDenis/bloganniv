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
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-02-24T23:31:12+00:00',
    ]
);

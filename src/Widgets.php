<?php

declare(strict_types=1);

namespace Dotclear\Plugin\bloganniv;

use DateTimeImmutable;
use DateTimeZone;
use Dotclear\App;
use Exception;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

/**
 * @brief       bloganniv widgets class.
 * @ingroup     bloganniv
 *
 * @author      Fran6t (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        $w->create(
            My::id(),
            My::name(),
            self::parseWidget(...),
            null,
            __('Counting the number of days before and after a particular date')
        )
        ->addTitle(My::name())
        ->setting('ftdatecrea', __('Born Date (dd/mm/yyyy) or blank:'), '')
        ->setting('dispyearborn', __('Display Born Date'), 1, 'check')
        ->setting('dispyear', __('Display Year(s) Old'), 1, 'check')
        ->addHomeOnly()
        ->addContentOnly()
        ->addClass()
        ->addOffline();
    }

    public static function parseWidget(WidgetsElement $w): string
    {
        if ($w->__get('offline')
            || !$w->checkHomeOnly(App::url()->getType())
            || !App::blog()->isDefined()
        ) {
            return '';
        }

        // Dates in blog time zone
        try {
            $tz = new DateTimeZone(is_string($tz = App::blog()->settings()->get('system')->get('blog_timezone')) && $tz !== '' ? $tz : 'UTC');
        } catch (Exception) {
            $tz = new DateTimeZone('UTC');
        }
        $today = new DateTimeImmutable('today', $tz);

        // Born date from widget setting, or blog creation date if empty or invalid
        $born       = null;
        $ftdatecrea = is_string($w->__get('ftdatecrea')) ? trim($w->__get('ftdatecrea')) : '';
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $ftdatecrea, $m) && checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            $born = (new DateTimeImmutable('today', $tz))->setDate((int) $m[3], (int) $m[2], (int) $m[1]);
        }
        if ($born === null) {
            $born = (new DateTimeImmutable('@' . App::blog()->creadt()))->setTimezone($tz)->setTime(0, 0);
        }

        if ($born > $today) {
            // Future date: not born yet, count days until born date
            $nbreannee = 0;
            $next      = $born;
        } else {
            $nbreannee = $born->diff($today)->y;
            $next      = $born->setDate((int) $today->format('Y'), (int) $born->format('m'), (int) $born->format('d'));
            if ($next < $today) {
                $next = $next->modify('+1 year');
            }
        }
        // Count calendar days, not seconds, to be safe with daylight saving time
        $nbrejours = (int) $today->diff($next)->days;

        return $w->renderDiv(
            (bool) $w->__get('content_only'),
            My::id() . (is_string($w->__get('class')) ? ' ' . $w->__get('class') : ''),
            '',
            (is_string($w->__get('title')) && !empty($w->__get('title')) ? $w->renderTitle(Html::escapeHTML($w->__get('title'))) : '') .
            '<ul>' .
            ($w->__get('dispyearborn') ? '<li>' . __('Born:') . ' <span class="annivne">' . $born->format('d/m/Y') . '</span></li>' : '') .
            ($w->__get('dispyear') ? '<li>' . __('Age:') . ' ' . sprintf(__('%s year', '%s years', $nbreannee), '<span class="annivan">' . $nbreannee . '</span>') . '</li>' : '') .
            '<li>' . ($nbrejours === 0 ?
                __('Birthday today') :
                __('Birthday in') . ' ' . sprintf(__('%s day', '%s days', $nbrejours), '<span class="annivjrs">' . $nbrejours . '</span>')
            ) . '</li>' .
            '</ul>'
        );
    }
}

<?php
/**
 * @brief bloganniv, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Fran6t, Pierre Van Glabeke and Contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\bloganniv;

use dcCore;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        $w->create(
            My::id(),
            My::name(),
            [self::class, 'parseWidget'],
            null,
            __('Counting the number of days before and after a particular date')
        )
        ->addTitle('')
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
        if ($w->__get('offline') || !$w->checkHomeOnly(dcCore::app()->url->type)) {
            return '';
        }

        // nullsafe PHP < 8.0
        if (is_null(dcCore::app()->blog)) {
            return '';
        }

        $ftdatecrea = $w->__get('ftdatecrea');
        //Si la date est vide nous recherchons la date en base
        if (strlen(rtrim($ftdatecrea)) == 0) {
            $jour       = date('d', dcCore::app()->blog->creadt);
            $mois       = date('m', dcCore::app()->blog->creadt);
            $annee      = date('Y', dcCore::app()->blog->creadt);
            $ftdatecrea = date('d/m/Y', dcCore::app()->blog->creadt);
        } else {
            [$jour, $mois, $annee] = explode('/', $ftdatecrea);
        }

        $jour      = (int) $jour;
        $mois      = (int) $mois;
        $annee     = (int) $annee;
        $nbrejours = 0;
        $nbreannee = 0;
        // Test si la date est valide
        if (@checkdate($mois, $jour, $annee)) {
            // Ok nous pouvons calculer la date anniversaire et le nombre de jours restant avant

            //Extraction des données
            $jour2  = (int) date('d');
            $mois2  = (int) date('m');
            $annee2 = (int) date('Y');

            //Calcul des timestamp
            $timestamp1 = mktime(0, 0, 0, $mois, $jour, $annee2); // La date anniversaire cette année
            $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $annee2);
            //Affichage du nombre de jour

            //je regarde si la date anniv n'est pas passé
            if (($timestamp2 - $timestamp1) > 0) {
                $timestamp1 = mktime(0, 0, 0, $mois, $jour, $annee2 + 1);
                $nbrejours  = round(abs(mktime(0, 0, 0, $mois2, $jour2, $annee2) - $timestamp1) / 86400);
                $nbreannee  = abs($annee2 - $annee);
            } else {
                $nbrejours = abs($timestamp2 - $timestamp1) / 86400;
                $nbreannee = abs($annee2 - $annee - 1);
            }
        // abs($timestamp2 - $timestamp1)/(86400*7); //Affichage du nombre de semaine : 3.85
        } else {
            // date invalide
            return '';
        }

        return $w->renderDiv(
            (bool) $w->__get('content_only'),
            My::id() . ' ' . $w->__get('class'),
            '',
            ($w->__get('title') ? $w->renderTitle(Html::escapeHTML($w->__get('title'))) : '') .
            '<ul>' .
            ($w->__get('dispyearborn') ? '<li>' . __('Born:') . ' <span class="annivne">' . $ftdatecrea . '</span></li>' : '') .
            ($w->__get('dispyear') ? '<li>' . __('Age:') . ' <span class="annivan">' . $nbreannee . '</span> ' . __('year(s)') . '</li>' : '') .
            '<li>' . __('Birthday in') . ' <span class="annivjrs">' . $nbrejours . '</span> ' . __('day(s)') . '</li>' .
            '</ul>'
        );
    }
}

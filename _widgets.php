<?php
/**
 * @brief bloganniv, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Fran6t, Pierre Van Glabeke and Contributors
 *
 * @copyright Jean-Crhistian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {
    return null;
}

dcCore::app()->addBehavior('initWidgets', ['blogAnnivWidgets', 'initWidgets']);

class blogAnnivWidgets
{
    public static function initWidgets($w)
    {
        $w->create(
            'blogAnniv',
            __('Blog Anniv'),
            ['blogAnnivWidgets', 'BlogAnnivWidget'],
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

    public static function BlogAnnivWidget($w)
    {
        if ($w->offline) {
            return null;
        }

        if (!$w->checkHomeOnly(dcCore::app()->url->type)) {
            return null;
        }

        $ftdatecrea = $w->ftdatecrea;
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
            $w->content_only,
            'bloganniv ' . $w->class,
            '',
            ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '') .
            '<ul>' .
            ($w->dispyearborn ? '<li>' . __('Born:') . ' <span class="annivne">' . $ftdatecrea . '</span></li>' : '') .
            ($w->dispyear ? '<li>' . __('Age:') . ' <span class="annivan">' . $nbreannee . '</span> ' . __('year(s)') . '</li>' : '') .
            '<li>' . __('Birthday in') . ' <span class="annivjrs">' . $nbrejours . '</span> ' . __('day(s)') . '</li>' .
            '</ul>'
        );
    }
}

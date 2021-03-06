<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Field_radio extends Jojo_Field
{
    var $fd_size;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->options = array();
    }

    function displayedit()
    {
        global $smarty;

        $this->options = explode("\n", $this->fd_options);

        $vals        = array();
        $displayvals = array();
        $extras      = array();
        $allextras   = array();

        foreach ($this->options as $option) {
            $option2     = str_replace("\r", '', $option); //hack hack
            $optionarray = explode(':', $option2);

            if (count($optionarray) == 3) {
                $val        = $optionarray[0];
                $displayval =  Jojo::either($optionarray[1], $optionarray[0]);
                $arr = explode('.', $optionarray[2]);
                foreach ($arr as $extra) {
                    $allextras[] = $extra;
                }
            } elseif (count($optionarray) == 2) {
                $val        = $optionarray[0];
                $displayval =  Jojo::either($optionarray[1], $optionarray[0]);
                $extra      = false;
            } else {
                $val        = $optionarray[0];
                $displayval = $optionarray[0];
                $extra      = false;
            }
            $vals[]         = $val;
            $displayvals[]  = $displayval;
            $extras[]       = $extra;
        }

        $smarty->assign('vals',        $vals);
        $smarty->assign('displayvals', $displayvals);
        $smarty->assign('extras',      $extras);
        $smarty->assign('allextras',   $allextras);
        $smarty->assign('fd_help',     htmlentities($this->fd_help));
        $smarty->assign('readonly',    $this->fd_readonly);
        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_field',    $this->fd_field);

        return  $smarty->fetch('admin/fields/radio.tpl');
    }

    function getHiddenFields()
    {
        $hiddenfields = array();
        $options = explode("\n", $this->fd_options);
        foreach ($options as $option) {
            $option2     = str_replace("\r", '', $option); //hack hack
            $optionarray = explode(':', $option2);
            if (!empty($optionarray[2]) && ($this->value != $optionarray[0])) {
                $arr = explode('.', $optionarray[2]);
                foreach ($arr as $hiddenfield) {
                    $hiddenfields[] = $hiddenfield;
                }
            }
        }
        return $hiddenfields;
    }

    function displayJs()
    {
        global $smarty;
        return $smarty->fetch('admin/fields/radio_js.tpl');
    }
}
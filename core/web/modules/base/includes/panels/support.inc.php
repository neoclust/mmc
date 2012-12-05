<?php
/**
 * (c) 2004-2007 Linbox / Free&ALter Soft, http://linbox.com
 * (c) 2007-2012 Mandriva, http://www.mandriva.com
 *
 * This file is part of Mandriva Management Console (MMC).
 *
 * MMC is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * MMC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MMC; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

include_once("modules/dashboard/includes/panel.class.php");

$options = array(
    "class" => "SupportPanel",
    "id" => "support",
    "refresh" => 14400,
    "title" => _("Your product"),
);

class SupportPanel extends Panel {

    function display_content() {
        echo '<p><strong>' . $this->data['product_name'][0] . '</strong></p>';
        echo '<p>' . _("Support :") . ' <a href="mailto:' . $this->data['support_mail']. '">' . $this->data['support_mail'] . '</a></p>';
        echo '<div class="subpanel"><h4>' . _("Licence") . '</h4>';
        $this->display_licence("users", _("Users : "));
        $this->display_licence("computers", _("Computers : "));
    }

    function display_licence($type, $title) {
        if ($this->data[$type] > 0) {
            if ($this->data['too_much_' . $type])
                echo '<p class="alert alert-error">';
            else
                echo '<p class="alert alert-success">';
            echo $title . ' <strong>' . $this->data['installed_' . $type] . ' / ' . $this->data[$type] . '</strong>';
        }
    }
}
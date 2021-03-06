<?php
/**
 * (c) 2004-2007 Linbox / Free&ALter Soft, http://linbox.com
 * (c) 2007 Mandriva, http://www.mandriva.com/
 *
 * $Id$
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

require('modules/msc/includes/commands_xmlrpc.inc.php');

$cmd_id = isset($_GET['cmd_id']) ? $_GET['cmd_id'] : '';

if (strlen($cmd_id)) {
    $status = get_command_on_group_status($cmd_id);
    $title = get_command_on_host_title($cmd_id);
    if ($status['total'] == 1) {
        $title = sprintf(_T("Command '%s' state concerning <b>one</b> computer", "msc"), $title);
    } else {
        $title = sprintf(_T("Command '%s' state concerning <b>%s</b> computers", "msc"), $title, $status['total']);
    }
} elseif (strlen($_GET['bundle_id'])) {
    $status = get_command_on_bundle_status($_GET['bundle_id']);
    $bdl = bundle_detail($_GET['bundle_id']);
    if ($bdl[0] == null) {
        print _T("error : the bundle_id given does not exists in the database.", 'msc');
    } else {
        $cmd_nb = count($bdl[1]);
        $machines_nb = $status['total'] / count($bdl[1]);
        if ($cmd_nb == 1) {
            if ($machines_nb == 1) { $title = sprintf(_T("Bundle '%s' state concerning <b>one</b> command on <b>one</b> computer", "msc"), $bdl[0]['title']); }
            else { $title = sprintf(_T("Bundle '%s' state concerning <b>one</b> command on <b>%s</b> computers", "msc"), $bdl[0]['title'], $machines_nb); }
        } else {
            if ($machines_nb == 1) { $title = sprintf(_T("Bundle '%s' state concerning <b>%s</b> commands on <b>one</b> computer", "msc"), $bdl[0]['title'], $cmd_nb); }
            else { $title = sprintf(_T("Bundle '%s' state concerning <b>%s</b> commands on <b>%s</b> computers", "msc"), $bdl[0]['title'], $cmd_nb, $machines_nb); }
        }
    }
} else {
    print _T("error : cmd_id or bundle_id must be given", "msc");
}

if (strlen($_GET['bundle_id']) && !strlen($cmd_id)) {
    /* Change labels when displaying a bundle summary */
    $labels = array(
        array('success', array(
            _T("<b>No</b> package installation was successful", "msc"),
            _T("<b>One</b> package installation was successful", "msc"),
            _T('<b>%s</b> packages installation were successful', 'msc'),
            _T('Success: %percent% (%d packages)', 'msc')
        )),
        array('stopped', array(
            _T("<b>No</b> package installation is stopped", "msc"),
            _T("<b>One</b> package installation is stopped", "msc"),
            _T('<b>%s</b> packages installation are stopped', 'msc'),
            _T('Stopped: %percent% (%d packages)', 'msc')
        )),
        array('paused', array(
            _T("<b>No</b> package installation is paused", "msc"),
            _T("<b>One</b> package installation is paused", "msc"),
            _T('<b>%s</b> packages installation are paused', 'msc'),
            _T('Paused: %percent% (%d packages)', 'msc')
        )),
        array('running', array(
            _T("<b>No</b> package installation is in progress", "msc"),
            _T("<b>One</b> package installation is in progress", "msc"),
            _T('<b>%s</b> packages installation are in progress', 'msc'),
            _T('In progress: %percent% (%d packages)', 'msc')
        )),
        array('failure', array(
            _T('<b>No</b> package installation failed', 'msc'),
            _T('<b>One</b> package installation failed', 'msc'),
            _T('<b>%s</b> packages installation failed', 'msc'),
            _T('Failed: %percent% (%d packages)', 'msc')
        )),
        );
} else {
    $labels = array(
        array('success', array(
            _T('<b>No</b> computer was successfully deployed', 'msc'),
            _T('<b>One</b> computer was successfully deployed', 'msc'),
            _T('<b>%s</b> computers were successfully deployed', 'msc'),
            _T('Success: %percent% (%d computers)', 'msc') // Pie chart legend text
        )),
        array('stopped', array(
            _T('<b>No</b> computer is stopped', 'msc'),
            _T('<b>One</b> computer is stopped', 'msc'),
            _T('<b>%s</b> computers are stopped', 'msc'),
            _T('Stopped: %percent% (%d computers)', 'msc') // Pie chart legend text
        )),
        array('paused', array(
            _T('<b>No</b> computer is paused', 'msc'),
            _T('<b>One</b> computer is paused', 'msc'),
            _T('<b>%s</b> computers are paused', 'msc'),
            _T('Paused: %percent% (%d computers)', 'msc') // Pie chart legend text
        )),
        array('running', array(
            _T('<b>No</b> computer is running a deployment', 'msc'),
            _T('<b>One</b> computer is running a deployment', 'msc'),
            _T('<b>%s</b> computers are running a deployment', 'msc'),
            _T('In progress: %percent% (%d computers)', 'msc') // Pie chart legend text
        )),
        array('failure', array(
            _T('<b>No</b> computer failed to deploy', 'msc'),
            _T('<b>One</b> computer failed to deploy', 'msc'),
            _T('<b>%s</b> computers failed to deploy', 'msc'),
            _T('Failed: %percent% (%d computers)', 'msc') // Pie chart legend text
        )),
        );
}

$verbs = array(
    'running'=>array(_T('is', 'msc'), _T('are', 'msc')),
    'failure'=>array(_T('has', 'msc'), _T('have', 'msc')),
    'failure#over_timed'=>array(_T('is', 'msc'), _T('are', 'msc'))
);
$slabels = array(
    'success'=>array(),
    'stopped'=>array(),
    'paused'=>array(),
    'running'=>array(
        array('wait_up', _T('waiting to upload', 'msc'), 'sec_up', _T('(with %s already try)', 'msc')),
        array('run_up', _T('uploading', 'msc')),
        array('wait_ex', _T('waiting to execute', 'msc'), 'sec_ex', _T('(with %s already try)', 'msc')),
        array('run_ex', _T('executing', 'msc')),
        array('wait_rm', _T('waiting to suppress', 'msc'), 'sec_rm', _T('(with %s already try)', 'msc')),
        array('run_rm', _T('suppressing', 'msc')),
        array('wait_inv', _T('waiting to inventory', 'msc'), 'sec_inv', _T('(with %s already try)', 'msc')),
        array('run_inv', _T('inventoring', 'msc')),
    ),
    'failure'=>array(
        array('fail_up', _T('failed during upload', 'msc'), 'conn_up', _T('(with %s being unreachable)', 'msc')),
        array('fail_ex', _T('failed during execution', 'msc'), 'conn_ex', _T('(with %s being unreachable)', 'msc')),
        array('fail_rm', _T('failed during suppression', 'msc'), 'conn_rm', _T('(with %s being unreachable)', 'msc')),
        array('fail_inv', _T('failed during suppression', 'msc'), 'conn_inv', _T('(with %s being unreachable)', 'msc')),
        array('over_timed', _T('out of the valid period of execution', 'msc'))
    )

);

function export_csv($cmd_id, $bundle_id, $state) {
    return " <td><a href='".
        urlStr("base/computers/statuscsv", array('cmd_id'=>$cmd_id, 'bundle_id'=>$bundle_id, 'state'=>$state)).
        "'><img src='modules/msc/graph/csv.png' alt='export csv'/></a></td>";
}

?>
<table width='100%'> <tr><td colspan='3'>
<h3><?php echo  $title ?>&nbsp;
</h3></td><td>
<a href='<?php echo  urlStr("base/computers/statuscsv", array('cmd_id'=>$cmd_id, 'bundle_id'=>$_GET['bundle_id'])) ?>'><img src='modules/msc/graph/csv.png' alt='export csv'/></a>
</td></tr>
 <?php

/*
 * If it's an action on a group:
 * -----------------------------
 *
 * machineStateNumber declaration
 * used like this to store number of machines by state:
 *      $machineStateNumber[state] = array(
 *          "number => "int(number),
 *          "percent" => int(percent)
 *
 *  states are:
 *      success
 *      running
 *      failure
 *      paused
 *      stopped
 *
 *  If it's a bundle on a group:
 *  ----------------------------
 *  
 *  bundleStatus declaration
 *  used in the same manner of machineStateNumber
 *
 *  But in bundles, machines can have packages with several status
 *  
 *  For a successfull status, all packages of the bundle must have success status.
 *
 */
$machineStateNumber = array();
$bundleStatus = array();

foreach ($labels as $l) {
    $s = $status[$l[0]];

    if (strlen($cmd_id)) { // If it's an action on a group
        $urlArray = array(
            'pieGroupStatus' => $l[0],
            'cmd_id' => $cmd_id,
            "tab" => "tabsta",
            "type" => 0,
        );
        $machineStateNumber[$l[0]] = array(
            "number" => $s['total'][0],
            "percent" => $s['total'][1],
            "url" => urlStr("base/computers/computersgroupcreator", $urlArray),
            "legend" => $l[1][3],
        );
    }
    elseif(strlen($_GET['bundle_id'])) { // If it's a bundle on a group
        $bundleStatus[$l[0]] = $s['machineNames'];
    }
    
    if ($s['total'][0] == '0') {
        //print "<tr><td colspan='3'>".$l[1][0]." (".$s['total'][1]."%)</td><td><img src='modules/msc/graph/nocsv.png' alt='no csv export possible'/></td></tr>";
    } elseif ($s['total'][0] == '1') {
        print "<tr><td colspan='3'>".$l[1][1]."</td>".export_csv($cmd_id, $_GET['bundle_id'], $l[0])."</tr>";
    } else {
        print "<tr><td colspan='3'>".sprintf($l[1][2], $s['total'][0])."</td>".export_csv($cmd_id, $_GET['bundle_id'], $l[0])."</tr>";
    }

    foreach ($slabels[$l[0]] as $sl) {
        $ss = $status[$l[0]][$sl[0]];
        if ($ss[0] != '0') {
            print "<tr><td>&nbsp;&nbsp;&nbsp;</td><td colspan='2'>";
        }
        $verb = $verbs[$l[0]];
        if (in_array($l[0]."#".$sl[0], array_keys($verbs))) {
            $verb = $verbs[$l[0]."#".$sl[0]];
        }
        if ($ss[0] == '0') {
            //print _T('None', 'msc')." ".$verb[0]." ";
        } elseif ($ss[0] == '1') {
            print _T('One', 'msc')." ".$verb[0]." ";
        } else {
            print $ss[0]." ".$verb[1]." ";
        }
        if ($ss[0] != '0') {
            print $sl[1];
        }
        if (count($sl) == 4 and $ss[0] != '0') {
            print " ".sprintf($sl[3], $status[$l[0]][$sl[2]][0]);
        }
        if ($ss[0] == 0) {
            //print "</td><td><img src='modules/msc/graph/nocsv.png' alt='no csv export possible'/></td></tr>";
        } else {
            print "</td>".export_csv($cmd_id, $_GET['bundle_id'], $sl[0])."</tr>";
        }
    }
}

/*
 *  bundle treatment:
 *  -----------------
 *
 *  machines can be stored in more than one status (one package can be success, and another in fail state)
 *
 *  If one package is failed, bundle is in fail status
 *  If no failed, but one running package, bundle is in running status
 *  For package paused => running status
 *  For package stopped => failed status
 */

if(strlen($_GET['bundle_id']) and !isset($_GET['tab'])) {
    #print "<pre>";
    #print_r(isset($_GET['tab']));
    #print "</pre>";
    // merge stopped and paused status
    $bundleStatus['failure'] = array_merge($bundleStatus['failure'], $bundleStatus['stopped']);
    $bundleStatus['running'] = array_merge($bundleStatus['running'], $bundleStatus['paused']);

    // If one package is in failure state, unset corresponding machine from other states
    foreach ($bundleStatus['failure'] as $failKey => $failValue) {
        foreach ($bundleStatus['success'] as $successKey => $successValue) {
            if ($failValue == $successValue) unset($bundleStatus['success'][$successKey]);
        }
        foreach ($bundleStatus['running'] as $runningKey => $runningValue) {
            if ($failValue == $runningValue) unset ($bundleStatus['running'][$runningKey]);
        }
    }

    // If a machine have both running and success packages, machine has running status
    foreach ($bundleStatus['running'] as $runningKey => $runningValue) {
        foreach ($bundleStatus['success'] as $successKey => $successValue) {
            if ($runningValue == $successValue) unset ($bundleStatus['success'][$successKey]);
        }
    }

    $totalMachineNumber = 0;
    foreach ($bundleStatus as $key => $value) {
        $totalMachineNumber += count($value);
    }

    $i = 0;
    foreach ($bundleStatus as $key => $value) {
        $urlArray = array(
            'pieGroupStatus' => $key,
            'bundle_id' => $_GET['bundle_id'],
            "tab" => "tabsta",
            "type" => 0,
        );
        $machineStateNumber[$key] = array(
            "number" => count($value),
            "url" => urlStr("base/computers/computersgroupcreator", $urlArray),
            "legend" => $labels[$i][1][3],
        );
        $i++;
    }
}

$jsonMachineStateNumber = json_encode($machineStateNumber);
echo <<< MSC
</table>

    <div id="msc-graphs"></div>
    <script type="text/javascript">
var machineStateNumber = $jsonMachineStateNumber,
        r = Raphael("msc-graphs", 400, 200),
        radius = 50,
        x = 90,
        y = 90;

    var data = [];
    var legend = [];
    var colors = [];
    var href = [];

    if (machineStateNumber.success.number) {
        data.push(machineStateNumber.success.number);
        legend.push(machineStateNumber.success.legend.replace('%d', machineStateNumber.success.number));
        href.push(machineStateNumber.success.url.replace(/&amp;/g, '&'));
        colors.push("#73d216");
    }
    if (machineStateNumber.stopped.number) {
        data.push(machineStateNumber.stopped.number);
        legend.push(machineStateNumber.stopped.legend.replace('%d', machineStateNumber.stopped.number));
        href.push(machineStateNumber.stopped.url.replace(/&amp;/g, '&'));
        colors.push("#111111");
    }
    if (machineStateNumber.paused.number) {
        data.push(machineStateNumber.paused.number);
        legend.push(machineStateNumber.paused.legend.replace('%d', machineStateNumber.paused.number));
        href.push(machineStateNumber.paused.url.replace(/&amp;/g, '&'));
        colors.push("#ff9c00");
    }
    if (machineStateNumber.running.number) {
        data.push(machineStateNumber.running.number);
        legend.push(machineStateNumber.running.legend.replace('%d', machineStateNumber.running.number));
        href.push(machineStateNumber.running.url.replace(/&amp;/g, '&'));
        colors.push("#0066ff");
    }
    if (machineStateNumber.failure.number) {
        data.push(machineStateNumber.failure.number);
        legend.push(machineStateNumber.failure.legend.replace('%d', machineStateNumber.failure.number));
        href.push(machineStateNumber.failure.url.replace(/&amp;/g, '&'));
        colors.push("#ef2929");
    }

    data = getPercentageData(data);

    // put percentage values in legend
    for (var i = 0; i < data.length; i++) {
        legend[i] = legend[i].replace('%percent', data[i]);
    }

    var pie = r.piechart(x, y, radius, data,
                     {legend: legend,
                      legendpos: "east",
                      colors: colors, 
                      href: href});
    pie.hover(function () {
        this.sector.stop();
        this.sector.animate({ transform: 's1.1 1.1 ' + this.cx + ' ' + this.cy }, 800, "elastic");

        if (this.label) {
            this.label[0].stop();
            this.label[0].attr({ r: 7.5 });
            this.label[1].attr({ "font-weight": 800 });
        }
    }, function () {
        this.sector.animate({ transform: 's1 1 ' + this.cx + ' ' + this.cy }, 800, "elastic");

        if (this.label) {
            this.label[0].animate({ r: 5 }, 500, "bounce");
            this.label[1].attr({ "font-weight": 400 });
        }
    });

    </script>
MSC;
?>

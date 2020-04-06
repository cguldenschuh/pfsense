<?php
/*
 * acpupsd.widget.php
 *
 * Copyright (c) 2020 Charles Guldenschuh
 * All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * Modeled after DHCP_leases.widget.php by Dave Field
 */

 require_once("functions.inc");
 require_once('system.inc');


 $awk = "/usr/bin/awk";
 $apcaccess = "/usr/local/sbin/apcaccess";

 /* Data we want from apcaccess */
 $datapat = "'/STATUS|LOADPCT|BCHARGE|TIMELEFT/'";

 /* Get the UPS info */
 $_gb = exec("{$apcaccess} | {$awk} {$datapat}}", $apc_data);

 $loadlevel = 0;
 $status = gettext("Unknown");
 $chargelevel = 0;
 $timeleft = 0;

 $_gb = exec("{$apcaccess} | {$awk} '/STATUS/ {print $3}'", $ts);
 $status = $ts[0];
 unset($ts);
 $_gb = exec("{$apcaccess} | {$awk} '/LOADPCT/ {print $3}'", $ts);
 $loadlevel = $ts[0];
 unset($ts);
 $_gb = exec("{$apcaccess} | {$awk} '/BCHARGE/ {print $3}'", $ts);
 $chargelevel = $ts[0];
 unset($ts);
 $_gb = exec("{$apcaccess} | {$awk} '/TIMELEFT/ {print $3}'", $ts);
 $timeleft = $ts[0];

 foreach ($apc_data as $dline) {
    print($dline);
    $data = explode(" ", $dline);
    switch ($data[0]) {
        case "STATUS":
            $status = $data[2];
            break;
        case "LOADPCT":
            $loadlevel = $data[2];
            break;
        case "BCHARGE":
            $chargelevel = $data[2];
            break;
        case "TIMELEFT":
            $timeleft = $data[2];
            break;
    }
  }
  switch ($status) {
    case "ONLINE":
        $status = "<b style=\"color:green\">" . gettext("Online") . "</b>"; break;
    case "CAL":
        $status = gettext("Calibrating"); break;
    case "ONBATT":
        $status = "<b style=\"color:red\">" . gettext("On battery") . "</b>";
        break;
    case "OVERLOAD":
        $status = "<b style=\"color:red\">" . gettext("Overload") . "</b>";
        break;
    case "LOWBATT":
        $status = "<b style=\"color:red\">" . gettext("Low battery") . "</b>";
        break;
    case "REPLACEBATT":
        $status = "<b style=\"color:red\">" . gettext("Replace battery") . "</b>";
        break;
  }
?>
<div>
<table class="table table-hover table-striped table-condensed">
    <tbody>
        <tr>
            <th style="width:25%"><?=gettext("Status");?></th>
            <td style="width:75%"><?php print($status);?></td>
        </tr>
        <tr>
            <th><?=gettext("Battery Charge");?></th>
            <td><?php print($chargelevel);?></td>
        </tr>
        <tr>
            <th><?=gettext("Time Left");?></th>
            <td><?php print($timeleft);?></td>
        </tr>
        <tr>
            <th><?=gettext("Load percentage");?></th>
            <td><?php print($loadlevel);?></td>
        </tr>
    </tbody>
</table>
</div>


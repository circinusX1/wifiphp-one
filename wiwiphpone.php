<?php
    class Ssh
    {
        private $connection;

        public function __construct($h,$u,$p)
        {
            $this->connection = ssh2_connect($h, 22);        
            ssh2_auth_password($this->connection, $u, $p);
        }

        function exec($cmd)
        {
            $stream = ssh2_exec($this->connection, $cmd);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $stream_err =  ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            while($line = fgets($stream_err)) { flush(); echo "<font color='red'>".$line."</font>\n"; }
            while($line = fgets($stream_out)) { flush(); echo $line."\n";}
            fclose($stream_out);
            fclose($stream_err);

        }

        function flush()
        {
            $stream = ssh2_exec($this->connection, "\r\n");
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $stream_err =  ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            while($line = fgets($stream_err)) { flush();}
            while($line = fgets($stream_out)) { flush();}
            fclose($stream_out);
            fclose($stream_err);
        }

        function shell($cmd, $errors=false)
        {
            $stream = ssh2_exec($this->connection, $cmd);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($stream_out, true);
            stream_set_blocking($stream_err, true);
            $err = stream_get_contents($stream_err);
            $ret = "";
            if($errors){
                if($err != null)
                    $ret .= "<font color='red'>".$err."</font>\n";
            }
            $ret .= stream_get_contents($stream_out);
            fclose($stream_out);
            fclose($stream_err);
            return $ret;
        }
    };

    if(isset($_GET['emac']) )
    {
        //print_r($_GET);
        //
        $ssh = new Ssh("localhost","s2w","s2w");
        $qry = "/usr/bin/wpa_passphrase {$_GET['name']} {$_GET['p']}";
//        echo $qry;
        $wpc = ($ssh->shell($qry));       
        if($wpc != null){
            file_put_contents("/etc/wpa_supplicant/wpa_supplicant.conf",$wpc); 
            $ssh->exec("sudo systemctl stop wpa_supplicant");
            $ssh->exec("sudo /sbin/wpa_supplicant -B -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf -Dwext");
            $ssh->exec("sudo /sbin/ifconfig {$_GET['w']} down");
            $ssh->exec("sudo /sbin/ifconfig {$_GET['w']} up");
            $ssh->exec("sudo /sbin/dhclient {$_GET['w']}");
            $wlan =  $ssh->shell("/sbin/iwconfig | grep ESSID | awk {'print $1}'");
            if($wlan == null)
            {
                echo "There is no wifi interface detected";
                die();
            }
            // find if there is an ip there
            $ip =  $ssh->shell("/sbin/ifconfig {$wlan} | grep 'inet' | grep 'netmask' | awk '{print $2}'");
            file_put_contents("/tmp/wifiphp.txt",$_GET['name']); 
            echo "Connected to {$ip}";
           
        }
        else{
            file_put_contents("/tmp/wifiphp.txt",""); 
            echo "Error {$qry}";
        }
        die();
    }

?>

<script
    src="http://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>

<style type = "text/css">
.wifipb{}
.wifikon
{
    border: 1px solid #000;
    cursor: pointer;
    width:100px;    
    display:inline-block;
}
.wifion
{
    border: 1px solid #000;
    width:200px;
    display:inline-block;
    background:#AFA;
}
</style>

<?php

    $ssh = new Ssh("localhost","s2w","s2w");

    $running = $ssh->shell("ps ax | grep supplic | grep -v grep");
    if($running == null)
    {
        $ssh->exec("sudo systemctl start wpa_supplicant");
        sleep(3);
    }
    $running = $ssh->shell("ps ax | grep supplic | grep -v grep");
    if($running == null)
    {
        echo "<font color='red'>Cannot start wpa_suplicant. F5 to refresh !</font>";
        die();
    }    
    // find out wlan
    $wlan =  $ssh->shell("/sbin/iwconfig | grep ESSID | awk {'print $1}'");
    if($wlan == null)
    {
        echo "There is no wifi interface detected. F5 to refresh !";
        die();
    }
    // find if there is an ip there
    $ssh->flush();    
    
    $cmd =     "/sbin/ifconfig {$wlan} | grep 'inet' | grep 'netmask' | awk '{print $2}'";
    echo ("<pre>");
    $ipaddr =  $ssh->shell($cmd);
    $ips = explode(' ',$ipaddr);
    if(count($ips))
    {
        for($i=0;$i<count($ips);$i++)
        {
            if($ips[$i] == 'inet')
            {
                $ipaddr = $ips[$i+1];
                break;
            }
        }
    }

    $keepgoing=false;
    $enabled=false;
    $qual=0;
    $name=null;
    $iscon=file_get_contents("/tmp/wifiphp.txt"); 
    $enc="off";
    $enc="<font color='red'>Open</font>";
    $keepgoing=false;
    $f="";

    // find on which we are connected
    $alines = explode("\n",$ssh->shell("/sbin/iwlist scanning"));
    $map = array();
    echo "<table width=800px' border='1' cellspacing='0' cellpading='0'><tr>".
         "<th width='30%'>Network</th><th width='20%'>Signal</th><th width='20%'>Security</th><th width='30%'>Status</th></tr>";
    foreach ($alines as $l)
    {
        if(strstr($l,"Cell"))
        {
            $enabled=false;
            $qual=0;
            $name=null;
            $enc="off";
            $enc="<font color='red'>Open Network</font>";
                $keepgoing=false;
                $pl=explode(" ", $l);
                $f=$pl[14];
        }
        if($keepgoing)
            continue;
        if(strstr($l,"Quality"))
        {
            $qual = $l;
        }
        else if(strstr($l,"ESSID"))
        {
            $ghi=strpos($l,"\"");
            $name=str_replace("\"","",substr($l,$ghi+1));
            //$name.=" ".$mac . " - ".$iscon ;
        }
        else if(strstr($l,"WPA"))
        {
            $enabled=true;
        }
        else if(strstr($l,"Encryption key"))
            $enc="WPA Secured";

        if($enabled && $name)
        {
        
            if(isset($map[$name]))
                continue;
            $map[$name]=1;

             echo "<tr><td>{$name}</td><td>";

            $perc = substr($qual, strpos($qual,"Quality=")+8);
            $perc = substr($perc, 0, strpos($perc,"/"));

            echo "<div class='meter-value' style='background-color:#393;width:{$perc}%;'>".
                  "{$perc}%</div>";
            echo "</td><td>{$enc}</td>";

            if($name==$iscon)
            {
                echo "<td><div class='wifion'>Connected: {$ipaddr}</div></td></tr>";
            }
            else if($perc==0)
            {
                echo "<td>0 Signal</td></tr>";
            }
            else
            {
                $fid = str_replace(":","_",$f);
                echo "<td width='40%'><div class='wifikon' id='{$fid}' >Connect</div>";
                    echo "<div class='hdefault' id='h_{$fid}'>";
                    echo "<li>Password:<input name='password'  id='p_{$fid}' value=''  size='12'>".
                         "<input hidden id='w_{$fid}' value='{$wlan}'>";
                    echo "<li><button class='wifipb' id='m_{$fid}' name='{$name}'>Connect...</button></div>";

                echo "</td></tr>";
            }
            $keepgoing=true;
       }
    }
    echo "</table>";

    $ssh->exec("sudo systemctl restart wpa_supplicant");
    
?>

<script>
$(document).ready(function() {

   $('.hdefault').toggle();


    $(".wifikon").click(function() {
        var id = "h_" + $(this).attr("id");
        $('#' + id).toggle();
    });


    $(".wifipb").click(function() {

        $(this).html("Connecting...");
        var pbid = $(this).attr('id');
        var emac = $(this).attr('id').substring(2); 
        var pid = "p_" + emac;
        var wid = "w_" + emac;
        var name = $(this).attr('name');
        var pass = $('#'+pid).val();       
        $.ajax({    async: true,
                    type: 'GET',
                    url: '/network.php?emac='+emac+'&name='+name+'&p='+pass+'&w='+wid,
                    success: function(result)
        {
            $('#'+pbid).html(result);
            $('#' +'h_'+ emac).toggle(200);
            location.reload();
        }});
    });


});
</script>

<!-- 

[Unit]
Description=WPA supplicant
Before=network.target
After=dbus.service
Wants=network.target

[Service]
Type=dbus
BusName=fi.epitest.hostap.WPASupplicant
#ExecStart=/sbin/wpa_supplicant -u -s -O /run/wpa_supplicant -c/etc/wpa_supplicant/wpa_supplicant.conf
#ExecStart=/sbin/wpa_supplicant -B -iwlan0 -c/etc/wpa_supplicant/wpa_supplicant.conf -Dwext

[Install]
WantedBy=multi-user.target
Alias=dbus-fi.epitest.hostap.WPASupplicant.service

-->


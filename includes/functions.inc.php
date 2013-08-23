<?php

function sendSSHCommand($server, $command) {

    if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
    // log in at server1.example.com on port 22
    if(!($con = ssh2_connect($server, 22))){
        return "fail: unable to establish connection\n";
    } else {
        // try to authenticate with username root, password secretpassword
        if(!ssh2_auth_password($con, SSH_USER, SSH_PASS)) {
            return "fail: unable to authenticate\n";
        } else {
            // execute a command
            $stream = ssh2_exec($con, $command);
        }
    }
    return "sent";
}

function getSSHResult($server, $command) {

    $data = "";
    if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
    // log in at server1.example.com on port 22
    if(!($con = ssh2_connect($server, 22))){
        return "fail: unable to establish connection\n";
    } else {
        // try to authenticate with username root, password secretpassword
        if(!ssh2_auth_password($con, SSH_USER, SSH_PASS)) {
            return "fail: unable to authenticate\n";
        } else {
            // execute a command
            if (!($stream = ssh2_exec($con, $command ))) {
                return "fail: unable to execute command\n";
            } else {
                // collect returning data from command
                stream_set_blocking($stream, true);
                while ($buf = fread($stream,4096)) {
                    $data .= $buf;
                }
                fclose($stream);
            }
        }
    }
    return $data;
}

function getTelnetResult($server, $command) {
    $data = "";
    $tn = new Telnet($server);
    if ($tn->login(TELNET_USER, TELNET_PASS)) {
        $data = $tn->exec($command);
    } else {
        $data = "failed to login";
    }

    return $data;
}

function duration($n) {
    if ($n>1000000) $s="?";
    else $s= sprintf("%d:%02d", $n/60, $n%60);
    return ($s);
}

function callList($callserver) {

    $data = array();

    $ds = ldap_connect($callserver,4000);
    if ( ! $ds) {
        return "LDAP connect failure";
    } else {
        $r = ldap_bind($ds, SPLICE_USER, SPLICE_PASS);
        if ( ! $r) {
            return "LDAP bind failure";
            $ds = false;
        }
    }


    if ($ds) {
        $sr=ldap_list($ds,"cn=Calls","objectclass=CallRecord");  
        $n =ldap_count_entries($ds,$sr);
        if ($n > 0){
            $info = ldap_get_entries($ds, $sr);
            for($key=0; $key!=$n ; $key++) {
                if ($info[$key]['connectedtonumber'][0]=="" AND $info[$key]['connectedtoname'][0]=="" 
                    AND $info[$key]['bend'][0]!="00000000-0000-0000-0000-000000000000") {
                    $a="AA/Voicemail";
                    }
                else $a=$info[$key]['connectedtoname'][0];

                $result = array(
                        "sourcename" => $info[$key]['sourcename'][0],
                        "sourcenumber" => $info[$key]['sourcenumber'][0],
                        "targetname" => $info[$key]['targetname'][0],
                        "targetnumber" => $info[$key]['targetnumber'][0],
                        "connectedtoname" => $a,
                        "connectedtonumber" => $info[$key]['connectedtonumber'][0],
                        "createtime" => $info[$key]['createtime'][0],
                        "modifiedtime" => $info[$key]['modifiedtime'][0]
                    );
                $data[] = $result;
            }
        }
        ldap_close($ds);
    } else {
        return "Unable to connect to LDAP server";
    }
    ldap_close($ds);
    $ds = false;
    return $data;
}

function isAvailable($server) { 
  $tB = microtime(true); 
  $fP = fSockOpen($server, 80, $errno, $errstr, 60); 
  if (!$fP) { return false; } 
  $tA = microtime(true); 
  return true; 
}


function ping($host)
{
    exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
    return $rval === 0;
}
?>
#!/usr/bin/php -q
<?php
$uniqueid=$argv[1];
$channel=$argv[2];
$queue=intval($argv[3]);
var_export($queue);

if ($queue==11802157141 || $queue==11802157143 || $queue==11802157145)
{
    $f2 = fopen("/tmp/log.call", "w");


    //время до предупреждения
    sleep(1080);

    if (uniqchan($uniqueid) == true) {
        //предупреждение
        echo("\nChannel is\n");
        fputs($f2, "Channel is\n");
        $uid = time();
        $f1 = fopen("/tmp/$uid.call", "w");
        fputs($f1, "Channel: LOCAL/2@spy-duration\n");
        fputs($f1, "MaxRetries: 0\n");
        fputs($f1, "RetryTime: 600\n");
        fputs($f1, "WaitTime: 30\n");
        fputs($f1, "Context: spy-duration\n");
        fputs($f1, "Extension: 1\n");
        fputs($f1, "Priority: 1\n");
        fputs($f1, "Set: audio=ru/custom/two_minutes\nSet: chan=$channel\n");
        fclose($f1);
        system("mv /tmp/$uid.call /var/spool/asterisk/outgoing/");
        //время от предупреждения до конца
        sleep(120);
        //время вышло
        if (uniqchan($uniqueid) == true) {
            echo("\nChannel is\n");
            fputs($f2, "Channel is\n");
            $uid = time();
            $f1 = fopen("/tmp/$uid.call", "w");
            fputs($f1, "Channel: LOCAL/2@spy-end\n");
            fputs($f1, "MaxRetries: 0\n");
            fputs($f1, "RetryTime: 600\n");
            fputs($f1, "WaitTime: 30\n");
            fputs($f1, "Context: spy-end\n");
            fputs($f1, "Extension: 1\n");
            fputs($f1, "Priority: 1\n");
            fputs($f1, "Set: audio=ru/custom/end_call\nSet: chan=$channel\n");
            fclose($f1);
            system("mv /tmp/$uid.call /var/spool/asterisk/outgoing/");
            sleep(5);
            $output = shell_exec("asterisk -rx 'channel request hangup $channel'");

        }

    } else {
        fputs($f2, "Channel none\n");

    }
}

//Проверка наличия канала
    function uniqchan($uniqueid)
    {

        //Вывод списка текущих uniqueid каналов

        $output = shell_exec("asterisk -rx'core show channels concise' | cut -d '!' -f14");
        $output = explode("\n", $output);
        $uniqueids = array_filter($output);
        var_export($uniqueids);
        $i = 0;
        $a = false;
        //сама проверка
        while ($i < count($uniqueids)) {
            if ($uniqueid == $uniqueids[$i]) {
                $a = true;
            }

            $i++;
        }
        return ($a);
    }

    fclose($f2);

?>

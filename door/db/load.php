<?php if(!defined('s7V9pz')) {die();}?><?php
function dbc($db, $out = 0) {
    if ($out == 0) {
        $db = cnf($db);
    }
    if (isset($db['host'])) {
        try {
            $conn = new PDO("mysql:host=".$db['host']."; dbname=".$db['db'], $db['user'], $db['pass']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e) {
            return false;
        }
    } else {
        return false;
    }

}
function db($m = "db,do,tb,set-wc,stval-whval,ad") {
    $arg = func_get_args();
    $do = $arg[1];
    $wh = 0; $i = 4; $ct = 0;
    if (is_array($arg[0])) {
        $db = $arg[0];
    } else {
        $db = cnf($arg[0]);
    }
    if ($do == "qw") {
        $pdo = new PDO("mysql:host=".$db['host']."", $db['user'], $db['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
    } else {
        if (is_array($arg[0])) {
            $pdo = new PDO("mysql:host=".$db['host']."; dbname=".$db['db'].";charset=utf8mb4", $db['user'], $db['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
        } else {
            if (!isset($GLOBALS['db-'.$arg[0]])) {
                $GLOBALS['db-'.$arg[0]] = new PDO("mysql:host=".$db['host']."; dbname=".$db['db'].";charset=utf8mb4", $db['user'], $db['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
            }
            $pdo = $GLOBALS['db-'.$arg[0]];
        }
    }
    if ($do[0] !== "q") {
        $arg[2] = $db['prefix'].$arg[2];
    }
    if ($do[0] == "s") {
        $do = explode(",", $do);
        if (count($do) > 1) {
            array_shift($do);
            $do = implode(',', $do);
            if (strpos($do, 'count(') !== false) {
                $ct = 1;
            }
        } else {
            $do = "*";
        }
        $act = "SELECT " . $do. " FROM " . $arg[2];
        if (!empty($arg[3])) {
            $arg[3] = str_replace(' ', '', $arg[3]);
            $wh = explode(",", $arg[3]);
        }
    } else if ($do[0] == "q") {
        $act = $arg[2];
        if (isset($arg[3])) {
            $data = $arg[3];
        }
    } else if ($do[0] == "i") {
        $arg[3] = str_replace(' ', '', $arg[3]);
        $r = explode(",", $arg[3]);
        $rvb = $rv = NULL;
        $j = count($r);
        foreach ($r as $w) {
            $j = $j - 1;
            $cm = ", ";
            if ($j == 0) {
                $cm = "";
            }

            if (strpos($w, '#') !== false) {
                $w = str_replace('#', '', $w);
                $data[$w] = $arg[$i];
            } else {
                $data[$w] = htmlspecialchars($arg[$i], ENT_COMPAT, 'UTF-8');
            }
            $i = $i + 1;
            $rv .= $w . $cm;
            $rvb .= " :" . $w . $cm;
        }
        $rv = str_replace(' ', '', $rv);
        $rv = explode(',', $rv);
        $rv = implode('`,`', $rv);
        $rv = '`'.$rv.'`';

        $act = "INSERT INTO " . $arg[2] . "(" . $rv . ") VALUES (" . $rvb . ")";
    } else if ($do[0] == "d") {
        $act = "DELETE FROM " . $arg[2];
        if (!empty($arg[3])) {
            $arg[3] = str_replace(' ', '', $arg[3]);
            $wh = explode(",", $arg[3]);
        }
    } else if ($do[0] == "u") {
        if (!empty($arg[4])) {
            $arg[4] = str_replace(' ', '', $arg[4]);
            $wh = explode(",", $arg[4]);
        }
        $i = 5;
        $arg[3] = str_replace(' ', '', $arg[3]);
        $st = explode(",", $arg[3]);
        $r = "";
        $j = count($st);
        foreach ($st as $w) {
            $j = $j - 1;
            $cm = ", ";
            if ($j == 0) {
                $cm = "";
            }
            if (strpos($w, '#') !== false) {
                $w = str_replace('#', '', $w);
                $wr = "n" . $w;
                $data[$wr] = $arg[$i];
            } else {
                $wr = "n" . $w;
                $data[$wr] = htmlspecialchars($arg[$i], ENT_COMPAT, 'UTF-8');
            }
            $i = $i + 1;
            $r .= " `" . $w . "`=:" . $wr . $cm;
        }
        $act = "UPDATE  " . $arg[2] . " SET " . $r;
    }
    if ($wh) {
        $j = count($wh);
    }
    $whr = $ad = "";
    if ($wh && count($wh) != 0 && $wh != 0) {
        $whr = " WHERE";
        foreach ($wh as $w) {
            $cm = "AND";
            $j = $j - 1;
            $eq = " = ";
            if (strpos($w, '|') !== false) {
                $cm = "OR";
            }

            if ($j == 0) {
                $cm = "";
            }

            if (strpos($w, '<=') !== false) {
                $eq = " <= ";
            } else
                if (strpos($w, '>=') !== false) {
                $eq = " >= ";
            } else
                if (strpos($w, '<>') !== false) {
                $eq = " <> ";
            } else
                if (strpos($w, '<') !== false) {
                $eq = " < ";
            } else
                if (strpos($w, '>') !== false) {
                $eq = " > ";
            } else if (strpos($w, ' LIKE') !== false) {
                $eq = ' LIKE ';
            }
            $w = str_replace(array(
                '|',
                '<',
                ">",
                "=",
                " LIKE"
            ), '', $w);
            $dw = $w.rn(4);
            $data[$dw] = htmlspecialchars($arg[$i], ENT_COMPAT, 'UTF-8');
            $i = $i + 1;
            $whr .= " " . $w . $eq . ":" . $dw . " " . $cm;
        }
    }
    if ($do[0] != "q" && !empty($arg[$i])) {
        $ad = $arg[$i];
    }

    if (empty($data)) {
        $data[] = 0;
    }
    $sql = $act . $whr . $ad;
    $db = $pdo->prepare($sql);
    if (1) {
        if (stripos($sql, 'SELECT') !== false && stripos($sql, 'FROM') !== false) {
            if ($ct == 0) {
                return $db->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return $db->fetchAll(PDO::FETCH_NUM);
            }
        } else if (strstr(strtolower($sql), ' ', true) === "show") {
            return $db->fetchAll();
        } else if (stripos($sql, 'INSERT INTO') !== false) {
            return $pdo->lastInsertId();
        } else
        {
            return true;
        }
    } else {
        return false;
    }
    $pdo = null;
}

?>
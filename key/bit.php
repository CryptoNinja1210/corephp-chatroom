<?php if(!defined('s7V9pz')) {die();}?>
<?php
function cnf($v = "cnf") {
    $cnf["cnf"] = array(
        "mode" => 1,
        "name" => "Grupo - Baevox Framework",
        "tag" => "Something Beyond Limits",
        "poet" => "Baevox",
        "url" => "https://sabayachat.com/chat2/",
        "region" => "Asia/Kolkata",
        "knob" => "knob",
        "door" => "door",
        "gem" => "gem",
        "bit" => "s7V9pz",
        "chief" => "admin",
        "codeword" => "pass",
        "samesite" => "None",
        "ext" => "xml",
        "global" => "1",
        "appversion" => 1,
    );
$cnf["Grupo"] = array(
                'host' => 'localhost',
                'db' => 'chat',
                'user' => 'root',
                'pass' => 'root',
                'prefix' => 'gr_'
                );
if ($v == "all") {
return $cnf;
} 
else if (isset($cnf[$v])) {
return $cnf[$v];
}
}
?>


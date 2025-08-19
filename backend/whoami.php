<?php
require "db.php";

$user = current_user();
if ($user) {
    json(["ok"=>true,"user"=>$user]);
} else {
    json(["ok"=>false]);
}

<?php
require "db.php";

session_destroy();
json(["ok"=>true,"message"=>"Logged out"]);

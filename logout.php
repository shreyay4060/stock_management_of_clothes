<?php
session_start();

// destroy PHP session
session_unset();
session_destroy();

// clear cookie if you ever use it
setcookie(session_name(), '', time() - 3600, '/');

// optional: also remove "user" from frontend (localStorage) with JS
echo "<script>
  localStorage.removeItem('user');
  window.location.href = 'index.php';
</script>";
exit;
?>

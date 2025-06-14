<?php
$password = '!@felicity%solar@&)ng-+';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hash;
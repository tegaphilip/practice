<?php

echo "<pre>";

print_r(get_loaded_extensions()); ?>

<?php
if (!extension_loaded('openssl')) {
    echo "Not loaded";
}
?>

<?php
echo time();
echo '<br>';
echo strtotime("+3 days");
$hook['pre_controller'] = array(
                                'class'    => 'login',
                                'function' => 'performLogin',
                                'filename' => 'login.php',
                                'filepath' => 'controllers/admin',
                                'params'   => array('beer', 'wine', 'snacks')
                                );
?>

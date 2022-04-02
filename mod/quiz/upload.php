<?php
   
if($_POST['data'])
{
    print_r($_POST['data']);
    file_put_contents('./my.pdf', $bin);
}
?>
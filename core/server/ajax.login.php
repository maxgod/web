<?php
if($_POST['reg'] == $_SESSION['reg'])
    return false;
else
{
    $_SESSION['reg'] = "";
}
<?php
//included functions for update_db
//this should be kept as limited as possible

//show error function( condensed)
function showerror() {
    if (mysql_error()) {
        echo "think tank forums fatal error: mysql error ".mysql_errno().": ".mysql_error();
        die();
    } else {
        echo "think tank forums fatal error: could not connect to the mysql dbms.";
        die();
    };
};

//setup connection
if (!($dbms_cnx = @mysql_pconnect($update_db['host'], $update_db['user'], $update_db['pass']))) showerror();
if (!mysql_select_db($update_db['db'])) showerror();
if (!mysql_query("SET NAMES 'utf8'")) showerror();
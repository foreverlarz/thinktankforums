<?php
/* think tank forums
 *
 * include_footer.php
 */
?>
            <br style="clear: both;" />
        </div>
    </body>
</html>
<?php

$time_end = microtime(true);
$time = $time_end - $time_start;

echo <<<EOF
<!-- page generated in $time seconds
     by think tank forums {$ttf_cfg["version"]}
     visit http://www.ttfproject.com/ -->

EOF

?>

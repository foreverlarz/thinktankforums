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
echo "<!-- page generated in $time seconds\n";
echo "     by think tank forums {$ttf_cfg["version"]}\n";
echo "     visit http://www.ttfproject.com/ -->\n";
?>

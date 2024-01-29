<?php

$template = file_get_contents('../html/500.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>
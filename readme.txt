* Horizontal View
Default css is for Vertical View. Please modify css. 
A filter hook 'radio_other_admin_css' might be a help.

add_filter('radio_other_admin_css','my_css_setting');
function my_css_setting() {?>
<style type="text/css">
/* my style here */
</style>
<?php

* Flexible Content
When you use this addon within Flexible Content, 'Add this answer to the choices.' does NOT work.

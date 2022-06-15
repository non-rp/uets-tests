<?php

class uetsTemplateLoader extends Gamajo_Template_Loader {

    protected $filter_prefix = 'uets-tests';

    protected $theme_template_directory = 'uets-tests';

    protected $plugin_directory = UETSTESTS_PATH;

    protected $plugin_template_directory = 'templates';

    public function register() {
        add_filter('template_include', [$this, 'uetsests_template']);
    }

    public function uetsests_template($template) {
        if(is_singular('test')) {
            $theme_files = ['single-test.php'];
            $exist = locate_template($theme_files, false);

            if($exist != '') {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__).'templates/single-test.php';
            }
        }
        return $template;
    }

    
}


$uetsTemplateLoader = new uetsTemplateLoader();
$uetsTemplateLoader->register();
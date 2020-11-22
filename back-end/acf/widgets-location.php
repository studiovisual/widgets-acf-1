<?php 

if(!defined('ABSPATH')) exit;

class WidgetsLocation extends ACF_Location {
    
    public function initialize() {
        $this->category = 'Forms';
        $this->name = 'widget_acf';
        $this->label = 'Widget ACF';
        
        return $this;
    }
    
    public function get_values($rule=null) {
        
        $widgets = array();
        
        $path =
        function_exists('\App\template') || function_exists('\Roots\view') ? 
        get_template_directory() . '/views/widgets-templates' :
        get_template_directory() . '/widgets-templates';
        
        
        if(!is_dir($path)){
            $path = plugin_dir_path(__FILE__) . '../../widgets-templates';
        }
        
        if(!is_dir($path))
        return $widgets;
        
        $dir = new DirectoryIterator($path);
        
        foreach($dir as $fileinfo):            
            if($fileinfo->isDir() && !$fileinfo->isDot()):
                
                $widget_label = Utils::parseWidgetHeaders($path.'/'.$fileinfo->getFilename().'/functions.php');
                
                $widget_name =  $widget_label['name'];
                
                $widget_label['title'] = ucfirst(str_replace('_', ' ', $widget_label['name']));
                $widgets[$widget_name] = $widget_label['title'];
            endif;
        endforeach;
        
        return $widgets;
    }
    
    public function export_zip($widget_name) {

        $path =
        function_exists('\App\template') || function_exists('\Roots\view') ? 
        get_template_directory() . '/views/widgets-templates' :
        get_template_directory() . '/widgets-templates';

        $dir = $path . '/';
        $zip = new ZipArchive();
        $zipname = $widget_name . '.zip';
        $res = $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($res === TRUE) {
            foreach (glob($dir . '/*') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        } else {
            echo 'Failed to create to zip. Error: ' . $res;
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);       

    }
                    
}
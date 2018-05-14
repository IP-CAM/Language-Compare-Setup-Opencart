
<?php


$olc = new OpencartLanguageCompare($argv);




class OpencartLanguageCompare {

    /**
     * Result directory
     * @var string
     */
    private $result_dir = './result';

    /**
     * Original files directory
     * @var string
     */
    private $original_directory = './original';

    /**
     * Compare directory
     * @var string
     */
    private $translate_directory = './translate';

    public function __construct($argv = array()) {
        if ($argv[1] && $argv[2]) {

            if (is_file($this->translate_directory . '/' . $argv[1] . '.php')) {
                rename($this->translate_directory . '/' . $argv[1]. '.php', $this->translate_directory . '/' . $argv[2]. '.php');
                $this->doCompare();
            } else {
                echo "\nFile " . $this->translate_directory . '/' . $argv[1] . '.php' . " NOT FOUND:\n";
            }

        } else {
            echo "\nWRONG COMMAND: the correct command is:\n";
            echo "php brain.php translation_filename original_filename\n\n";
        }
    }



    /**
     * Main compare logic
     */
    public function doCompare() {

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->original_directory));

        $it->rewind();
        while($it->valid()) {


            if (!$it->isDot() && $it->isFile()) {

                // check file extension
                $file_parts = pathinfo($it->key());

                if ($file_parts['extension'] == 'php') {

                    // Include original file
                    if( is_file($it->key())) {
                        require_once $it->key();
                    }

                    // Save original file data
                    $original_array = array();

                    if ( isset($_) ) {
                        $original_array = $_;

                        // clear original data before include translation file
                        unset($_);
                    }

                    $translation_file = $this->getTranslationFilePath( $it->getSubPathName() );
                    if( is_file($translation_file)) {
                        require_once $translation_file;
                    }


                    $coinciding = array();
                    $not_coinciding = array();

                    if ($original_array && isset($_)) {
                        // if have translated file

                        foreach ($original_array as $key => $value) {

                            if (array_key_exists($key, $_)) {
                                $coinciding[$key] = $_[$key];
                            } else {
                                $not_coinciding[$key] = $value;
                            }
                        }
                    } else {
                        // if haven't translated file
                        $not_coinciding = $original_array;
                    }

                    $output_array = array_merge($coinciding, $not_coinciding);

                    if ($output_array) {
                        $this->createFile($it->getSubPath(), $it->getSubPathName(), $output_array);
                    }
//                    $this->createFile($it->getSubPath(), $it->getSubPathName(), $not_coinciding);

                    echo 'Filename: ' . $it->getSubPathName() . "\n";

                    unset($coinciding);
                    unset($not_coinciding);
                    unset($original_array);
                    unset($_);

                } // end check file extension


            }

            $it->next();
        }
    }

    public function createFile($dirname, $filename, $data) {

        $dirname = $this->result_dir . '/' . $dirname;

        //rmdir($dirname);
        if (!is_dir($dirname)) {
            // dir doesn't exist, make it
            mkdir($dirname, 0777, true);
        }
        $filename = $this->result_dir . '/' . $filename;
        file_put_contents( $filename, $this->generateTextBasedOnArray($data) );
    }

    /**
     *
     * Generate text data for inserting to the file
     *
     * @param $data
     * @return string
     */
    private function generateTextBasedOnArray($data) {

        $output = '<?php' . "\n\n";

        foreach ($data as $key => $value) {
            
            $output .= '$_["' . $key . '"]          = "' . stripslashes($value) . '"'. ";\n";

        }

        return $output;
    }

    private function getTranslationFilePath($original) {
        return $this->translate_directory . '/' . $original;
    }

}
<?php

namespace teluq;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Installs both Moodle and its plugins under moodle/ directory.
 *
 * Project file structure will become:
 *  - composer.json
 *  - composer.lock
 *  - vendor/
 *  - moodle/
 *
 * Moodle root will be located in the moodle/ directory, and it's
 * plugins in corresponding type specific subdirectories such
 * as moodle/mod/ and moodle/local/.
 */
class MoodleInstaller extends LibraryInstaller {
    protected const MOODLE_LOCATION = "moodle/";
    protected const MOODLE_PLUGINTYPE = "moodle-";
    protected const NOCOMPOSER_TYPE = "package";
    
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package) {
        $prettyName = $package->getPrettyName();

        if($package->getType() == "package") {
            var_dump($package);
        }
        


        if ($prettyName == 'moodle/moodle') {
            return self::MOODLE_LOCATION;
        }

        if (strpos($prettyName, '/') !== false) {
            list($vendor, $name) = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }

        $availableVars = compact('name', 'vendor');

        $extra = $package->getExtra();
        if (!empty($extra['installer-name'])) {
            $availableVars['name'] = $extra['installer-name'];
        }

        $pluginType = str_replace(self::MOODLE_PLUGINTYPE, '', $package->getType());

        return self::MOODLE_LOCATION . $this->templatePath($this->locations[$pluginType], $availableVars);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType) {
        $prefix = substr($packageType, 0, 7);

        if ($prefix == self::MOODLE_PLUGINTYPE || $packageType == self::NOCOMPOSER_TYPE) {
            return TRUE;
        }

        return 'project' === $packageType;
    }

    /**
     * Replace vars in a path
     *
     * @param  string $path
     * @param  array  $vars
     * @return string
     */
    protected function templatePath($path, array $vars = array())
    {
        if (strpos($path, '{') !== false) {
            extract($vars);
            preg_match_all('@\{\$([A-Za-z0-9_]*)\}@i', $path, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $var) {
                    $path = str_replace('{$' . $var . '}', $$var, $path);
                }
            }
        }

        return $path;
    }

    protected $locations = array(
        'mod'                => 'mod/{$name}/',
        'admin_report'       => 'admin/report/{$name}/',
        'atto'               => 'lib/editor/atto/plugins/{$name}/',
        'tool'               => 'admin/tool/{$name}/',
        'assignment'         => 'mod/assignment/type/{$name}/',
        'assignsubmission'   => 'mod/assign/submission/{$name}/',
        'assignfeedback'     => 'mod/assign/feedback/{$name}/',
        'antivirus'          => 'lib/antivirus/{$name}/',
        'auth'               => 'auth/{$name}/',
        'availability'       => 'availability/condition/{$name}/',
        'block'              => 'blocks/{$name}/',
        'booktool'           => 'mod/book/tool/{$name}/',
        'cachestore'         => 'cache/stores/{$name}/',
        'cachelock'          => 'cache/locks/{$name}/',
        'calendartype'       => 'calendar/type/{$name}/',
        'customfield'        => 'customfield/field/{$name}/',
        'fileconverter'      => 'files/converter/{$name}/',
        'format'             => 'course/format/{$name}/',
        'coursereport'       => 'course/report/{$name}/',
        'contenttype'        => 'contentbank/contenttype/{$name}/',
        'customcertelement'  => 'mod/customcert/element/{$name}/',
        'datafield'          => 'mod/data/field/{$name}/',
        'dataformat'         => 'dataformat/{$name}/',
        'datapreset'         => 'mod/data/preset/{$name}/',
        'editor'             => 'lib/editor/{$name}/',
        'enrol'              => 'enrol/{$name}/',
        'filter'             => 'filter/{$name}/',
        'gradeexport'        => 'grade/export/{$name}/',
        'gradeimport'        => 'grade/import/{$name}/',
        'gradereport'        => 'grade/report/{$name}/',
        'gradingform'        => 'grade/grading/form/{$name}/',
        'local'              => 'local/{$name}/',
        'logstore'           => 'admin/tool/log/store/{$name}/',
        'ltisource'          => 'mod/lti/source/{$name}/',
        'ltiservice'         => 'mod/lti/service/{$name}/',
        'media'              => 'media/player/{$name}/',
        'message'            => 'message/output/{$name}/',
        'mnetservice'        => 'mnet/service/{$name}/',
        'paygw'              => 'payment/gateway/{$name}/',
        'plagiarism'         => 'plagiarism/{$name}/',
        'portfolio'          => 'portfolio/{$name}/',
        'qbehaviour'         => 'question/behaviour/{$name}/',
        'qformat'            => 'question/format/{$name}/',
        'qtype'              => 'question/type/{$name}/',
        'quizaccess'         => 'mod/quiz/accessrule/{$name}/',
        'quiz'               => 'mod/quiz/report/{$name}/',
        'report'             => 'report/{$name}/',
        'repository'         => 'repository/{$name}/',
        'scormreport'        => 'mod/scorm/report/{$name}/',
        'search'             => 'search/engine/{$name}/',
        'theme'              => 'theme/{$name}/',
        'tinymce'            => 'lib/editor/tinymce/plugins/{$name}/',
        'profilefield'       => 'user/profile/field/{$name}/',
        'webservice'         => 'webservice/{$name}/',
        'workshopallocation' => 'mod/workshop/allocation/{$name}/',
        'workshopeval'       => 'mod/workshop/eval/{$name}/',
        'workshopform'       => 'mod/workshop/form/{$name}/',
        'component-addon'    => 'lib/polyfills/',
    );
}

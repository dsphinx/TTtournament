<?php

/**
 *  Copyright (c) 2025, dsphinx  by chatGPT
 *  Copyright (c) 2015-2017, dsphinx
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *   1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *   2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *   3. All advertising materials mentioning features or use of this software
 *      must display the following acknowledgement:
 *      This product includes software developed by the dsphinx.
 *   4. Neither the name of the dsphinx nor the
 *      names of its contributors may be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY dsphinx ''AS IS'' AND ANY
 *  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL dsphinx BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  Filename: miniMVController.php
 *  Created : 23/11/15 21:32
 *
 *  Based on MVC dsphinx Framework II -September 2015-
 *           go to FULL version MVC dsphinx@plug.gr
 *
 *  Dev Path:  /data/www/Framework/version_II/Developer.Plugins/miniVersion/miniMVController.php
 *
 *
 * afeter static html, or xml ....
 * settings in sqlite3
 *
 */

class miniMVController
{
    public static $db = null; // SQLite DB instance
    public static $_EOL = "\n"; // Default end of line character
    public static $config = [ // Global configuration array
        'header' => 'App/Data/htmlStructure/header',
        'footer' => 'App/Data/htmlStructure/footer',
        'menu' => 'App/Data/htmlStructure/menus',
        'configuration' => 'App/configuration',
        'page' => null,
        'HTMLBlock' => [
            'title' => '',
            'appID' => '',
        ],
        'menus' => null,
        'currentFile' => null,
    ];

    // Outputs string with EOL
    public static function echoc(string $text): void
    {
        $text .= self::$_EOL;
        echo $text;
    }

    // Pretty prints an object/array for debugging, formatted as HTML
    public static function object(mixed $data, bool $return_data = false): string|null
    {
        $output = print_r($data, true);
        $output = str_replace(" ", "&nbsp;", $output);
        $output = str_replace(["\r\n", "\r", "\n"], ["<br>\r\n", "<br>\r", "<br>\n"], $output);
        $output = str_replace(["[", "]"], [
            "<b style=\"color:red;\">[ </b>",
            "<b style=\"color:red;\"> ]</b>"
        ], $output);

        if (!$return_data) {
            echo $output;
            return null;
        }

        return $output;
    }

    // Gets parameter from GET, POST, SESSION, COOKIE
    public static function param(string $name): ?string
    {
        if (isset($_SESSION)) {
            $sources = [$_GET, $_POST, $_SESSION, $_COOKIE];
        } else {
            $sources = [$_GET, $_POST, $_COOKIE];
        }

        foreach ($sources as $source) {
            if (isset($source[$name])) {
                return htmlspecialchars((string)$source[$name], ENT_QUOTES, 'UTF-8');
            }
        }

        return null;
    }

    // Loads a PHP or HTML file and optionally returns its content
    public static function load(string $file, bool $returnCode = true, ?string $path = null, bool $display = true, bool $current = false): ?string
    {
        $types = ['html', 'php'];
        $ret = null;

        foreach ($types as $ext) {
            $scan = "$path$file.$ext";
            if (file_exists($scan)) {
                if ($returnCode) {
                    $ret = file_get_contents($scan);
                } else {
                    include $scan;
                    $ret = " ";
                }

                if ($current) {
                    self::$config['currentFile'] = ($ext === "php") ? $scan : null;
                }

                break;
            }
        }

        if (is_null($ret) && $display) {
            self::echoc(self::error('URI Dead !', ' Sorry, page not found. <br/> Linked to <mark> $file </mark> ' ));
        }

        return $ret;
    }

    // Finds all block placeholders in HTML like [@blockName]
    public static function getBlocks(string $_html, string $pattern = '/(?!\b)(\[@\w+\b\])/', int $index = 0): array
    {
        preg_match_all($pattern, $_html, $matches);
        return $matches[$index] ?? [];
    }

    // Replaces placeholders in subject with actual values
    public static function setBlocks(array|string $from, array|string $to, string $subject): string
    {
        return str_replace($from, $to, $subject);
    }

    // Outputs header HTML, replacing placeholders with config values
    public static function header(): void
    {
        $header = file_get_contents(self::$config['header'] . ".html");
        $tmp = self::getBlocks($header);

        foreach (self::$config['HTMLBlock'] as $tag => $val) {
            $header = self::setBlocks('[@' . $tag . ']', $val, $header);
        }

        self::echoc($header);
    }

    // Returns an error HTML block
    public static function error(string $title = " Not found", string $info = " Not found "): string
    {
        return '<div class="panel panel-default " style="width: 90%;float: none;  margin-left: auto;  margin-right: auto; ">
				 <div class="panel-heading "> <span class="glyphicon glyphicon-warning-sign"></span> &nbsp;' . $title . '</div>
				  <div class="panel-body">
				     ' . $info . '
				  </div>
				  <a style="width: 100%;" onclick="window.history.back()"  class="btn btn-danger btn-sm" role="button">&laquo; επιστροφή </a>
				</div>';
    }

    // Main function to output full page
    public static function go(): void
    {
        $html = self::preProcessPage(self::$config['page']);
        self::header();
        include self::$config['menu'] . ".html";

        if (!$html) {
            self::echoc(self::error('URI Dead !', ' Sorry, page not found. <br/> Linked to ' . self::$config['page']));
        } else {
            if (self::$config['currentFile']) {
                include self::$config['currentFile'];
            } else {
                echo $html;
            }
        }

        include self::$config['footer'] . ".html";
    }

    // Loads page content and extracts title
    public static function preProcessPage(string $page): ?string
    {
        $html = null;

        foreach ($_SESSION['PATHS']['INCLUDE_DIRS'] as $value) {
            $path = $_SESSION['PATHS']['__ROOT__'] . $value;
            if (file_exists($path) && is_null($html)) {
                $html = self::load(self::$config['page'], true, $value, false, true);
            }
        }

        if ($html && preg_match("/<title>(.*)<\/title>/i", $html, $title)) {
            self::$config['HTMLBlock']['title'] = $title[1];
        }

        return $html;
    }

    // Returns base URL (without parameters)
    public static function getUrl(): string
    {
        $_url = $_SERVER["REQUEST_URI"] ?? '';
        $_tmp = explode("&", $_url);
        return $_tmp[0];
    }

    // Prints JavaScript message to #tailOutput element
    public static function show(string $message, ?string $error = null): void
    {
        if (!is_null($error)) {
            $message = "<b>Error --> </b> <mark>$message</mark><br/> ";
        } else {
            $message = "<br/> <i> $message</i>";
        }

        echo "<script> $(function () { $('#tailOutput').append('" . $message . " <br/>'); }); </script>";
    }

    // Initializes SQLite DB and config
    public static function initConfig(string $sqlLiteFile = 'App/Data/dataInfo.sqlite'): void
    {
        require_once 'sqliteDb.php';
        self::$db = new sqliteDb($sqlLiteFile);

        $page = self::param('page');
        self::$config['page'] = $page ?: "main";

        if (!self::load(self::$config['configuration'], false)) {
            die(self::echoc(self::error(' Error loading config file')));
        }

        self::$config['page'] = $page ?: self::$config['HTMLBlock']['defaultPageRun'] ?? "main";
    }
}

// Start the controller
miniMVController::initConfig();
// miniMVController::go();  // Uncomment when you're ready
<?php

/**
 * reCAPTCHA extension for eZ Publish
 * Written by Bruce Morrison <bruce@stuffandcontent.com>
 * Copyright (C) 2008. Bruce Morrison.  All rights reserved.
 * http://www.stuffandcontent.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

class reCAPTCHATemplateOperator
{

    public $Operators;

    public function __construct()
    {
        $this->Operators = array('recaptcha_get_html');
    }


    public function operatorList()
    {
        return $this->Operators;
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array(
            'recaptcha_get_html' => array(
                'key' => array(
                    'type' => 'string',
                    'required' => false,
                    'default' => null
                )
            ),
        );
    }

    public function modify(
        $tpl,
        $operatorName,
        $operatorParameters,
        $rootNamespace,
        $currentNamespace,
        &$operatorValue,
        $namedParameters
    ) {
        switch ($operatorName) {
            case 'recaptcha_get_html':

                $key = $namedParameters['key'];
                if ($key == null) {
                    $projectIni = \eZINI::instance('project.ini');
                    $key = $projectIni->variable('Site', 'RecaptchaSiteKey');
                }

                $operatorValue = '
                    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" charset="utf-8"></script>
                    <div class="g-recaptcha" data-sitekey="' . $key . '" data-callback="recaptchaSuccess"></div>
                ';
        }
    }
};

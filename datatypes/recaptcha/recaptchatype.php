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

// Include the super class file
include_once("kernel/classes/ezdatatype.php");

// Define the name of datatype string
define("EZ_DATATYPESTRING_RECAPTCHA", "recaptcha");


class recaptchaType extends eZDataType
{
    public function __construct()
    {
        $this->eZDataType(
            EZ_DATATYPESTRING_RECAPTCHA,
            "reCAPTCHA",
            array(
                'serialize_supported' => false,
                'translation_allowed' => false
            )
        );
    }

    private static function bypassRecaptcha()
    {
        $bypassAccess = false;

        $projectIni = \eZINI::instance('project.ini');
        $allowedSiteAccess = $projectIni->variable('Recaptcha', 'AllowedSiteaccess');
        if (is_array($allowedSiteAccess)) {
            // Checks if any allowed siteaccesses are in the current siteaccess
            $bypassAccess = !count(array_intersect($allowedSiteAccess, $GLOBALS['eZCurrentAccess']));
        } else {
            $bypassAccess = in_array($allowedSiteAccess, $GLOBALS['eZCurrentAccess']);
        }
    }

    public function validateObjectAttributeHTTPInput(
        $http,
        $base,
        $objectAttribute
    ) {
        $bypassAccess = self::bypassRecaptcha();

        if ($bypassAccess || $this->reCAPTCHAValidate($http)) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        $objectAttribute->setValidationError(ezpI18n::tr('extension/recaptcha', "The reCAPTCHA wasn't entered correctly. Please try again. :-)"));
        return eZInputValidator::STATE_INVALID;
    }

    public function validateCollectionAttributeHTTPInput($http, $base, $objectAttribute)
    {
        $bypassAccess = self::bypassRecaptcha();
        
        if ($bypassAccess || $this->reCAPTCHAValidate($http)) {
            return eZInputValidator::STATE_ACCEPTED;
        }


        $objectAttribute->setValidationError(ezpI18n::tr('extension/recaptcha', "The reCAPTCHA wasn't entered correctly. Please try again. :-("));
        return eZInputValidator::STATE_INVALID;
    }

    public function isIndexable()
    {
        return false;
    }

    public function isInformationCollector()
    {
        return true;
    }

    public function hasObjectAttributeContent($contentObjectAttribute)
    {
        return false;
    }

    public function reCAPTCHAValidate($http)
    {
        $http = \eZHTTPTool::instance();
        $gRecaptchaResponse = $http->hasPostVariable('g-recaptcha-response')
            ? $http->postVariable('g-recaptcha-response')
            : '';

        $projectIni = \eZINI::instance('project.ini');
        $secret = $projectIni->variable('Site', 'RecaptchaSecret');
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);

        $resp = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }
}
eZDataType::register(EZ_DATATYPESTRING_RECAPTCHA, "recaptchaType");

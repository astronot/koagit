<?PHP
///////////////////////////////////////////////////////////////////////////////
// 03/01/2004
// VDaemon PHP Library version 2.3.0
//
// Copyright (C) 2002-2004 Alexander Orlov and Andrei Stepanuga
//
// Support can be obtained at:
// http://www.x-code.com/vdaemon_web_form_validation.php
// vdaemon@x-code.com
//
///////////////////////////////////////////////////////////////////////////////

//-----------------------------------------------------------------------------
//                 Errors
//-----------------------------------------------------------------------------

define('VD_E_INVALID_HTML',             'Invalid HTML syntax');
define('VD_E_UNNAMED_FORM',             'Name attribute of a form must be specified');
define('VD_E_UNCLOSED_FORM',            'Closing form tag </form> for %s form is not found');
define('VD_E_FORMLESS_SUMMARY',         'Summary must be associated with a VDaemon form (form attribute is missing)');
define('VD_E_FORMLESS_LABEL',           'Label must be associated with a VDaemon form (form attribute is missing)');
define('VD_E_FORMLESS_VALIDATOR',       'Validator must be located inside VDaemon form');
define('VD_E_UNCLOSED_LABEL',           'Closing tag </vllabel> is missing');
define('VD_E_UNCLOSED_GROUP',           'Closing tag </vlgroup> is missing');
define('VD_E_INVALID_FORM_ATTRIBUTE',   'Can not find form specified in Form attribute');
define('VD_E_UNNAMED_VALIDATOR',        'Name attribute of a validator must be specified');
define('VD_E_VALIDATOR_NAME',           'Validator Name must be unique');
define('VD_E_FORM_METHOD',              'Only POST method is allowed for VDaemon Form');
define('VD_E_UNTYPED_VALIDATOR',        'Type attribute of a validator must be specified');
define('VD_E_VALIDATOR_TYPE',           'Invalid Validator type');
define('VD_E_CONTROL_MISSED',           'Control attribute of a validator must be specified');
define('VD_E_VALIDATOR_CONTROL',        'Input element referenced by Control attribute not found');
define('VD_E_VALIDTYPE_MISSED',         'This type of Validator must have a ValidType attribute');
define('VD_E_VALIDTYPE_INVALID',        'Invalid ValidType attribute value');
define('VD_E_MINLENGTH_INVALID',        'Invalid MinLength attribute value');
define('VD_E_MAXLENGTH_INVALID',        'Invalid MaxLength attribute value');
define('VD_E_MINVALUE_MISSED',          'Range Validator must have a MinValue attribute');
define('VD_E_MINVALUE_INVALID',         'Invalid MinValue attribute value');
define('VD_E_MAXVALUE_MISSED',          'Range Validator must have a MaxValue attribute');
define('VD_E_MAXVALUE_INVALID',         'Invalid MaxValue attribute value');
define('VD_E_DATEORDER_INVALID',        'Invalid DateOrder attribute value');
define('VD_E_OPERATOR_MISSED',          'Compare Validator must have an Operator attribute');
define('VD_E_OPERATOR_INVALID',         'Invalid Operator attribute value');
define('VD_E_COMPAREVALUE_MISSED',      'Compare Validator must have either CompareValue or CompareControl attribute');
define('VD_E_COMPAREVALUE_INVALID',     'Invalid CompareValue attribute value');
define('VD_E_COMPARECONTROL_NOT_FOUND', 'Input element referenced by CompareControl attribute not found');
define('VD_E_REGEXP_MISSED',            'RegExp Validator must have a RegExp attribute');
define('VD_E_FUNCTION_MISSED',          'Custom Validator must have a Function attribute');
define('VD_E_FUNCTION_INVALID',         'Invalid Function attribute value');
define('VD_E_GROUP_CONTENT',            'Group Validator can contain Validators only');
define('VD_E_GROUP_EMPTY',              'Group Validator must contain at least one Validator');
define('VD_E_VALIDATORS_MISSED',        'Label must have a Validators attribute');
define('VD_E_VALIDATOR_NOT_FOUND',      'Validator referenced by Validators attribute not found');
define('VD_E_DISPLAYMODE_INVALID',      'Invalid DisplayMode attribute value');
define('VD_E_SERIALIZE',                'Can\'t serialize validators information.');
define('VD_E_UNSERIALIZE',              'Can\'t unserialize validators information.');
define('VD_E_POST_SECURITY',            'Page is accessed using POST method, but validators information isn\'t defined.');

//-----------------------------------------------------------------------------
//                 Library Code
//-----------------------------------------------------------------------------

ob_start('VDCallback');
if (!session_id())
{
    session_start();
}

define('PATH_TO_VDAEMON', dirname(__FILE__).'/');
require_once(PATH_TO_VDAEMON . 'XML/XML_HTMLSax.php');
require_once(PATH_TO_VDAEMON . 'config.php');

$oVDaemonStatus = null;
$_VDAEMON = array();

if (isset($_GET['vdaemonid']) &&
    isset($_SESSION['VDaemonData']) &&
    $_GET['vdaemonid'] == $_SESSION['VDaemonData']['ID'])
{
    $_VDAEMON = $_SESSION['VDaemonData']['POST'];
    $oVDaemonStatus = @unserialize($_SESSION['VDaemonData']['STATUS']);
}

VDValidate();

function VDCallback($sBuffer)
{
    $sResult = $sBuffer;

    if (!defined('VDAEMON_PARSE') || VDAEMON_PARSE != false || strtolower(VDAEMON_PARSE) != 'false')
    {
        //ini_set('error_log', 'errors.log');

        ini_set('display_errors', false);
        ini_set('log_errors', true);
        if (!ini_get('error_log'))
        {
            ini_set('error_log', 'syslog');
        }

        $oPage = new CVDPage($sBuffer);
        $sResult = $oPage->ProcessPage();
    }

    return $sResult;
}

function VDValidate()
{
    global $oVDaemonStatus;
    global $_VDAEMON;
    $sErrMsg = '';
    $aValidators = array();

    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST')
    {
        return;
    }
    if (!isset($_POST['VDaemonValidators']))
    {
        if (VDAEMON_POST_SECURITY)
        {
            $sErrMsg = VD_E_POST_SECURITY;
        }
        else
        {
            return;
        }
    }
    else
    {
        $sValue = VDGetValue('VDaemonValidators');
        if (!$sValue)
        {
            $sErrMsg = VD_E_UNSERIALIZE;
        }
        else
        {
            $oRuntime = @unserialize($sValue);
            if (!$oRuntime || get_class($oRuntime) != 'cvdvalruntime' || !is_array($oRuntime->aNodes))
            {
                $sErrMsg = VD_E_UNSERIALIZE;
            }
            else
            {
                foreach ($oRuntime->aNodes as $nIdx => $mTmp)
                {
                    if (get_class($oRuntime->aNodes[$nIdx]) != 'xmlnode')
                    {
                        $sErrMsg = VD_E_UNSERIALIZE;
                        break;
                    }
        
                    $oVal =& new CVDValidator($oRuntime->aNodes[$nIdx]);
                    if ($oVal->CheckSyntax(null))
                    {
                        $sErrMsg = VD_E_UNSERIALIZE;
                        break;
                    }
        
                    $aValidators[] =& $oVal;
                }
            }
        }
    }

    if ($sErrMsg)
    {
        echo VDErrorMessage($sErrMsg);
        exit;
    }

    unset($_POST['VDaemonValidators']);
    $_VDAEMON = $_POST;

    $oVDaemonStatus = new CVDFormStatus();
    $oVDaemonStatus->sForm = $oRuntime->sForm;
    $oVDaemonStatus->bValid = true;
    foreach ($aValidators as $nIdx => $mTmp)
    {
        $oValStatus =& new CVDValidatorStatus();
        $oValStatus->bValid = $aValidators[$nIdx]->Validate();
        $oValStatus->sErrMsg = isset($aValidators[$nIdx]->oNode->aAttrs['errmsg']) ?
                                     $aValidators[$nIdx]->oNode->aAttrs['errmsg'] : '';
        $oVDaemonStatus->bValid = $oVDaemonStatus->bValid && $oValStatus->bValid;
        $oVDaemonStatus->aValidators[$aValidators[$nIdx]->oNode->aAttrs['name']] =& $oValStatus;
    }

    if (!$oVDaemonStatus->bValid)
    {
        if ($oRuntime->sPage != $_SERVER['PHP_SELF'] || $oRuntime->sArgs != VDGetCurrentArgs())
        {
            $nId = mt_rand(10000000, 99999999);
            $_SESSION['VDaemonData']['ID'] = strval($nId);
            $_SESSION['VDaemonData']['POST'] = $_VDAEMON;
            $_SESSION['VDaemonData']['STATUS'] = serialize($oVDaemonStatus);

            $oRuntime->sArgs .= $oRuntime->sArgs ? '&' : '';
            $oRuntime->sArgs .= "vdaemonid=$nId&";
            if (SID)
            {
                $oRuntime->sArgs .= SID . '&';
            }
            $sLink = $oRuntime->sProtocol . $_SERVER['HTTP_HOST'] . $oRuntime->sPage;
            $sLink .=  $oRuntime->sArgs ? '?' . $oRuntime->sArgs : '';
            $sLink .=  $oRuntime->sAnchor ? '#' . $oRuntime->sAnchor : '';

            header("location: $sLink");
            exit;
        }
    }
}

function VDGetValue($sName, $bSession = false, $bQuotes = false)
{
    global $_VDAEMON;
    $sValue = null;
    
    if (preg_match('/^([^[]*)(\[(.*?)\])?/', $sName, $aMatches))
    {
        $sName = $aMatches[1];
        if (isset($aMatches[2]))
        {
            $sIdx = $aMatches[3];
            $sIdx = VDEscape($sIdx);
            $sIdx = str_replace('\'', '\\\'', $sIdx);
            if (preg_match('/^\d+$/', $sIdx))
            {
                $sIdx = intval($sIdx);
            }
        }
    }

    if (!$bSession && isset($_FILES[$sName]))
    {
        if (!isset($sIdx) || $sIdx == '')
        {
            $mRef =& $_FILES[$sName]['name'];
        }
        else
        {
            $mRef =& $_FILES[$sName]['name'][$sIdx];
        }
        
        if (isset($mRef))
        {
            $sValue = is_array($mRef) ? join(VDAEMON_DELIMITER, $mRef) : $mRef;
        }
    }
    else
    {
        if (!isset($sIdx) || $sIdx === '')
        {
            if ($bSession)
            {
                $mRef =& $_VDAEMON[$sName];
            }
            else
            {
                $mRef =& $_POST[$sName];
            }
        }
        else
        {
            if ($bSession)
            {
                $mRef =& $_VDAEMON[$sName][$sIdx];
            }
            else
            {
                $mRef =& $_POST[$sName][$sIdx];
            }
        }
        
        if (isset($mRef))
        {
            $sValue = $mRef;
            if (is_array($sValue))
            {
                foreach ($sValue as $nIdx => $mTmp)
                {
                    $sValue[$nIdx] = VDFormat($sValue[$nIdx], $bQuotes);
                }
                $sValue = join(VDAEMON_DELIMITER, $sValue);
            }
            else
            {
                $sValue = VDFormat($sValue, $bQuotes);
            }
        }
    }

    return $sValue;
}

function VDFormat($sValue, $bQuotes = false)
{
    $sValue = trim($sValue);
    if ($bQuotes xor get_magic_quotes_gpc())
    {
        if ($bQuotes)
        {
            $sValue = addslashes($sValue);
        }
        else
        {
            $sValue = stripslashes($sValue);
        }
    }

    return $sValue;
}

function VDGetCurrentArgs()
{
    $sArgs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    $nPos = strpos($sArgs, '#');
    if ($nPos !== false)
    {
        $sArgs = substr($sArgs, 0, $nPos);
    }
    
    $sSessName = session_name();
    $sArgs = preg_replace("/$sSessName=[^&]*&?/", '', $sArgs);
    $sArgs = preg_replace("/vdaemonid=[^&]*&?/", '', $sArgs);
    if (substr($sArgs, -1) == '&')
    {
        $sArgs = substr($sArgs, 0, strlen($sArgs) - 1);
    }
    
    return $sArgs;
}

function VDHtmlEncode($sValue)
{
    if (version_compare(phpversion(), '4.3', '<'))
    {
        $aVDTransTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        $aVDTransTable = array_flip($aVDTransTable);
        $aVDTransTable["&#039;"] = "'";

        $sValue = strtr($sValue, $aVDTransTable);
    }
    else
    {
        $sValue = html_entity_decode($sValue, ENT_QUOTES);
    }
    
    return htmlspecialchars($sValue); 
}

function VDEscape($sValue)
{
    return str_replace('\\', '\\\\', $sValue);
}

function VDErrorMessage($sErrMsg)
{
    return '<p style="font-family:Verdana,Arial,Helvetica;font-size:12px;margin-left:15px;margin-right:15px;"><b>VDaemon Error:</b> ' . $sErrMsg . "</p>\n";
}

//--------------------------------------------------------------------------
//                  class CVDFormStatus
//--------------------------------------------------------------------------

class CVDFormStatus
{
    var $sForm;
    var $bValid;
    var $aValidators;

    function __sleep()
    {
        $this->sForm = urlencode($this->sForm);
        return array('sForm','bValid','aValidators');
    }
    
    function __wakeup()
    {
        $this->sForm = urldecode($this->sForm);
        return array('sForm','bValid','aValidators');
    }
}

//--------------------------------------------------------------------------
//                  class CVDValidatorStatus
//--------------------------------------------------------------------------

class CVDValidatorStatus
{
    var $bValid;
    var $sErrMsg;

    function __sleep()
    {
        $this->sErrMsg = urlencode($this->sErrMsg);
        return array('bValid','sErrMsg');
    }
    
    function __wakeup()
    {
        $this->sErrMsg = urldecode($this->sErrMsg);
        return array('bValid','sErrMsg');
    }
}

//--------------------------------------------------------------------------
//                  class XmlNode
//--------------------------------------------------------------------------

class XmlNode
{
    var $sName;
    var $aAttrs = array();
    var $aSubNodes = array();
    var $nStart;
    var $nEnd;
    
    function XmlNode($sName, $aAttrs = null, $nStart = 0, $nEnd = 0)
    {
        $this->sName = $sName;
        $this->nStart = $nStart;
        $this->nEnd = $nEnd;
        if (is_array($aAttrs))
        {
            $this->aAttrs = $aAttrs;
        }
    }
    
    function __sleep()
    {
        $this->sName = urlencode($this->sName);
        foreach ($this->aAttrs as $sKey => $sValue)
        {
            $this->aAttrs[$sKey] = urlencode($this->aAttrs[$sKey]);
        }
        
        return array('sName','aAttrs','aSubNodes');
    }
    
    function __wakeup()
    {
        $this->sName = urldecode($this->sName);
        foreach ($this->aAttrs as $sKey => $sValue)
        {
            $this->aAttrs[$sKey] = urldecode($this->aAttrs[$sKey]);
        }
        
        return array('sName','aAttrs','aSubNodes');
    }
    
    function &AddSubNode($sName, $aAttrs = null, $nStart = 0, $nEnd = 0)
    {
        $oSubNode =& new XmlNode($sName, $aAttrs, $nStart, $nEnd);
        $this->aSubNodes[] =& $oSubNode;
        
        return $oSubNode;
    }
    
    function &FindSubNode($sName)
    {
        $oReturn = false;
        
        if ($this->sName == $sName)
        {
            $oReturn =& $this;
        }
        else
        {
            foreach ($this->aSubNodes as $sKey => $oSubNode)
            {
                if (is_object($this->aSubNodes[$sKey]))
                {
                    $oReturn =& $this->aSubNodes[$sKey]->FindSubNode($sName);
                    if ($oReturn !== false)
                    {
                        return $oReturn;
                    }
                }
            }
        }
        
        return $oReturn;
    }
    
    function Serialize()
    {
        $sXml = '<' . $this->sName;
        
        foreach ($this->aAttrs as $sName => $sValue)
        {
            if ($sValue === true)
            {
                $sValue = 'true';
            }
            $sXml .= ' ' . $sName . '="' . VDHtmlEncode($sValue) . '"';
        }
        
        if (count($this->aSubNodes) > 0)
        {
            $sXml .= '>';
            foreach ($this->aSubNodes as $sValue)
            {
                if (is_object($sValue))
                {
                    $sXml .= $sValue->Serialize();
                }
                else
                {
                    $sXml .= $sValue;
                }
            }
            
            $sXml .= '</' . $this->sName . '>';
        }
        else
        {
            $sXml .= ' />';
        }
        
        return $sXml;
    }

    function SerializeWithoutRoot()
    {
        $sXml = '';
        foreach ($this->aSubNodes as $sValue)
        {
            if (is_object($sValue))
            {
                $sXml .= $sValue->Serialize();
            }
            else
            {
                $sXml .= $sValue;
            }
        }
        
        return $sXml;
    }
}

//--------------------------------------------------------------------------
//                  class CVDPage
//--------------------------------------------------------------------------

class CVDPage
{
    var $sHtml;
    var $aForms;
    var $aFormlessNodes;
    var $oScriptNode;
    
    var $sError;
    var $aDepthNodes;
    var $nDepth;
    var $nCharNodeStart;
    var $nCharNodeEnd;
    var $bScript;
    var $sForm;
    var $oLabel;
    var $oGroup;
    var $bTextarea;
    var $bSelect;
    var $bOption;
    var $nIdCount;
    
    function CVDPage($sSource)
    {
        $this->sHtml = $sSource;
        $this->aForms = array();
        $this->aFormlessNodes = array();
        $this->sError = '';
    }
    
    function ProcessPage()
    {
        $this->ParsePage();
        if ($this->sError != '')
        {
            return $this->sError;
        }
        
        $this->CheckSyntax();
        if ($this->sError != '')
        {
            return $this->sError;
        }
        
        $this->Prepare();
        
        return $this->aDepthNodes[0]->SerializeWithoutRoot();
    }
    
    function ParsePage()
    {
        $oSaxParser =& new XML_HTMLSax();
        $oSaxParser->set_object($this);
        $oSaxParser->set_element_handler('StartElement','EndElement');
        $oSaxParser->set_data_handler('CharacterData');
        
        $this->aDepthNodes = array();
        $this->aDepthNodes[0] =& new XmlNode('ROOT', array());
        $this->nDepth = 1;
        $this->nCharNodeStart = 0;
        $this->nCharNodeEnd = 0;
        $this->bScript = false;
        $this->sForm = '';
        $this->oLabel = null;
        $this->oGroup = null;
        $this->bTextarea = false;
        $this->bSelect = false;
        $this->bOption = false;
        $this->nIdCount = 1;
        
        $oSaxParser->parse($this->sHtml);
        $this->AddCharNode(); 
        
        if ($this->sError == '')
        {
            if ($this->sForm != '')
            {
                $this->MakeErrorMessage(sprintf(VD_E_UNCLOSED_FORM, $this->sForm), null);
            }
            foreach ($this->aFormlessNodes as $nIdx => $mTmp)
            {
                $oFlNode =& $this->aFormlessNodes[$nIdx];
                $this->MakeErrorMessage(VD_E_INVALID_FORM_ATTRIBUTE, $oFlNode);
            }
        }
        
        unset($oSaxParser);
    }
    
    function StartElement(&$oParser, $sName, $aAttrs) 
    {
        if ($this->sError)
        {
            return;
        }
        
        if (!$this->bScript)
        {
            $sNameL = strtolower($sName);
            $aAttrsL = array_change_key_case($aAttrs, CASE_LOWER);
            
            switch ($sNameL)
            {
                case 'script':
                    $this->bScript = true;
                    break;
                
                case 'body':
                    $this->nCharNodeEnd = $oParser->get_current_position();
                    $this->oScriptNode =& $this->AddCustomNode('');
                    break;
                
                case 'form':
                {
                    if ($this->sForm == '' &&
                        isset($aAttrsL['runat']) &&
                        strcasecmp($aAttrsL['runat'], 'vdaemon') == 0)
                    {
                        if (!isset($this->oScriptNode))
                        {
                            $this->oScriptNode =& $this->AddCustomNode('');
                        }
                        
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        if (isset($aAttrsL['name']) && $aAttrsL['name'] != '')
                        {
                            $this->nDepth++;
                            $this->sForm = $aAttrsL['name'];
                            $this->aForms[$this->sForm] =& new CVDForm($oNode);
                            
                            foreach ($this->aFormlessNodes as $nIdx => $mTmp)
                            {
                                $oFlNode =& $this->aFormlessNodes[$nIdx];
                                if ($oFlNode->aAttrs['form'] == $this->sForm)
                                {
                                    if ($oFlNode->sName == 'vlsummary')
                                    {
                                        $this->aForms[$this->sForm]->AddSummary($oFlNode, $this->MakeId());
                                    }
                                    elseif ($oFlNode->sName == 'vllabel')
                                    {
                                        $this->aForms[$this->sForm]->AddLabel($oFlNode, $this->MakeId());
                                    }
                                    
                                    unset($this->aFormlessNodes[$nIdx]);
                                }
                            }
                        }
                        else
                        {
                            $this->MakeErrorMessage(VD_E_UNNAMED_FORM, $oNode);
                        }
                    }
                    elseif ($this->sForm != '' && !$this->bTextarea)
                    {
                        $this->MakeErrorMessage(sprintf(VD_E_UNCLOSED_FORM, $this->sForm), null);
                    }
                }
                break;
                
                case 'vlsummary':
                {
                    if (!$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        if (isset($aAttrsL['form']))
                        {
                            if (isset($this->aForms[$aAttrsL['form']]))
                            {
                                $this->aForms[$aAttrsL['form']]->AddSummary($oNode, $this->MakeId());
                            }
                            else
                            {
                                $this->aFormlessNodes[] =& $oNode;
                            }
                        }
                        else
                        {
                            if ($this->sForm != '')
                            {
                                $this->aForms[$this->sForm]->AddSummary($oNode, $this->MakeId());
                            }
                            else
                            {
                                $this->MakeErrorMessage(VD_E_FORMLESS_SUMMARY, $oNode);
                            }
                        }
                    }
                }
                break;
                
                case 'vllabel':
                {
                    if (!$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->nDepth++;
                        $this->oLabel =& $oNode;
                        if (isset($aAttrsL['form']))
                        {
                            if (isset($this->aForms[$aAttrsL['form']]))
                            {
                                $this->aForms[$aAttrsL['form']]->AddLabel($oNode, $this->MakeId());
                            }
                            else
                            {
                                $this->aFormlessNodes[] =& $oNode;
                            }
                        }
                        else
                        {
                            if ($this->sForm != '')
                            {
                                $this->aForms[$this->sForm]->AddLabel($oNode, $this->MakeId());
                            }
                            else
                            {
                                $this->MakeErrorMessage(VD_E_FORMLESS_LABEL, $oNode);
                            }
                        }
                    }
                }
                break;
                
                case 'vlvalidator':
                {
                    if (!$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        if ($this->sForm == '')
                        {
                            $this->MakeErrorMessage(VD_E_FORMLESS_VALIDATOR, $oNode);
                        }
                        elseif (!$this->oGroup)
                        {
                            $sErrMsg = $this->aForms[$this->sForm]->AddValidator($oNode);
                            if ($sErrMsg)
                            {
                                $this->MakeErrorMessage($sErrMsg, $oNode);
                            }
                        }
                    }
                }
                break;
                
                case 'vlgroup':
                {
                    if (!$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->nDepth++;
                        $this->oGroup =& $oNode;
                        if ($this->sForm != '')
                        {
                            $sErrMsg = $this->aForms[$this->sForm]->AddValidator($oNode);
                            if ($sErrMsg)
                            {
                                $this->MakeErrorMessage($sErrMsg, $oNode);
                            }
                        }
                        else
                        {
                            $this->MakeErrorMessage(VD_E_FORMLESS_VALIDATOR, $oNode);
                        }
                    }
                }
                break;
                
                case 'input':
                    if ($this->sForm != '' && !$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->aForms[$this->sForm]->AddControl($oNode);
                    }
                    break;
                
                case 'textarea':
                    if ($this->sForm != '' && !$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->aForms[$this->sForm]->AddControl($oNode);
                        $this->nDepth++;
                        $this->bTextarea = true;
                    }
                    break;
                
                case 'select':
                    if ($this->sForm != '' && !$this->bTextarea && !$this->bSelect)
                    {
                        $oNode =& $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->aForms[$this->sForm]->AddControl($oNode);
                        $this->nDepth++;
                        $this->bSelect = true;
                    }
                    break;
                
                case 'option':
                {
                    if ($this->bSelect)
                    {
                        if ($this->bOption)
                        {
                            $this->AddCharNode();
                            $this->nDepth--;
                        }
                        $this->AddElementNode($oParser, $sNameL, $aAttrsL);
                        $this->nDepth++;
                        $this->bOption = true;
                    }
                }
                break;
            }
        }
        
        $this->nCharNodeEnd = $oParser->get_current_position();
    }
    
    function EndElement(&$oParser, $sName)
    {
        if ($this->sError)
        {
            return;
        }
        
        $sNameL = strtolower($sName);
        
        switch ($sNameL)
        {
            case 'script':
                $this->bScript = false;
                break;
            
            case 'form':
                if ($this->sForm != '' && !$this->bTextarea && !$this->bSelect)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    $this->nDepth--;
                    $this->sForm = '';
                    
                    if ($this->oLabel)
                    {
                        $this->MakeErrorMessage(VD_E_UNCLOSED_LABEL, $this->oLabel);
                    }
                    if ($this->oGroup)
                    {
                        $this->MakeErrorMessage(VD_E_UNCLOSED_GROUP, $this->oLabel);
                    }
                }
                break;
            
            case 'vllabel':
                if ($this->oLabel)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    $this->nDepth--;
                    unset($this->oLabel);
                    $this->oLabel = null;
                }
                break;
            
            case 'vlgroup':
                if ($this->oGroup)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    $this->nDepth--;
                    unset($this->oGroup);
                    $this->oGroup = null;
                }
                break;
            
            case 'textarea':
                if ($this->bTextarea)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    $this->nDepth--;
                    $this->bTextarea = false;
                }
                break;
            
            case 'select':
                if ($this->bSelect)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    if ($this->bOption)
                    {
                        $this->nDepth--;
                        $this->bOption = false;
                    }
                    $this->nDepth--;
                    $this->bSelect = false;
                }
                break;
            
            case 'option':
                if ($this->bOption)
                {
                    $this->AddCharNode();
                    $this->nCharNodeStart = $oParser->get_current_position();
                    $this->nDepth--;
                    $this->bOption = false;
                }
                break;
        }
        
        $this->nCharNodeEnd = $oParser->get_current_position();
    }
    
    function CharacterData(&$oParser, $sData)
    {
        if ($this->sError)
        {
            return;
        }
        
        $this->nCharNodeEnd = $oParser->get_current_position();
    }

    function AddCharNode()
    {
        if ($this->nCharNodeEnd > $this->nCharNodeStart)
        {
            $this->aDepthNodes[$this->nDepth - 1]->aSubNodes[] =
                substr($this->sHtml, $this->nCharNodeStart, $this->nCharNodeEnd - $this->nCharNodeStart);
            $this->nCharNodeStart = $this->nCharNodeEnd;
        }
    }
    
    function &AddCustomNode($sText)
    {
        $this->AddCharNode();
        $this->aDepthNodes[$this->nDepth - 1]->aSubNodes[] =& $sText;
        
        return $sText;
    }
    
    function &AddElementNode(&$oParser, $sName, $aAttrs)
    {
        $nStart = $this->nCharNodeEnd;
        $nEnd = $oParser->get_current_position();
        
        $this->AddCharNode();
        $this->nCharNodeStart = $nEnd;
        $oNode =& new XmlNode($sName, $aAttrs, $nStart, $nEnd);
        
        if ($this->oLabel)
        {
            $this->MakeErrorMessage(VD_E_UNCLOSED_LABEL, $this->oLabel);
        }
        elseif ($this->oGroup && $sName != 'vlvalidator')
        {
            $this->MakeErrorMessage(VD_E_UNCLOSED_GROUP, $this->oGroup);
        }
        else
        {
            $this->aDepthNodes[$this->nDepth] =& $oNode;
            $this->aDepthNodes[$this->nDepth - 1]->aSubNodes[] =& $oNode;
        }
        
        return $oNode;
    }
    
    function CheckSyntax()
    {
        foreach ($this->aForms as $sFormName => $mTmp)
        {
            $this->aForms[$sFormName]->CheckSyntax($this);
        }
    }
    
    function GetClientScript()
    {
$sScript = '
<script type="text/JavaScript" src="'.PATH_TO_VDAEMON_JS.'"></script>
<script type="text/JavaScript">
<!--

vdDelimiter="'.VDAEMON_DELIMITER.'";
var f,v,i,l,s;
';
            foreach ($this->aForms as $sName => $mTmp)
            {
                $sScript .= $this->aForms[$sName]->GetClientScript();
            }
            
$sScript .= '
//-->
</script>
';
        return $sScript;
    }
    
    function Prepare()
    {
        $bClientValidate = false;
        foreach ($this->aForms as $sName => $mTmp)
        {
            $bClientValidate = $bClientValidate || $this->aForms[$sName]->ClientValidate();
        }
        
        if ($bClientValidate && isset($this->oScriptNode))
        {
            $sScript = $this->GetClientScript();
            $this->oScriptNode = $sScript;
        }
        
        foreach ($this->aForms as $sName => $mTmp)
        {
            $this->aForms[$sName]->Prepare();
        }
    }
    
    function MakeId()
    {
        $sId = 'VDaemonID_' . $this->nIdCount;
        $this->nIdCount++;
        
        return $sId;
    }
    
    function MakeErrorMessage($sErrMsg, $oErrNode)
    {
        $sError = VDErrorMessage($sErrMsg);

        if ($oErrNode && $oErrNode->nStart < $oErrNode->nEnd)
        {
            $sPrefix = '';
            $sBuffer = substr($this->sHtml, 0, $oErrNode->nStart);
            for ($nIdx = 0; $nIdx < 3 && $sBuffer != ''; $nIdx++)
            {
                $nPos = strrpos($sBuffer, "\n");
                if ($nPos === false)
                {
                    $nPos = 0;
                }
                $sPrefix = substr($sBuffer, $nPos) . $sPrefix;
                $sBuffer = substr($sBuffer, 0, $nPos);
            }
            $sPrefix = htmlspecialchars(ltrim($sPrefix, "\r\n"));
                
            $sSuffix = '';
            $sBuffer = substr($this->sHtml, $oErrNode->nEnd);
            for ($nIdx = 0; $nIdx < 3 && $sBuffer != ''; $nIdx++)
            {
                $nPos = strpos($sBuffer, "\n");
                if ($nPos === false)
                {
                    $nPos = strlen($sBuffer) - 1;
                }
                $sSuffix .= substr($sBuffer, 0, $nPos + 1);
                $sBuffer = substr($sBuffer, $nPos + 1);
            }
            $sSuffix = htmlspecialchars(rtrim($sSuffix, "\r\n"));

            $sBody = substr($this->sHtml, $oErrNode->nStart, $oErrNode->nEnd - $oErrNode->nStart);
            $sBody = '<b>' . htmlspecialchars($sBody) . '</b>';
            $sError .= '<pre style="font-family:\'Courier New\',Courier,mono;font-size:11px;margin-left:15px;margin-right:15px;padding:5px;border:1px solid #999999;background-color:#CCCCCC;">';
            $sError .= $sPrefix . $sBody . $sSuffix;
            $sError .= "</pre>\n";
        }
        
        $this->sError .= $sError;
    }
}

//--------------------------------------------------------------------------
//                  class CVDForm
//--------------------------------------------------------------------------

class CVDForm
{
    var $oNode;
    var $aSummaries;
    var $aLabels;
    var $aValidators;
    var $aControls;
    var $bClientValidate;
    
    function CVDForm(&$oNode)
    {
        $this->oNode =& $oNode;
        $this->aSummaries = array();
        $this->aLabels = array();
        $this->aValidators = array();
        $this->aControls = array();
    }
    
    function AddSummary(&$oNode, $sId)
    {
        $this->aSummaries[] =& new CVDSummary($oNode, $sId);
    }
    
    function AddLabel(&$oNode, $sId)
    {
        $this->aLabels[] =& new CVDLabel($oNode, $sId);
    }
    
    function AddValidator(&$oNode)
    {
        if (!isset($oNode->aAttrs['name']) || $oNode->aAttrs['name'] == '')
        {
            return VD_E_UNNAMED_VALIDATOR;
        }
        if (isset($this->aValidators[$oNode->aAttrs['name']]))
        {
            return VD_E_VALIDATOR_NAME;
        }
        
        $this->aValidators[$oNode->aAttrs['name']] =& new CVDValidator($oNode);
        return '';
    }
    
    function AddControl(&$oNode)
    {
        if (isset($oNode->aAttrs['name']) && $oNode->aAttrs['name'] != '')
        {
            if (!isset($this->aControls[$oNode->aAttrs['name']]))
            {
                $this->aControls[$oNode->aAttrs['name']] =& new CVDControl($oNode->aAttrs['name']);
            }
            $this->aControls[$oNode->aAttrs['name']]->AddInput($oNode);
        }
    }
    
    function CheckSyntax(&$oPage)
    {
        if (!isset($this->oNode->aAttrs['method']) || strcasecmp($this->oNode->aAttrs['method'], 'post') != 0)
        {
            $oPage->MakeErrorMessage(VD_E_FORM_METHOD, $this->oNode);
        }
        
        foreach ($this->aValidators as $sName => $mTmp)
        {
            $aErrors = $this->aValidators[$sName]->CheckSyntax($this);
            foreach ($aErrors as $sError)
            {
                $oPage->MakeErrorMessage($sError, $this->aValidators[$sName]->oNode);
            }
        }
        foreach ($this->aLabels as $nIdx => $mTmp)
        {
            $this->aLabels[$nIdx]->CheckSyntax($oPage, $this);
        }
        foreach ($this->aSummaries as $nIdx => $mTmp)
        {
            $this->aSummaries[$nIdx]->CheckSyntax($oPage);
        }
    }
    
    function ClientValidate()
    {
        if (!isset($this->bClientValidate))
        {
            if (isset($this->oNode->aAttrs['clientvalidate']) &&
                strtolower($this->oNode->aAttrs['clientvalidate']) == 'false')
            {
                $this->bClientValidate = false;
            }
            else
            {
                $bVals = false;
                foreach ($this->aValidators as $sName => $mTmp)
                {
                    $bVals = $bVals || $this->aValidators[$sName]->ClientValidate();
                }
                
                $this->bClientValidate = $bVals;
            }
        }
        
        return $this->bClientValidate;
    }
    
    function GetClientScript()
    {
        $sScript = '';
        if ($this->ClientValidate())
        {
            $sName = VDEscape($this->oNode->aAttrs['name']);
            $sScript = "\nf=new Object(); f.name=\"$sName\"; f.validators=new Array(); f.labels=new Array(); f.summaries=new Array();\n";

            foreach ($this->aValidators as $sName => $mTmp)
            {
                $sScript .= $this->aValidators[$sName]->GetClientScript();
            }
            foreach ($this->aLabels as $nIdx => $mTmp)
            {
                $sScript .= $this->aLabels[$nIdx]->GetClientScript();
            }
            foreach ($this->aSummaries as $nIdx => $mTmp)
            {
                $sScript .= $this->aSummaries[$nIdx]->GetClientScript();
            }
            
            $sScript .= "vdAllForms[f.name]=f;\n";
        }
        
        return $sScript;
    }
    
    function Prepare()
    {
        global $oVDaemonStatus;
        
        if ($this->ClientValidate())
        {
            $sName = VDEscape($this->oNode->aAttrs['name']);
            $this->oNode->aAttrs['onsubmit'] = "return VDValidateForm('$sName');";
        }
        
        foreach ($this->aLabels as $nIdx => $mTmp)
        {
            $this->aLabels[$nIdx]->Prepare($this);
        }
        foreach ($this->aSummaries as $nIdx => $mTmp)
        {
            $this->aSummaries[$nIdx]->Prepare($this);
        }
        
        foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
        {
            $oSubNode =& $this->oNode->aSubNodes[$nIdx];
            if (is_object($oSubNode) && in_array($oSubNode->sName, array('vlvalidator','vlgroup')))
            {
                unset($this->oNode->aSubNodes[$nIdx]);
            }
        }
        
        foreach ($this->aControls as $sName => $mTmp)
        {
            $this->aControls[$sName]->Prepare();
        }
        
        if ($oVDaemonStatus &&
            $this->oNode->aAttrs['name'] == $oVDaemonStatus->sForm &&
            !$oVDaemonStatus->bValid &&
            (!isset($this->oNode->aAttrs['populate']) || strtolower($this->oNode->aAttrs['populate']) != 'false'))
        {
            foreach ($this->aControls as $sName => $mTmp)
            {
                $this->aControls[$sName]->Populate();
            }
        }
        
        $oRuntime =& new CVDValRuntime();
        $oRuntime->Fill($this);
        
        $this->oNode->aSubNodes[] = "\n";
        $sValue = serialize($oRuntime);
        if (!$sValue)
        {
            VDErrorMessage(VD_E_SERIALIZE);
            exit;
        }
        $this->oNode->aSubNodes[] =& new XmlNode('input', array('type' => 'hidden',
                                                                'name' => 'VDaemonValidators',
                                                                'value' => $sValue));
        $this->oNode->aSubNodes[] = "\n";
        
        unset($this->oNode->aAttrs['clientvalidate']);
        unset($this->oNode->aAttrs['runat']);
        unset($this->oNode->aAttrs['populate']);
    }
}

//--------------------------------------------------------------------------
//                  class CVDSummary
//--------------------------------------------------------------------------

class CVDSummary
{
    var $oNode;
    var $sId;
    
    function CVDSummary(&$oNode, $sId)
    {
        $this->oNode =& $oNode;
        $this->sId = $sId;
    }
    
    function CheckSyntax(&$oPage)
    {
        if (isset($this->oNode->aAttrs['displaymode']) &&
            !in_array(strtolower($this->oNode->aAttrs['displaymode']), array('list','bulletlist','paragraph')))
        {
            $oPage->MakeErrorMessage(VD_E_DISPLAYMODE_INVALID, $this->oNode);
        }
    }
    
    function GetClientScript()
    {
        $sScript = '';
        $sScript = "s=new Object(); s.id=\"$this->sId\";";

        $sHeaderText = isset($this->oNode->aAttrs['headertext']) ? VDEscape($this->oNode->aAttrs['headertext']) : '';
        $sScript .= " s.headertext=\"$sHeaderText\";";
        
        $sDisplayMode = isset($this->oNode->aAttrs['displaymode']) ?
            strtolower($this->oNode->aAttrs['displaymode']) : 'list';
        $sScript .= " s.displaymode=\"$sDisplayMode\";";
        
        $sShowSummary = (isset($this->oNode->aAttrs['showsummary']) &&
            strtolower($this->oNode->aAttrs['showsummary']) == 'false') ? 'false' : 'true';
        $sScript .= " s.showsummary=$sShowSummary;";

        $sMessageBox = (isset($this->oNode->aAttrs['messagebox']) &&
            strtolower($this->oNode->aAttrs['messagebox']) != 'false') ? 'true' : 'false';
        $sScript .= " s.messagebox=$sMessageBox;";

        $sScript .= " f.summaries[f.summaries.length]=s;\n";
        
        return $sScript;
    }
    
    function Prepare(&$oForm)
    {
        global $oVDaemonStatus;
        $bValid = true;
        
        if ($oVDaemonStatus && $oForm->oNode->aAttrs['name'] == $oVDaemonStatus->sForm)
        {
            $bValid = $oVDaemonStatus->bValid;
        }
        
        $bShowSummary = (isset($this->oNode->aAttrs['showsummary']) &&
            strtolower($this->oNode->aAttrs['showsummary']) == 'false') ? false : true;
        if (!$bValid && $bShowSummary)
        {
            $sDisplayMode = isset($this->oNode->aAttrs['displaymode']) ?
                strtolower($this->oNode->aAttrs['displaymode']) : 'list';
            switch ($sDisplayMode)
            {
                case "list":
                default: 
                    $sHeaderSep = '<br>';
                    $sFirst = '';
                    $sPre = '';
                    $sPost = '<br>';
                    $sLast = '';
                    break;
                    
                case "bulletlist":
                    $sHeaderSep = '';
                    $sFirst = '<ul>';
                    $sPre = '<li>';
                    $sPost = '</li>';
                    $sLast = '</ul>';
                    break;
                    
                case "paragraph":
                    $sHeaderSep = ' ';
                    $sFirst = '';
                    $sPre = '';
                    $sPost = ' ';
                    $sLast = '';
                    break;
            }
            
            $sSummary = '';
            foreach ($oVDaemonStatus->aValidators as $oValidator)
            {
                if (!$oValidator->bValid && $oValidator->sErrMsg != '')
                {
                    $sSummary .= $sPre . $oValidator->sErrMsg . $sPost;
                }
            }
            
            if ($sSummary != '')
            {
                $sSummary = $sHeaderSep . $sFirst . $sSummary . $sLast;
            }
            if (isset($this->oNode->aAttrs['headertext']))
            {
                $sSummary = $this->oNode->aAttrs['headertext'] . $sSummary;
            }
            
            if ($sSummary != '')
            {
                $this->oNode->aSubNodes = array();
                $this->oNode->aSubNodes[] = $sSummary;
            }
        }
        
        $this->oNode->sName = 'div';
        $this->oNode->aAttrs['id'] = $this->sId;
        if (!$this->oNode->aSubNodes)
        {
            $this->oNode->aAttrs['style'] = 'display:none';
            //$this->oNode->aSubNodes[] = $bShowSummary ? '&nbsp;' : '';
            $this->oNode->aSubNodes[] = '';
        }
        
        unset($this->oNode->aAttrs['headertext']);
        unset($this->oNode->aAttrs['displaymode']);
        unset($this->oNode->aAttrs['showsummary']);
        unset($this->oNode->aAttrs['messagebox']);
        unset($this->oNode->aAttrs['form']);
    }
}

//--------------------------------------------------------------------------
//                  class CVDLabel
//--------------------------------------------------------------------------

class CVDLabel
{
    var $oNode;
    var $sId;
    
    function CVDLabel(&$oNode, $sId)
    {
        $this->oNode =& $oNode;
        $this->sId = $sId;
    }
    
    function CheckSyntax(&$oPage, &$oForm)
    {
        if (!isset($this->oNode->aAttrs['validators']))
        {
            $oPage->MakeErrorMessage(VD_E_VALIDATORS_MISSED, $this->oNode);
        }
        else
        {
            $aValidators = explode(',', $this->oNode->aAttrs['validators']);
            foreach ($aValidators as $sValidator)
            {
                $sValidator = trim($sValidator);
                if (!isset($oForm->aValidators[$sValidator]))
                {
                    $oPage->MakeErrorMessage(VD_E_VALIDATOR_NOT_FOUND, $this->oNode);
                }
            }
        }
    }
    
    function GetClientScript()
    {
        $sScript = '';
        $sScript = "l=new Object(); l.id=\"$this->sId\";";

        $sOkText = VDEscape($this->oNode->SerializeWithoutRoot());
        $sOkText = str_replace('"', '\"', $sOkText);
        $sOkText = preg_replace('/\s+/', ' ', $sOkText);
        $sScript .= " l.oktext=\"$sOkText\";";
        
        $sErrText = isset($this->oNode->aAttrs['errtext']) ? VDEscape($this->oNode->aAttrs['errtext']) : $sOkText;
        $sScript .= " l.errtext=\"$sErrText\";";
        
        $sOkClass = isset($this->oNode->aAttrs['class']) ? VDEscape($this->oNode->aAttrs['class']) : '';
        $sScript .= " l.okclass=\"$sOkClass\";";
        
        $sErrClass = isset($this->oNode->aAttrs['errclass']) ? VDEscape($this->oNode->aAttrs['errclass']) : $sOkClass;
        $sScript .= " l.errclass=\"$sErrClass\";";

        $sScript .= " l.validators=new Array(";
        $aValidators = explode(',', $this->oNode->aAttrs['validators']);
        foreach ($aValidators as $nIdx => $mTmp)
        {
            $aValidators[$nIdx] = '"' . trim(VDEscape($aValidators[$nIdx])) . '"';
        }
        $sScript .= join(',', $aValidators) . ');';
        
        $sScript .= "f.labels[f.labels.length]=l;\n";
        
        return $sScript;
    }
    
    function Prepare(&$oForm)
    {
        global $oVDaemonStatus;
        $bValid = true;
        
        if ($oVDaemonStatus && $oForm->oNode->aAttrs['name'] == $oVDaemonStatus->sForm && !$oVDaemonStatus->bValid)
        {
            $aValidators = explode(',', $this->oNode->aAttrs['validators']);
            foreach ($aValidators as $sValidator)
            {
                $sValidator = trim($sValidator);
                if (isset($oVDaemonStatus->aValidators[$sValidator]))
                {
                    $bValid = $bValid && $oVDaemonStatus->aValidators[$sValidator]->bValid;
                }
            }
        }
        
        if (!$bValid)
        {
            if (isset($this->oNode->aAttrs['errclass']))
            {
                $this->oNode->aAttrs['class'] = $this->oNode->aAttrs['errclass'];
            }
            if (isset($this->oNode->aAttrs['errtext']))
            {
                $this->oNode->aSubNodes = array();
                $this->oNode->aSubNodes[] = $this->oNode->aAttrs['errtext'];
            }
        }
        
        $this->oNode->sName = 'label';
        $this->oNode->aAttrs['id'] = $this->sId;
        if (!$this->oNode->aSubNodes)
        {
            $this->oNode->aSubNodes[] = '';
        }
        
        unset($this->oNode->aAttrs['errtext']);
        unset($this->oNode->aAttrs['errclass']);
        unset($this->oNode->aAttrs['validators']);
        unset($this->oNode->aAttrs['form']);
    }
}

//--------------------------------------------------------------------------
//                  class CVDValidator
//--------------------------------------------------------------------------

class CVDValidator
{
    var $oNode;
    var $bClientValidate;
    
    function CVDValidator(&$oNode)
    {
        $this->oNode =& $oNode;
    }
    
    function CheckSyntax($oForm)
    {
        $aErrors = array();
        
        if ($this->oNode->sName == 'vlgroup')
        {
            foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
            {
                $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                if (!is_object($oSubNode))
                {
                    if (trim($oSubNode) != '')
                    {
                        $aErrors[] = VD_E_GROUP_CONTENT;
                    }
                    else
                    {
                        unset($this->oNode->aSubNodes[$nIdx]);
                    }
                }
                elseif ($oSubNode->sName != 'vlvalidator')
                {
                    $aErrors[] = VD_E_GROUP_CONTENT;
                }
                else
                {
                    $oSubValidator =& new CVDValidator($oSubNode);
                    $aErrors = array_merge($aErrors, $oSubValidator->CheckSyntax($oForm));
                }
            }
            
            if (!$this->oNode->aSubNodes)
            {
                $aErrors[] = VD_E_GROUP_EMPTY;
            }
        }
        else
        {
            if (!isset($this->oNode->aAttrs['type']))
            {
                $aErrors[] = VD_E_UNTYPED_VALIDATOR;
            }
            else
            {
                $sType = strtolower($this->oNode->aAttrs['type']);
                if (!isset($this->oNode->aAttrs['control']))
                {
                    if ($sType != 'custom')
                    {
                        $aErrors[] = VD_E_CONTROL_MISSED;
                    }
                }
                elseif ($oForm && !isset($oForm->aControls[$this->oNode->aAttrs['control']]))
                {
                    $aErrors[] = VD_E_VALIDATOR_CONTROL;
                }
                
                switch ($sType)
                {
                    default:
                        $aErrors[] = VD_E_VALIDATOR_TYPE;
                        break;
                    
                    case 'required':
                        if (isset($this->oNode->aAttrs['minlength']) &&
                            !preg_match('/^(0|[1-9]\d*)$/', $this->oNode->aAttrs['minlength']))
                        {
                            $aErrors[] = VD_E_MINLENGTH_INVALID;
                        }
                        if (isset($this->oNode->aAttrs['maxlength']) &&
                            !preg_match('/^(0|[1-9]\d*)$/', $this->oNode->aAttrs['maxlength']))
                        {
                            $aErrors[] = VD_E_MAXLENGTH_INVALID;
                        }
                        break;
                        
                    case 'email':
                        break;
                    
                    case 'checktype':
                    case 'range':
                    case 'compare':
                    {
                        if (!isset($this->oNode->aAttrs['validtype']))
                        {
                            $aErrors[] = VD_E_VALIDTYPE_MISSED;
                        }
                        elseif (!in_array(strtolower($this->oNode->aAttrs['validtype']),
                                          array('string','integer','float','date','currency')))
                        {
                            $aErrors[] = VD_E_VALIDTYPE_INVALID;
                        }
                        elseif (strtolower($this->oNode->aAttrs['validtype']) == 'date' &&
                                isset($this->oNode->aAttrs['dateorder']) &&
                                !in_array(strtolower($this->oNode->aAttrs['dateorder']), array('ymd','dmy','mdy')))
                        {
                            $aErrors[] = VD_E_DATEORDER_INVALID;
                        }
                        elseif ($sType == 'range')
                        {
                            if (!isset($this->oNode->aAttrs['minvalue']))
                            {
                                $aErrors[] = VD_E_MINVALUE_MISSED;
                            }
                            elseif ($this->Convert($this->oNode->aAttrs['minvalue']) === false)
                            {
                                $aErrors[] = VD_E_MINVALUE_INVALID;
                            }
                            
                            if (!isset($this->oNode->aAttrs['maxvalue']))
                            {
                                $aErrors[] = VD_E_MAXVALUE_MISSED;
                            }
                            elseif ($this->Convert($this->oNode->aAttrs['maxvalue']) === false)
                            {
                                $aErrors[] = VD_E_MAXVALUE_INVALID;
                            }
                        }
                        elseif ($sType == 'compare')
                        {
                            if (!isset($this->oNode->aAttrs['operator']))
                            {
                                $aErrors[] = VD_E_OPERATOR_MISSED;
                            }
                            elseif (!in_array(strtolower($this->oNode->aAttrs['operator']), array('e','ne','g','ge','l','le')))
                            {
                                $aErrors[] = VD_E_OPERATOR_INVALID;
                            }
                            
                            if (isset($this->oNode->aAttrs['comparevalue']))
                            {
                                if ($this->Convert($this->oNode->aAttrs['comparevalue']) === false)
                                {
                                    $aErrors[] = VD_E_COMPAREVALUE_INVALID;
                                }
                            }
                            elseif (isset($this->oNode->aAttrs['comparecontrol']))
                            {
                                if ($oForm && !isset($oForm->aControls[$this->oNode->aAttrs['comparecontrol']]))
                                {
                                    $aErrors[] = VD_E_COMPARECONTROL_NOT_FOUND;
                                }
                            }
                            else
                            {
                                $aErrors[] = VD_E_COMPAREVALUE_MISSED;
                            }
                        }
                    }
                    break;
                    
                    case 'regexp':
                        if (!isset($this->oNode->aAttrs['regexp']))
                        {
                            $aErrors[] = VD_E_REGEXP_MISSED;
                        }
                        break;
                    
                    case 'custom':
                        if (!isset($this->oNode->aAttrs['function']))
                        {
                            $aErrors[] = VD_E_FUNCTION_MISSED;
                        }
                        elseif (!preg_match('/^[a-zA-Z_]\w*$/', $this->oNode->aAttrs['function']))
                        {
                            $aErrors[] = VD_E_FUNCTION_INVALID;
                        }
                        break;
                }
            }
        }
        
        return $aErrors;
    }
    
    function ClientValidate()
    {
        if (!isset($this->bClientValidate))
        {
            $bResult = true;
            
            if (isset($this->oNode->aAttrs['clientvalidate']) &&
                strtolower($this->oNode->aAttrs['clientvalidate']) == 'false')
            {
                $bResult = false;
            }
            else
            {
                $sType = ($this->oNode->sName == 'vlgroup') ? 'group' : strtolower($this->oNode->aAttrs['type']);
                if ($sType == 'custom' && !isset($this->oNode->aAttrs['clientfunction']))
                {
                    $bResult = false;
                }
                
                if ($sType == 'group')
                {
                    $bResult = false;
                    foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
                    {
                        $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                        if (is_object($oSubNode) && $oSubNode->sName == 'vlvalidator')
                        {
                            $oSubValidator =& new CVDValidator($oSubNode);
                            $bResult = $bResult || $oSubValidator->ClientValidate();
                        }
                    }
                }
            }
            
            $this->bClientValidate = $bResult;
        }
        
        return $this->bClientValidate;
    }
    
    function GetClientScript($bInGroup = false)
    {
        $sScript = '';
        if ($this->ClientValidate())
        {
            $sLet = $bInGroup ? 'i' : 'v';
            $sScript = "$sLet=new Object();";
            
            $sType = ($this->oNode->sName == 'vlgroup') ? 'group' : strtolower($this->oNode->aAttrs['type']);
            $sScript .= " $sLet.type=\"$sType\";";

            if (!$bInGroup)
            {
                $sName = VDEscape($this->oNode->aAttrs['name']);
                $sScript .= " $sLet.name=\"$sName\";";
                
                $sErrMsg = isset($this->oNode->aAttrs['errmsg']) ? VDEscape($this->oNode->aAttrs['errmsg']) : '';
                $sScript .= " $sLet.errmsg=\"$sErrMsg\";";
            }
            if ($sType != 'group' && ($sType != 'custom' || isset($this->oNode->aAttrs['control'])))
            {
                $sControl = VDEscape($this->oNode->aAttrs['control']);
                $sScript .= " $sLet.control=\"$sControl\";";
            }
            
            if ($sType == 'group')
            {
                $sScript .= "v.items=new Array();\n";
                foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
                {
                    $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                    if (is_object($oSubNode) && $oSubNode->sName == 'vlvalidator')
                    {
                        $oSubValidator =& new CVDValidator($oSubNode);
                        $sScript .= $oSubValidator->GetClientScript(true);
                    }
                }
                $sScript .= "f.validators[v.name]=v;\n";
            }
            else
            {
                $sCase = (isset($this->oNode->aAttrs['casesensitive']) &&
                    strtolower($this->oNode->aAttrs['casesensitive']) != 'false') ? 'true' : 'false';
                $sValidType = isset($this->oNode->aAttrs['validtype']) ? strtolower($this->oNode->aAttrs['validtype']) : '';
                $sDateOrder = isset($this->oNode->aAttrs['dateorder']) ? strtolower($this->oNode->aAttrs['dateorder']) : 'mdy';
                
                switch ($sType)
                {
                    case 'required':
                        $sMinLength = isset($this->oNode->aAttrs['minlength']) ? $this->oNode->aAttrs['minlength'] : '1';
                        $sMaxLength = isset($this->oNode->aAttrs['maxlength']) ? $this->oNode->aAttrs['maxlength'] : '-1';
                        $sScript .= " $sLet.minlength=$sMinLength;";
                        $sScript .= " $sLet.maxlength=$sMaxLength;";
                        break;
                    
                    case 'checktype':
                        $sScript .= " $sLet.validtype=\"$sValidType\";";
                        if ($sValidType == 'date')
                        {
                            $sScript .= " $sLet.dateorder=\"$sDateOrder\";";
                        }
                        break;
                    
                    case 'range':
                        $sMin = isset($this->oNode->aAttrs['minvalue']) ? VDEscape($this->oNode->aAttrs['minvalue']) : '';
                        $sMax = isset($this->oNode->aAttrs['maxvalue']) ? VDEscape($this->oNode->aAttrs['maxvalue']) : '';
                        $sScript .= " $sLet.validtype=\"$sValidType\";";
                        if ($sValidType == 'date')
                        {
                            $sScript .= " $sLet.dateorder=\"$sDateOrder\";";
                        }
                        $sScript .= " $sLet.casesensitive=$sCase;";
                        $sScript .= " $sLet.minvalue=\"$sMin\";";
                        $sScript .= " $sLet.maxvalue=\"$sMax\";";
                        break;
                    
                    case 'compare':
                        if (isset($this->oNode->aAttrs['comparevalue']))
                        {
                            $sAttr = 'comparevalue';
                            $sVal = VDEscape($this->oNode->aAttrs['comparevalue']);
                        }
                        else
                        {
                            $sAttr = 'comparecontrol';
                            $sVal = VDEscape($this->oNode->aAttrs['comparecontrol']);
                        }
                        $sOperator = isset($this->oNode->aAttrs['operator']) ? strtolower($this->oNode->aAttrs['operator']) : 'e';
                        $sScript .= " $sLet.validtype=\"$sValidType\";";
                        if ($sValidType == 'date')
                        {
                            $sScript .= " $sLet.dateorder=\"$sDateOrder\";";
                        }
                        $sScript .= " $sLet.casesensitive=$sCase;";
                        $sScript .= " $sLet.$sAttr=\"$sVal\";";
                        $sScript .= " $sLet.operator=\"$sOperator\";";
                        break;
                    
                    case 'regexp':
                        $sRegExp = isset($this->oNode->aAttrs['clientregexp']) ?
                            VDEscape($this->oNode->aAttrs['clientregexp']) :
                            VDEscape($this->oNode->aAttrs['regexp']);
                        $sScript .= " $sLet.clientregexp=\"$sRegExp\";";
                        break;
                    
                    case 'custom':
                        $sFunc = isset($this->oNode->aAttrs['clientfunction']) ? $this->oNode->aAttrs['clientfunction'] : '';
                        $sScript .= " $sLet.clientfunction=\"$sFunc\";";
                        break;
                }
                
                if ($bInGroup)
                {
                    $sScript .= " v.items[v.items.length]=i;\n";
                }
                else
                {
                    $sScript .= " f.validators[v.name]=v;\n";
                }
            }
        }
        
        return $sScript;
    }
    
    function Convert($sValue)
    {
        $mResult = false;
        
        if (isset($this->oNode->aAttrs['validtype']))
        {
            $sType = strtolower($this->oNode->aAttrs['validtype']);
            switch ($sType)
            {
                case 'string':
                    $mResult = strval($sValue);
                    break;
                
                case 'integer':
                    if (preg_match('/^\s*[-+]?\d+\s*$/', $sValue))
                    {
                        $mResult = intval($sValue);
                    }
                    break;
                
                case 'float':
                    if (preg_match('/^\s*[-+]?(\d+)?(\.\d+)?\s*$/', $sValue, $aMatches))
                    {
                        if (strlen($aMatches[1]) > 0 || strlen($aMatches[2]) > 1)
                        {
                            $mResult = doubleval($sValue);
                        }
                    }
                    break;
                
                case 'currency':
                    if (preg_match('/^\s*[-+]?(\d+\,)*\d+(\.\d{1,2})?\s*$/', $sValue))
                    {
                        $mResult = doubleval(str_replace(',', '', $sValue));
                    }
                    break;
                
                case 'date':
                {
                    $sYear = -1;
                    $sDateOrder = isset($this->oNode->aAttrs['dateorder']) ?
                                  strtolower($this->oNode->aAttrs['dateorder']) : 'mdy';
                    if ($sDateOrder == 'ymd')
                    {
                        if (preg_match('|^\s*(\d{2}(\d{2})?)([-\./])(\d{1,2})\3(\d{1,2})\s*$|', $sValue, $aMatches))
                        {
                            $nDay = intval($aMatches[5]);
                            $nMonth = intval($aMatches[4]);
                            $sYear = $aMatches[1];
                        }
                    }
                    elseif (preg_match('|^\s*(\d{1,2})([-\./])(\d{1,2})\2(\d{2}(\d{2})?)\s*$|', $sValue, $aMatches))
                    {
                        $sYear = $aMatches[4];
                        
                        if ($sDateOrder == 'dmy')
                        {
                            $nDay = intval($aMatches[1]);
                            $nMonth = intval($aMatches[3]);
                        }
                        else
                        {
                            $nDay = intval($aMatches[3]);
                            $nMonth = intval($aMatches[1]);
                        }
                    }
                    
                    if ($sYear != -1)
                    {
                        $nYear = intval($sYear);
                        if (strlen($sYear) < 3)
                        {
                            $nYear += 2000 - ($nYear < 30 ? 0 : 100);
                            $sYear = strval($nYear);
                        }
                        
                        if (checkdate($nMonth, $nDay, $nYear))
                        {
                            if ($nDay < 10)
                            {
                                $nDay = '0' . $nDay;
                            }
                            if ($nMonth < 10)
                            {
                                $nMonth = '0' . $nMonth;
                            }
                        
                            $mResult = $sYear . $nMonth . $nDay;
                        }
                    }
                }
                break;
            }
        }
        
        return $mResult;
    }
    
    function Compare($sOperand1, $sOperand2, $sOperator)
    {
        $bResult = true;
        
        if (($mOp1 = $this->Convert($sOperand1)) === false)
        {
            $bResult = false;
        }
        elseif (($mOp2 = $this->Convert($sOperand2)) !== false)
        {
            $sValidType = strtolower($this->oNode->aAttrs['validtype']);
            $bCase = (isset($this->oNode->aAttrs['casesensitive']) &&
                strtolower($this->oNode->aAttrs['casesensitive']) != 'false') ? true : false;
            
            if ($sValidType == "string" && !$bCase)
            {
                $mOp1 = strtolower($mOp1);
                $mOp2 = strtolower($mOp2);
            }
                
            switch ($sOperator)
            {
                case "ne":
                    $bResult = ($mOp1 != $mOp2);
                    break;
                    
                case "g":
                    $bResult = ($mOp1 > $mOp2);
                    break;
                    
                case "ge":
                    $bResult = ($mOp1 >= $mOp2);
                    break;
                    
                case "l":
                    $bResult = ($mOp1 < $mOp2);
                    break;
                    
                case "le":
                    $bResult = ($mOp1 <= $mOp2);
                    break;
                    
                case "e":
                default:
                    $bResult = ($mOp1 == $mOp2);
                    break;
            }
        }
        
        return $bResult;
    }
    
    function Validate()
    {
        $bValid = true;
        
        $sType = ($this->oNode->sName == 'vlgroup') ? 'group' : strtolower($this->oNode->aAttrs['type']);
        $sCtrlVal = isset($this->oNode->aAttrs['control']) ? VDGetValue($this->oNode->aAttrs['control']) : null;
        $bCase = (isset($this->oNode->aAttrs['casesensitive']) &&
                  strtolower($this->oNode->aAttrs['casesensitive']) != 'false') ? true : false;
        
        switch ($sType)
        {
            case "required":
                $nMinLength = isset($this->oNode->aAttrs['minlength']) ? intval($this->oNode->aAttrs['minlength']) : 1;
                $nMaxLength = isset($this->oNode->aAttrs['maxlength']) ? intval($this->oNode->aAttrs['maxlength']) : -1;
                
                $bValid = $nMinLength <= strlen($sCtrlVal);
                if ($bValid && $nMaxLength != -1)
                {
                    $bValid = strlen($sCtrlVal) <= $nMaxLength;
                }
                break;
            
            case "checktype":
                if ($sCtrlVal != '')
                {
                    $bValid = $this->Convert($sCtrlVal) !== false;
                }
                break;
            
            case "range":
                if ($sCtrlVal != '')
                {
                    $sMinVal = isset($this->oNode->aAttrs['minvalue']) ? $this->oNode->aAttrs['minvalue'] : '';
                    $sMaxVal = isset($this->oNode->aAttrs['maxvalue']) ? $this->oNode->aAttrs['maxvalue'] : '';
                    $bValid = $this->Compare($sCtrlVal, $sMinVal, 'ge') && $this->Compare($sCtrlVal, $sMaxVal, 'le');
                }
                break;
            
            case "compare":
                if ($sCtrlVal != '')
                {
                    $sCompareVal = '';
                    if (isset($this->oNode->aAttrs['comparevalue']))
                    {
                        $sCompareVal = $this->oNode->aAttrs['comparevalue'];
                    }
                    else
                    {
                        $sCompareVal = VDGetValue($this->oNode->aAttrs['comparecontrol']);
                    }
                    $sOperator = isset($this->oNode->aAttrs['operator']) ? strtolower($this->oNode->aAttrs['operator']) : 'e';
                    
                    $bValid = $this->Compare($sCtrlVal, $sCompareVal, $sOperator);
                }
                break;
            
            case "regexp":
                if ($sCtrlVal != '')
                {
                    $sRegExp = isset($this->oNode->aAttrs['regexp']) ? $this->oNode->aAttrs['regexp'] : '';
                    $bValid = preg_match($sRegExp, $sCtrlVal) == true;
                }
                break;
            
            case "email":
                if ($sCtrlVal != '')
                {
                    $bValid = preg_match('/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.\w{2,8}$/', $sCtrlVal) == true;
                }
                break;
            
            case "custom":
                $sFunc = isset($this->oNode->aAttrs['function']) ? $this->oNode->aAttrs['function'] : '';
                if (function_exists($sFunc))
                {
                    $oStatus =& new CVDValidatorStatus();
                    $oStatus->bValid = true;
                    $oStatus->sErrMsg = isset($this->oNode->aAttrs['errmsg']) ? $this->oNode->aAttrs['errmsg'] : '';
                    
                    $sFunc($sCtrlVal, $oStatus);
                    $this->oNode->aAttrs['errmsg'] = $oStatus->sErrMsg;
                    $bValid = $oStatus->bValid;
                }
                break;
            
            case "group": 
            {
                $bValid = false;
                foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
                {
                    $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                    if (is_object($oSubNode) && $oSubNode->sName == 'vlvalidator')
                    {
                        $oSubValidator =& new CVDValidator($oSubNode);
                        $bValid = $bValid || $oSubValidator->Validate();
                    }
                }
            }
            break;
        }
        
        return $bValid;
    }
}

//--------------------------------------------------------------------------
//                  class CVDControl
//--------------------------------------------------------------------------

class CVDControl
{
    var $sName;
    var $aInputs;
    
    function CVDControl($sName)
    {
        $this->sName = $sName;
        $this->aInputs = array();
    }
    
    function AddInput(&$oNode)
    {
        $this->aInputs[] =& new CVDInput($oNode);
    }
    
    function Prepare()
    {
        foreach ($this->aInputs as $nIdx => $mTmp)
        {
            $this->aInputs[$nIdx]->Prepare();
        }
    }
    
    function Populate()
    {
        if (substr($this->sName, -2) == '[]')
        {
            $sName = substr($this->sName, 0, strlen($this->sName) - 2);
            $nVal = 0;
            for ($nIdx = 0; $nIdx < count($this->aInputs); $nIdx++)
            {
                $sValue = VDGetValue($sName.'['.$nVal.']', true);
                if ($this->aInputs[$nIdx]->sType == 'select' && isset($this->aInputs[$nIdx]->oNode->aAttrs['multiple']))
                {
                    $this->aInputs[$nIdx]->ClearMultiple();
                    while ($sValue !== null && $this->aInputs[$nIdx]->SetValue($sValue))
                    {
                        $nVal++;
                        $sValue = VDGetValue($sName.'['.$nVal.']', true);
                    }
                }
                else
                {
                    if ($this->aInputs[$nIdx]->SetValue($sValue))
                    {
                        $nVal++;
                    }
                }
            }
        }
        else
        {
            $sValue = VDGetValue($this->sName, true);
            foreach ($this->aInputs as $nIdx => $mTmp)
            {
                $this->aInputs[$nIdx]->ClearMultiple();
                $this->aInputs[$nIdx]->SetValue($sValue);
            }
        }
    }
}

//--------------------------------------------------------------------------
//                  class CVDInput
//--------------------------------------------------------------------------

class CVDInput
{
    var $oNode;
    var $sType;
    var $sPopulate;
    
    function CVDInput(&$oNode)
    {
        $this->oNode =& $oNode;
        switch ($this->oNode->sName)
        {
            case 'input':
                $this->sType = isset($this->oNode->aAttrs['type']) ? strtolower($this->oNode->aAttrs['type']) : 'text';
                break;
            
            case 'select':
                $this->sType = 'select';
                break;
                
            case 'textarea':
                $this->sType = 'textarea';
                break;
            
            default:
                $this->sType = 'text';
                break;
        }
        
        if (isset($this->oNode->aAttrs['populate']))
        {
            $this->bPopulate = strtolower($this->oNode->aAttrs['populate']) != 'false';
        }
        else
        {
            $this->bPopulate = $this->sType == 'password' ? false : true;
        }
    }
    
    function Prepare()
    {
        if ($this->sType == 'select')
        {
            foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
            {
                $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                if (is_object($oSubNode) && $oSubNode->sName == 'option')
                {
                    if (!$oSubNode->aSubNodes)
                    {
                        $oSubNode->aSubNodes[] = '';
                    }
                    if (!isset($oSubNode->aAttrs['value']))
                    {
                        $sValue = $oSubNode->SerializeWithoutRoot();
                        $oSubNode->aAttrs['value'] = trim(strip_tags($sValue));
                    }
                }
            }
        }
        elseif ($this->sType == 'textarea')
        {
            if (!$this->oNode->aSubNodes)
            {
                $this->oNode->aSubNodes[] = '';
            }
        }
        
        unset($this->oNode->aAttrs['populate']);
    }
    
    function ClearMultiple()
    {
        if ($this->bPopulate && $this->sType == 'select' && isset($this->oNode->aAttrs['multiple']))
        {
            foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
            {
                $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                if (is_object($oSubNode) && $oSubNode->sName == 'option')
                {
                    unset($oSubNode->aAttrs['selected']);
                }
            }
        }
    }
    
    function SetValue($sValue)
    {
        $bResult = true;
        
        switch ($this->sType)
        {
            case 'text':
            case 'password':
                if ($this->bPopulate)
                {
                    if ($sValue != '')
                    {
                        $this->oNode->aAttrs['value'] = htmlspecialchars($sValue);
                    }
                    else
                    {
                        unset($this->oNode->aAttrs['value']);
                    }
                }
                break;
            
            case 'hidden':
                if ($this->bPopulate)
                {
                    $this->oNode->aAttrs['value'] = htmlspecialchars($sValue);
                }
                break;
                
            case 'textarea':
                if ($this->bPopulate)
                {
                    $this->oNode->aSubNodes = array();
                    $this->oNode->aSubNodes[] = htmlspecialchars($sValue);
                }
                break;
            
            case 'file':
                $bResult = false;
                break;
            
            case 'submit':
                if (isset($this->oNode->aAttrs['value']))
                {
                    $bResult = $this->oNode->aAttrs['value'] == $sValue;
                }
                else
                {
                    $bResult = in_array(strtolower($sValue), array('submit', 'submit query'));
                }
                break;
            
            case 'checkbox':
            case 'radio':
                $sInputValue = isset($this->oNode->aAttrs['value']) ? $this->oNode->aAttrs['value'] : 'on';
                $bResult = $sInputValue == $sValue;
                if ($this->bPopulate)
                {
                    if ($bResult)
                    {
                        $this->oNode->aAttrs['checked'] = 'true';
                    }
                    else
                    {
                        unset($this->oNode->aAttrs['checked']);
                    }
                }
                break;
            
            case 'select':
                $bResult = false;
                foreach ($this->oNode->aSubNodes as $nIdx => $mTmp)
                {
                    $oSubNode =& $this->oNode->aSubNodes[$nIdx];
                    if (is_object($oSubNode) && $oSubNode->sName == 'option')
                    {
                        if ($oSubNode->aAttrs['value'] == $sValue)
                        {
                            $bResult = true;
                            if ($this->bPopulate)
                            {
                                $oSubNode->aAttrs['selected'] = true;
                            }
                        }
                        elseif ($this->bPopulate && !isset($this->oNode->aAttrs['multiple']))
                        {
                            unset($oSubNode->aAttrs['selected']);
                        }
                    }
                }
                break;
        }
        
        return $bResult;
    }
}

//--------------------------------------------------------------------------
//                  class CVDValRuntime
//--------------------------------------------------------------------------

class CVDValRuntime
{
    var $sProtocol;
    var $sPage;
    var $sArgs;
    var $sAnchor;
    var $sForm;
    var $aNodes;
    
    function CVDValRuntime()
    {
        $this->sProtocol = '';
        $this->sPage = '';
        $this->sArgs = '';
        $this->sAnchor = '';
        $this->sForm = '';
        $this->aNodes = array();
    }
    
    function Fill(&$oForm)
    {
        $this->sProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        $this->sPage = $_SERVER['PHP_SELF'];
        $this->sArgs = VDGetCurrentArgs();
        $this->sAnchor = isset($oForm->oNode->aAttrs['anchor']) ? $oForm->oNode->aAttrs['anchor'] : '';
        $this->sForm = $oForm->oNode->aAttrs['name'];
        
        foreach ($oForm->aValidators as $oVal)
        {
            $oNode =& new XmlNode($oVal->oNode->sName, $oVal->oNode->aAttrs);
            unset($oNode->aAttrs['clientvalidate']);
            unset($oNode->aAttrs['clientregexp']);
            unset($oNode->aAttrs['clientfunction']);
            
            foreach ($oVal->oNode->aSubNodes as $oSubValNode)
            {
                if (is_object($oSubValNode) && $oSubValNode->sName == 'vlvalidator')
                {
                    $oSubNode =& $oNode->AddSubNode($oSubValNode->sName, $oSubValNode->aAttrs);
                    unset($oSubNode->aAttrs['clientvalidate']);
                    unset($oSubNode->aAttrs['clientregexp']);
                    unset($oSubNode->aAttrs['clientfunction']);
                }
            }
            
            $this->aNodes[] =& $oNode;
        }
    }
}

//--------------------------------------------------------------------------
//                  THE END
//--------------------------------------------------------------------------
?>
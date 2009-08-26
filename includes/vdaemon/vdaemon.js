///////////////////////////////////////////////////////////////////////////////
// VDaemon PHP Library version 2.3.0
// Copyright (C) 2002-2004 Alexander Orlov and Andrei Stepanuga
//
// VDaemon client-side validation file
//
///////////////////////////////////////////////////////////////////////////////

function VDSymError()
{
  return true;
}
window.onerror = VDSymError;

var vdAllForms = new Array();
var vdForm = null;
var vdDelimiter = "~";

function VDValidateForm(formName)
{
    if (typeof(vdAllForms[formName]) == "undefined")
        return true;

    vdForm = vdAllForms[formName];
    var isPageValid = true;
    for (var key in vdForm.validators)
    {
        VDValidateValidator(vdForm.validators[key]);
        isPageValid = isPageValid && vdForm.validators[key].isvalid;
    }
    vdForm.isvalid = isPageValid;
    VDUpdateLabels();
    VDUpdateSummaries();

    vdForm = null;
    return isPageValid;
}

function VDValidateValidator(validator)
{
    validator.isvalid = true;
    switch (validator.type)
    {
        case "required":
            validator.isvalid = VDEvaluateRequired(validator);
            break;
        case "checktype":
            validator.isvalid = VDEvaluateChecktype(validator);
            break;
        case "range":
            validator.isvalid = VDEvaluateRange(validator);
            break;
        case "compare":
            validator.isvalid = VDEvaluateCompare(validator);
            break;
        case "regexp":
            validator.isvalid = VDEvaluateRegExp(validator);
            break;
        case "email":
            validator.isvalid = VDEvaluateEmail(validator);
            break;
        case "custom":
            validator.isvalid = VDEvaluateCustom(validator);
            break;
        case "group":
            validator.isvalid = false;
            for (var i in validator.items)
            {
                VDValidateValidator(validator.items[i]);
                validator.isvalid = validator.isvalid || validator.items[i].isvalid;
            }
            break;
    }
}

function VDUpdateLabels()
{
    if (typeof(vdForm.labels) == "undefined")
        return;
    var i, j;
    for (i in vdForm.labels)
    {
        var oLabel = vdForm.labels[i];
        var label = document.getElementById(oLabel.id);
        if (label != null)
        {
            var isValid = true;
            for (j in oLabel.validators)
            {
                var valName = oLabel.validators[j];
                if (typeof(vdForm.validators[valName]) != "undefined")
                {
                    isValid = isValid && vdForm.validators[valName].isvalid;
                }
            }

            label.innerHTML = "";
            if (isValid)
            {
                label.innerHTML = oLabel.oktext;
                label.className = oLabel.okclass;
            }
            else
            {
                label.innerHTML = oLabel.errtext;
                label.className = oLabel.errclass;
            }
        }
    }
}

function VDUpdateSummaries()
{
    if (typeof(vdForm.summaries) == "undefined")
        return;

    for (var i in vdForm.summaries)
    {
        var headerSep, first, pre, post, last, s;
        var oSummary = vdForm.summaries[i];
        var summary = document.getElementById(oSummary.id);
        if (summary != null)
        {
            if (vdForm.isvalid)
            {
                //summary.innerHTML = oSummary.showsummary ? "&nbsp;" : "";
                summary.innerHTML = "";
                summary.style.display = "none";
            }
            else
            {
                if (oSummary.showsummary)
                {
                    switch (oSummary.displaymode)
                    {
                        case "list":
                        default:
                            headerSep = "<br>";
                            first = "";
                            pre = "";
                            post = "<br>";
                            last = "";
                            break;
                        case "bulletlist":
                            headerSep = "";
                            first = "<ul>";
                            pre = "<li>";
                            post = "</li>";
                            last = "</ul>";
                            break;
                        case "paragraph":
                            headerSep = " ";
                            first = "";
                            pre = "";
                            post = " ";
                            last = "";
                            break;
                    }

                    s = "";
                    for (var j in vdForm.validators)
                    {
                        var val = vdForm.validators[j];
                        if (!val.isvalid && val.errmsg)
                        {
                            s += pre + val.errmsg + post;
                        }
                    }
                    if (s != "")
                    {
                        s = headerSep + first + s + last;
                    }
                    if (oSummary.headertext != "")
                    {
                        s = oSummary.headertext + s;
                    }
                    
                    summary.innerHTML = s;
                    summary.style.display = (s == "") ? "none" : "";
                    //window.scrollTo(0,0);
                }

                if (oSummary.messagebox)
                {
                    s = "";
                    if (oSummary.headertext != "")
                    {
                        s += oSummary.headertext + "\n";
                    }
                    for (var j in vdForm.validators)
                    {
                        var val = vdForm.validators[j];
                        if (!val.isvalid && val.errmsg != null)
                        {
                            switch (oSummary.displaymode)
                            {
                                case "list":
                                default:
                                    s += val.errmsg + "\n";
                                    break;
                                case "bulletlist":
                                    s += "  - " + val.errmsg + "\n";
                                    break;
                                case "paragraph":
                                    s += val.errmsg + " ";
                                    break;
                            }
                        }
                    }
                    alert(s);
                }
            }
        }
    }
}

function VDGetControlValue(formName, controlName)
{
    var control;
    control = document.forms[formName].elements[controlName];
    if (typeof(control) == "undefined")
        return "";
    
    var isArray = false;
    if (controlName.length > 2)
        isArray = controlName.substring(controlName.length - 2, controlName.length) == "[]";
    
    return VDGetControlValueRecursive(control, isArray);
 }

function VDGetControlValueRecursive(control, isArray)
{
    var result = "";
    if (typeof(control.type) == "undefined")
    {
        if (typeof(control.tagName) == "undefined" && typeof(control.length) == "number")
        {
            for (var j = 0; j < control.length; j++)
            {
                var value = VDGetControlValueRecursive(control[j], isArray);
                if (value != "")
                {
                    if (isArray && result != "")
                        result += vdDelimiter + value;
                    else
                        result = value;
                }
            }
        }
        else if (typeof(control.tagName) == "string" && control.tagName.toLowerCase() == "option")
        {
            if (control.selected)
            {
                if (typeof(control.value) == "string")
                {
                    if (control.getAttribute("VALUE") == "")
                        result = VDTrim(control.text);
                    else
                        result = VDTrim(control.value);
                }
                else
                {
                    result = VDTrim(control.text);
                }
            }
        }
    }
    else
    {
        if (control.type == "select-multiple")
        {
            var children = control.getElementsByTagName("OPTION");
            result = VDGetControlValueRecursive(children, isArray);
        }
        else if (typeof(control.value) == "string")
        {
            if (control.type == "checkbox" || control.type == "radio")
            {
                if (control.checked)
                    result = VDTrim(control.value);
            }
            else
                result = VDTrim(control.value);
        }
    }
    return result;
}

function VDTrim(str)
{
    var match = str.match(/^\s*(\S+(\s+\S+)*)\s*$/);
    return (match == null) ? "" : match[1];
}

function VDConvert(op, val)
{
    function GetFullYear(year)
    {
        return (year + 2000) - ((year < 30) ? 0 : 100);
    }
    
    var dataType = val.validtype;
    var num, cleanInput, m, exp;
    if (dataType == "integer")
    {
        exp = /^\s*[-\+]?\d+\s*$/;
        if (op.match(exp) == null) 
            return null;
        num = parseInt(op, 10);
        return (isNaN(num) ? null : num);
    }
    else if(dataType == "float")
    {
        exp = new RegExp("^\\s*([-\\+])?(\\d+)?(\\.\\d+)?\\s*$");
        m = op.match(exp);
        if (m == null)
            return null;
        cleanInput = m[1] + (m[2].length > 0 ? m[2] : "0") + m[3];
        num = parseFloat(cleanInput);
        return (isNaN(num) ? null : num);            
    } 
    else if (dataType == "currency")
    {
        exp = new RegExp("^\\s*([-\\+])?(((\\d+)\\,)*)(\\d+)(\\.\\d{1,2})?\\s*$");
        m = op.match(exp);
        if (m == null)
            return null;
        var intermed = m[2] + m[5];
        cleanInput = m[1] + intermed.replace(new RegExp("(\\,)", "g"), "") + m[6];
        num = parseFloat(cleanInput);
        return (isNaN(num) ? null : num);            
    }
    else if (dataType == "date")
    {
        var day, month, year;
        if (val.dateorder == "ymd")
        {
            exp = new RegExp("^\\s*((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})\\s*$");
            m = op.match(exp);
            if (m == null)
                return null;
            day = m[6];
            month = m[5];
            year = (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
        }
        else
        {
            exp = new RegExp("^\\s*(\\d{1,2})([-./])(\\d{1,2})\\2((\\d{4})|(\\d{2}))\\s*$");
            m = op.match(exp);
            if (m == null)
                return null;
            if (val.dateorder == "dmy")
            {
                day = m[1];
                month = m[3];
            }
            else
            {
                day = m[3];
                month = m[1];
            }
            year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
        }
        month -= 1;
        var date = new Date(year, month, day);
        return (typeof(date) == "object" && year == date.getFullYear() && month == date.getMonth() && day == date.getDate()) ? date.valueOf() : null;
    }
    else
    {
        return op.toString();
    }
}

function VDCompare(operand1, operand2, operator, val)
{
    var op1, op2;
    if ((op1 = VDConvert(operand1, val)) == null)
        return false;    
    if ((op2 = VDConvert(operand2, val)) == null)
        return true;

    if (val.validtype == "string" && !val.casesensitive)
    {
        op1 = op1.toLowerCase();
        op2 = op2.toLowerCase();
    }    
    switch (operator)
    {
        case "ne":
            return (op1 != op2);
        case "g":
            return (op1 > op2);
        case "ge":
            return (op1 >= op2);
        case "l":
            return (op1 < op2);
        case "le":
            return (op1 <= op2);
        case "e":
        default:
            return (op1 == op2);            
    }
}

function VDEvaluateRequired(validator)
{
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length < validator.minlength)
        return false;
    if (validator.maxlength != -1)
        return (value.length <= validator.maxlength);
    
    return true;
}

function VDEvaluateChecktype(validator)
{
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length == 0)
        return true;
    
    return (VDConvert(value, validator) != null);
}

function VDEvaluateRange(validator)
{
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length == 0) 
        return true;
    
    return (VDCompare(value, validator.minvalue, "ge", validator) &&
            VDCompare(value, validator.maxvalue, "le", validator));
}

function VDEvaluateCompare(validator)
{
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length == 0) 
        return true;
    
    var compareTo = "";
    if (typeof(validator.comparevalue) != "undefined")
    {
        compareTo = validator.comparevalue;
    }
    else if (typeof(validator.comparecontrol) != "undefined")
    {
        compareTo = VDGetControlValue(vdForm.name, validator.comparecontrol);
    }
    else
        return false;

    return VDCompare(value, compareTo, validator.operator, validator);
}

function VDEvaluateRegExp(validator)
{
    var result = true;
    
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length > 0)
    {
        var rx;
        try
        {
            eval("rx = " + validator.clientregexp + ";");
            var matches = rx.exec(value);
            result = (matches != null);
        }
        catch(e)
        {
            result = true;
        }
    }
    
    return result;
}

function VDEvaluateEmail(validator)
{
    var value = VDGetControlValue(vdForm.name, validator.control);
    if (value.length == 0) 
        return true;        
    var rx = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.\w{2,8}$/;
    var matches = rx.exec(value);
    return (matches != null);
}

function VDEvaluateCustom(validator)
{
    var value = null;
    if (typeof(validator.control) == "string")
    {
        value = VDGetControlValue(vdForm.name, validator.control);
    }
    
    var args = new Object();
    args.isvalid = true;
    args.errmsg = validator.errmsg;
    args.value = value;
    if (typeof(validator.clientfunction) == "string")
    {
        var rx = /^[a-zA-Z_]\w*$/;
        var m = rx.exec(validator.clientfunction);
        var isfunc;
        if (m != null)
        {
            eval("isfunc = typeof(" + validator.clientfunction + ") == 'function';");
            if (isfunc)
            {
                eval(validator.clientfunction + "(args);");
                args.isvalid = (args.isvalid === true);
                if (typeof(args.errmsg) == "string" && validator.type != "group")
                {
                    validator.errmsg = args.errmsg;
                }
            }
        }
    }        
    return args.isvalid;
}

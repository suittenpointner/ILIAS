<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/


/**
* Class ilObjMailGUI
* for admin panel
*
* @author Stefan Meyer <smeyer@databay.de> 
* $Id$
* 
* @ilCtrl_Calls ilObjMailGUI: ilPermissionGUI
* 
* @extends ilObjectGUI
* @package ilias-core
*/

require_once "class.ilObjectGUI.php";

class ilObjMailGUI extends ilObjectGUI
{
	/**
	* Constructor
	* @access public
	*/
	function ilObjMailGUI($a_data,$a_id,$a_call_by_reference)
	{
		$this->type = "mail";
		$this->ilObjectGUI($a_data,$a_id,$a_call_by_reference);
	}

	function viewObject()
	{
#		parent::editObject();
		
		$this->lng->loadLanguageModule("mail");

		$this->tpl->addBlockFile("SYSTEMSETTINGS", "systemsettings", "tpl.mail_basicdata.html");
		$this->tpl->setCurrentBlock("systemsettings");

		$settings = $this->ilias->getAllSettings();

		if (isset($_POST["save_settings"]))  // formular sent
		{
			//init checking var
			$form_valid = true;
			
			// put any checks here!!!

			if (!$form_valid)	//required fields not satisfied. Set formular to already fill in values
			{
				// mail server
				$settings["mail_server"] = $_POST["mail_server"];
				$settings["mail_port"] = $_POST["mail_port"];

				// internal mail
#				$settings["mail_intern_enable"] = $_POST["mail_intern_enable"];
				$settings["mail_maxsize_mail"] = $_POST["mail_maxsize_mail"];
				$settings["mail_maxsize_attach"] = $_POST["mail_maxsize_attach"];
				$settings["mail_maxsize_box"] = $_POST["mail_maxsize_box"];
				$settings["mail_maxtime_mail"] = $_POST["mail_maxtime_mail"];
				$settings["mail_maxtime_attach"] = $_POST["mail_maxtime_attach"];
			}
			else // all required fields ok
			{

		////////////////////////////////////////////////////////////
		// write new settings

				// mail server
				$this->ilias->setSetting("mail_server",$_POST["mail_server"]);
				$this->ilias->setSetting("mail_port",$_POST["mail_port"]);

				// internal mail
#				$this->ilias->setSetting("mail_intern_enable",$_POST["mail_intern_enable"]);
				$this->ilias->setSetting("mail_maxsize_mail",$_POST["mail_maxsize_mail"]);
				$this->ilias->setSetting("mail_maxsize_attach",$_POST["mail_maxsize_attach"]);
				$this->ilias->setSetting("mail_maxsize_box",$_POST["mail_maxsize_box"]);
				$this->ilias->setSetting("mail_maxtime_mail",$_POST["mail_maxtime_mail"]);
				$this->ilias->setSetting("mail_maxtime_attach",$_POST["mail_maxtime_attach"]);

				$settings = $this->ilias->getAllSettings();

				// feedback
				sendInfo($this->lng->txt("saved_successfully"));
			}
		}

		////////////////////////////////////////////////////////////
		// setting language vars

		// common
		$this->tpl->setVariable("TXT_DAYS",$this->lng->txt("days"));
		$this->tpl->setVariable("TXT_KB",$this->lng->txt("kb"));

		// mail server
		$this->tpl->setVariable("TXT_MAIL_SMTP", $this->lng->txt("mail")." (".$this->lng->txt("smtp").")");
		$this->tpl->setVariable("TXT_MAIL_SERVER", $this->lng->txt("server"));
		$this->tpl->setVariable("TXT_MAIL_PORT", $this->lng->txt("port"));

		// internal mail
		$this->tpl->setVariable("TXT_MAIL_INTERN", $this->lng->txt("mail")." (".$this->lng->txt("internal_system").")");
#		$this->tpl->setVariable("TXT_MAIL_INTERN_ENABLE", $this->lng->txt("mail_intern_enable"));
		$this->tpl->setVariable("TXT_MAIL_MAXSIZE_MAIL", $this->lng->txt("mail_maxsize_mail"));
		$this->tpl->setVariable("TXT_MAIL_MAXSIZE_ATTACH", $this->lng->txt("mail_maxsize_attach"));
		$this->tpl->setVariable("TXT_MAIL_MAXSIZE_BOX", $this->lng->txt("mail_maxsize_box"));
		$this->tpl->setVariable("TXT_MAIL_MAXTIME_MAIL", $this->lng->txt("mail_maxtime_mail"));
		$this->tpl->setVariable("TXT_MAIL_MAXTIME_ATTACH", $this->lng->txt("mail_maxtime_attach"));
		$this->tpl->setVariable("TXT_SAVE", $this->lng->txt("save"));

		///////////////////////////////////////////////////////////
		// display formula data

		// mail server
		$this->tpl->setVariable("MAIL_SERVER",$settings["mail_server"]);
		$this->tpl->setVariable("MAIL_PORT",$settings["mail_port"]);

		// internal mail
#		if ($settings["mail_intern_enable"] == "y")
#		{
#			$this->tpl->setVariable("MAIL_INTERN_ENABLE","checked=\"checked\"");
#		}

		$this->tpl->setVariable("MAIL_MAXSIZE_MAIL", $settings["mail_maxsize_mail"]);
		$this->tpl->setVariable("MAIL_MAXSIZE_ATTACH", $settings["mail_maxsize_attach"]);
		$this->tpl->setVariable("MAIL_MAXSIZE_BOX", $settings["mail_maxsize_box"]);
		$this->tpl->setVariable("MAIL_MAXTIME_MAIL", $settings["mail_maxtime_mail"]);
		$this->tpl->setVariable("MAIL_MAXTIME_ATTACH", $settings["mail_maxtime_attach"]);

		$this->tpl->parseCurrentBlock();
	}

	function importObject()
	{
		global $rbacsystem,$lng;

		if (!$rbacsystem->checkAccess('write',$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilias->error_obj->WARNING);
		}
		$this->getTemplateFile("import");

		// GET ALREADY CREATED UPLOADED XML FILE
		$this->__initFileObject();
		if($this->file_obj->findXMLFile())
		{
			$this->tpl->setVariable("TXT_IMPORTED_FILE",$lng->txt("checked_files"));
			$this->tpl->setVariable("XML_FILE",basename($this->file_obj->getXMLFile()));

			$this->tpl->setVariable("BTN_IMPORT",$this->lng->txt("import"));
		}

		$this->tpl->setVariable("FORMACTION", "adm_object.php?ref_id=".$_GET["ref_id"]."&cmd=gateway");
		$this->tpl->setVariable("TXT_IMPORT_MAIL",$this->lng->txt("table_mail_import"));
		$this->tpl->setVariable("TXT_IMPORT_FILE",$this->lng->txt("mail_import_file"));
		$this->tpl->setVariable("BTN_CANCEL",$this->lng->txt("cancel"));
		$this->tpl->setVariable("BTN_UPLOAD",$this->lng->txt("upload"));

		return true;
	}

	function performImportObject()
	{
		global $rbacsystem,$lng;

		if (!$rbacsystem->checkAccess('write',$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilias->error_obj->WARNING);
		}
		$this->__initFileObject();
		$this->file_obj->findXMLFile();
		$this->__initParserObject($this->file_obj->getXMLFile(),"import");
		$this->parser_obj->startParsing();
		$number = $this->parser_obj->getCountImported();
		sendInfo($lng->txt("import_finished")." ".$number,true);
		
		ilUtil::redirect("adm_object.php?ref_id=".$_GET["ref_id"]."&cmd=import");
	}
	
	

	function uploadObject()
	{
		global $rbacsystem,$lng;

		if (!$rbacsystem->checkAccess('write',$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilias->error_obj->WARNING);
		}
		
		$this->__initFileObject();
		if(!$this->file_obj->storeUploadedFile($_FILES["importFile"]))	// STEP 1 save file in ...import/mail
		{
			$this->message = $lng->txt("import_file_not_valid"); 
			$this->file_obj->unlinkLast();
		}
		else if(!$this->file_obj->unzip())
		{
			$this->message = $lng->txt("cannot_unzip_file");					// STEP 2 unzip uplaoded file
			$this->file_obj->unlinkLast();
		}
		else if(!$this->file_obj->findXMLFile())						// STEP 3 getXMLFile
		{
			$this->message = $lng->txt("cannot_find_xml");
			$this->file_obj->unlinkLast();
		}
		else if(!$this->__initParserObject($this->file_obj->getXMLFile(),"check"))
		{
			$this->message = $lng->txt("error_parser");				// STEP 4 init sax parser
		}
		else if(!$this->parser_obj->startParsing())
		{
			$this->message = $lng->txt("users_not_imported").":<br/>"; // STEP 5 start parsing
			$this->message .= $this->parser_obj->getNotAssignableUsers();
		}
		// FINALLY CHECK ERROR
		if(!$this->message)
		{
			$this->message = $lng->txt("uploaded_and_checked");
		}
		sendInfo($this->message,true);
		

		ilUtil::redirect("adm_object.php?ref_id=".$_GET["ref_id"]."&cmd=import");
	}

	// PRIVATE
	function __initFileObject()
	{
		include_once "./classes/class.ilFileDataImportMail.php";

		$this->file_obj =& new ilFileDataImportMail();

		return true;
	}
	function __initParserObject($a_xml,$a_mode)
	{
		include_once "./classes/class.ilMailImportParser.php";

		if(!$a_xml)
		{
			return false;
		}

		$this->parser_obj =& new ilMailImportParser($a_xml,$a_mode);
		
		return true;
	}
	
	function &executeCommand()
	{
		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		switch($next_class)
		{
			default:
				if(!$cmd)
				{
					$cmd = "view";
				}
				$cmd .= "Object";
				$this->$cmd();

				break;
		}
		return true;
	}
	
	/**
	* get tabs
	* @access	public
	* @param	object	tabs gui object
	*/
	function getTabs(&$tabs_gui)
	{
		global $rbacsystem;

		if ($rbacsystem->checkAccess("visible,read",$this->object->getRefId()))
		{
			$tabs_gui->addTarget("settings",
				$this->ctrl->getLinkTarget($this, "view"), array("view",""), "", "");
		}

		if ($rbacsystem->checkAccess('edit_permission',$this->object->getRefId()))
		{
			$tabs_gui->addTarget("perm_settings",
				$this->ctrl->getLinkTargetByClass(array(get_class($this),'ilpermissiongui'), "perm"), array("perm","info","owner"), 'ilpermissiongui');
		}
		
		if ($rbacsystem->checkAccess('write',$this->object->getRefId()))
		{
			$tabs_gui->addTarget("import",
				$this->ctrl->getLinkTarget($this, "import"), "import", "", "");
		}
	}
} // END class.ilObjMailGUI
?>

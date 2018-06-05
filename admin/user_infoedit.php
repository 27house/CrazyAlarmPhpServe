<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "user_infoinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$user_info_edit = NULL; // Initialize page object first

class cuser_info_edit extends cuser_info {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{BB44000F-AEA7-4717-AEF5-CDDD7A526D64}";

	// Table name
	var $TableName = 'user_info';

	// Page object name
	var $PageObjName = 'user_info_edit';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (user_info)
		if (!isset($GLOBALS["user_info"]) || get_class($GLOBALS["user_info"]) == "cuser_info") {
			$GLOBALS["user_info"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["user_info"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'user_info', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id->CurrentValue == "")
			$this->Page_Terminate("user_infolist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("user_infolist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
		$this->avatar->Upload->Index = $objForm->Index;
		if ($this->avatar->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->avatar->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->avatar->CurrentValue = $this->avatar->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->account->FldIsDetailKey) {
			$this->account->setFormValue($objForm->GetValue("x_account"));
		}
		if (!$this->nickname->FldIsDetailKey) {
			$this->nickname->setFormValue($objForm->GetValue("x_nickname"));
		}
		if (!$this->password->FldIsDetailKey) {
			$this->password->setFormValue($objForm->GetValue("x_password"));
		}
		if (!$this->create_time->FldIsDetailKey) {
			$this->create_time->setFormValue($objForm->GetValue("x_create_time"));
			$this->create_time->CurrentValue = ew_UnFormatDateTime($this->create_time->CurrentValue, 0);
		}
		if (!$this->login_time->FldIsDetailKey) {
			$this->login_time->setFormValue($objForm->GetValue("x_login_time"));
			$this->login_time->CurrentValue = ew_UnFormatDateTime($this->login_time->CurrentValue, 0);
		}
		if (!$this->describe->FldIsDetailKey) {
			$this->describe->setFormValue($objForm->GetValue("x_describe"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
		$this->account->CurrentValue = $this->account->FormValue;
		$this->nickname->CurrentValue = $this->nickname->FormValue;
		$this->password->CurrentValue = $this->password->FormValue;
		$this->create_time->CurrentValue = $this->create_time->FormValue;
		$this->create_time->CurrentValue = ew_UnFormatDateTime($this->create_time->CurrentValue, 0);
		$this->login_time->CurrentValue = $this->login_time->FormValue;
		$this->login_time->CurrentValue = ew_UnFormatDateTime($this->login_time->CurrentValue, 0);
		$this->describe->CurrentValue = $this->describe->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id->setDbValue($rs->fields('id'));
		$this->account->setDbValue($rs->fields('account'));
		$this->nickname->setDbValue($rs->fields('nickname'));
		$this->password->setDbValue($rs->fields('password'));
		$this->avatar->Upload->DbValue = $rs->fields('avatar');
		$this->avatar->CurrentValue = $this->avatar->Upload->DbValue;
		$this->create_time->setDbValue($rs->fields('create_time'));
		$this->login_time->setDbValue($rs->fields('login_time'));
		$this->describe->setDbValue($rs->fields('describe'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->account->DbValue = $row['account'];
		$this->nickname->DbValue = $row['nickname'];
		$this->password->DbValue = $row['password'];
		$this->avatar->Upload->DbValue = $row['avatar'];
		$this->create_time->DbValue = $row['create_time'];
		$this->login_time->DbValue = $row['login_time'];
		$this->describe->DbValue = $row['describe'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// account
		// nickname
		// password
		// avatar
		// create_time
		// login_time
		// describe

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// account
			$this->account->ViewValue = $this->account->CurrentValue;
			$this->account->ViewCustomAttributes = "";

			// nickname
			$this->nickname->ViewValue = $this->nickname->CurrentValue;
			$this->nickname->ViewCustomAttributes = "";

			// password
			$this->password->ViewValue = $this->password->CurrentValue;
			$this->password->ViewCustomAttributes = "";

			// avatar
			if (!ew_Empty($this->avatar->Upload->DbValue)) {
				$this->avatar->ImageWidth = 50;
				$this->avatar->ImageHeight = 50;
				$this->avatar->ImageAlt = $this->avatar->FldAlt();
				$this->avatar->ViewValue = ew_UploadPathEx(FALSE, $this->avatar->UploadPath) . $this->avatar->Upload->DbValue;
			} else {
				$this->avatar->ViewValue = "";
			}
			$this->avatar->ViewCustomAttributes = "";

			// create_time
			$this->create_time->ViewValue = $this->create_time->CurrentValue;
			$this->create_time->ViewCustomAttributes = "";

			// login_time
			$this->login_time->ViewValue = $this->login_time->CurrentValue;
			$this->login_time->ViewCustomAttributes = "";

			// describe
			$this->describe->ViewValue = $this->describe->CurrentValue;
			$this->describe->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// account
			$this->account->LinkCustomAttributes = "";
			$this->account->HrefValue = "";
			$this->account->TooltipValue = "";

			// nickname
			$this->nickname->LinkCustomAttributes = "";
			$this->nickname->HrefValue = "";
			$this->nickname->TooltipValue = "";

			// password
			$this->password->LinkCustomAttributes = "";
			$this->password->HrefValue = "";
			$this->password->TooltipValue = "";

			// avatar
			$this->avatar->LinkCustomAttributes = "";
			$this->avatar->HrefValue = "";
			$this->avatar->HrefValue2 = $this->avatar->UploadPath . $this->avatar->Upload->DbValue;
			$this->avatar->TooltipValue = "";

			// create_time
			$this->create_time->LinkCustomAttributes = "";
			$this->create_time->HrefValue = "";
			$this->create_time->TooltipValue = "";

			// login_time
			$this->login_time->LinkCustomAttributes = "";
			$this->login_time->HrefValue = "";
			$this->login_time->TooltipValue = "";

			// describe
			$this->describe->LinkCustomAttributes = "";
			$this->describe->HrefValue = "";
			$this->describe->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// account
			$this->account->EditCustomAttributes = "";
			$this->account->EditValue = ew_HtmlEncode($this->account->CurrentValue);
			$this->account->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->account->FldCaption()));

			// nickname
			$this->nickname->EditCustomAttributes = "";
			$this->nickname->EditValue = ew_HtmlEncode($this->nickname->CurrentValue);
			$this->nickname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->nickname->FldCaption()));

			// password
			$this->password->EditCustomAttributes = "";
			$this->password->EditValue = ew_HtmlEncode($this->password->CurrentValue);
			$this->password->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->password->FldCaption()));

			// avatar
			$this->avatar->EditCustomAttributes = "";
			if (!ew_Empty($this->avatar->Upload->DbValue)) {
				$this->avatar->ImageWidth = 50;
				$this->avatar->ImageHeight = 50;
				$this->avatar->ImageAlt = $this->avatar->FldAlt();
				$this->avatar->EditValue = ew_UploadPathEx(FALSE, $this->avatar->UploadPath) . $this->avatar->Upload->DbValue;
			} else {
				$this->avatar->EditValue = "";
			}
			if (!ew_Empty($this->avatar->CurrentValue))
				$this->avatar->Upload->FileName = $this->avatar->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->avatar);

			// create_time
			// login_time
			// describe

			$this->describe->EditCustomAttributes = "";
			$this->describe->EditValue = $this->describe->CurrentValue;
			$this->describe->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->describe->FldCaption()));

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// account
			$this->account->HrefValue = "";

			// nickname
			$this->nickname->HrefValue = "";

			// password
			$this->password->HrefValue = "";

			// avatar
			$this->avatar->HrefValue = "";
			$this->avatar->HrefValue2 = $this->avatar->UploadPath . $this->avatar->Upload->DbValue;

			// create_time
			$this->create_time->HrefValue = "";

			// login_time
			$this->login_time->HrefValue = "";

			// describe
			$this->describe->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ew_CheckInteger($this->id->FormValue)) {
			ew_AddMessage($gsFormError, $this->id->FldErrMsg());
		}
		if (!$this->account->FldIsDetailKey && !is_null($this->account->FormValue) && $this->account->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->account->FldCaption());
		}
		if (!$this->nickname->FldIsDetailKey && !is_null($this->nickname->FormValue) && $this->nickname->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->nickname->FldCaption());
		}
		if (!$this->password->FldIsDetailKey && !is_null($this->password->FormValue) && $this->password->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->password->FldCaption());
		}
		if (is_null($this->avatar->Upload->Value)) {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->avatar->FldCaption());
		}
		if (!$this->describe->FldIsDetailKey && !is_null($this->describe->FormValue) && $this->describe->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->describe->FldCaption());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// account
			$this->account->SetDbValueDef($rsnew, $this->account->CurrentValue, "", $this->account->ReadOnly);

			// nickname
			$this->nickname->SetDbValueDef($rsnew, $this->nickname->CurrentValue, NULL, $this->nickname->ReadOnly);

			// password
			$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, "", $this->password->ReadOnly || (EW_ENCRYPTED_PASSWORD && $rs->fields('password') == $this->password->CurrentValue));

			// avatar
			if (!($this->avatar->ReadOnly) && !$this->avatar->Upload->KeepFile) {
				$this->avatar->Upload->DbValue = $rs->fields('avatar'); // Get original value
				if ($this->avatar->Upload->FileName == "") {
					$rsnew['avatar'] = NULL;
				} else {
					$rsnew['avatar'] = $this->avatar->Upload->FileName;
				}
			}

			// create_time
			$this->create_time->SetDbValueDef($rsnew, ew_CurrentDateTime(), ew_CurrentDate());
			$rsnew['create_time'] = &$this->create_time->DbValue;

			// login_time
			$this->login_time->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['login_time'] = &$this->login_time->DbValue;

			// describe
			$this->describe->SetDbValueDef($rsnew, $this->describe->CurrentValue, NULL, $this->describe->ReadOnly);
			if (!$this->avatar->Upload->KeepFile) {
				if (!ew_Empty($this->avatar->Upload->Value)) {
					$rsnew['avatar'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->avatar->UploadPath), $rsnew['avatar']); // Get new file name
				}
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
					if (!$this->avatar->Upload->KeepFile) {
						if (!ew_Empty($this->avatar->Upload->Value)) {
							$this->avatar->Upload->SaveToFile($this->avatar->UploadPath, $rsnew['avatar'], TRUE);
						}
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// avatar
		ew_CleanUploadTempPath($this->avatar, $this->avatar->Upload->Index);
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "user_infolist.php", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, ew_CurrentUrl());
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($user_info_edit)) $user_info_edit = new cuser_info_edit();

// Page init
$user_info_edit->Page_Init();

// Page main
$user_info_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$user_info_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var user_info_edit = new ew_Page("user_info_edit");
user_info_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = user_info_edit.PageID; // For backward compatibility

// Form object
var fuser_infoedit = new ew_Form("fuser_infoedit");

// Validate form
fuser_infoedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_id");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($user_info->id->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_account");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user_info->account->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_nickname");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user_info->nickname->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_password");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user_info->password->FldCaption()) ?>");
			felm = this.GetElements("x" + infix + "_avatar");
			elm = this.GetElements("fn_x" + infix + "_avatar");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user_info->avatar->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_describe");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($user_info->describe->FldCaption()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fuser_infoedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuser_infoedit.ValidateRequired = true;
<?php } else { ?>
fuser_infoedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $user_info_edit->ShowPageHeader(); ?>
<?php
$user_info_edit->ShowMessage();
?>
<form name="fuser_infoedit" id="fuser_infoedit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="user_info">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_user_infoedit" class="table table-bordered table-striped">
<?php if ($user_info->id->Visible) { // id ?>
	<tr id="r_id">
		<td><span id="elh_user_info_id"><?php echo $user_info->id->FldCaption() ?></span></td>
		<td<?php echo $user_info->id->CellAttributes() ?>>
<span id="el_user_info_id" class="control-group">
<span<?php echo $user_info->id->ViewAttributes() ?>>
<?php echo $user_info->id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($user_info->id->CurrentValue) ?>">
<?php echo $user_info->id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user_info->account->Visible) { // account ?>
	<tr id="r_account">
		<td><span id="elh_user_info_account"><?php echo $user_info->account->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $user_info->account->CellAttributes() ?>>
<span id="el_user_info_account" class="control-group">
<input type="text" data-field="x_account" name="x_account" id="x_account" size="30" maxlength="225" placeholder="<?php echo $user_info->account->PlaceHolder ?>" value="<?php echo $user_info->account->EditValue ?>"<?php echo $user_info->account->EditAttributes() ?>>
</span>
<?php echo $user_info->account->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user_info->nickname->Visible) { // nickname ?>
	<tr id="r_nickname">
		<td><span id="elh_user_info_nickname"><?php echo $user_info->nickname->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $user_info->nickname->CellAttributes() ?>>
<span id="el_user_info_nickname" class="control-group">
<input type="text" data-field="x_nickname" name="x_nickname" id="x_nickname" size="30" maxlength="15" placeholder="<?php echo $user_info->nickname->PlaceHolder ?>" value="<?php echo $user_info->nickname->EditValue ?>"<?php echo $user_info->nickname->EditAttributes() ?>>
</span>
<?php echo $user_info->nickname->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user_info->password->Visible) { // password ?>
	<tr id="r_password">
		<td><span id="elh_user_info_password"><?php echo $user_info->password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $user_info->password->CellAttributes() ?>>
<span id="el_user_info_password" class="control-group">
<input type="text" data-field="x_password" name="x_password" id="x_password" size="30" maxlength="16" placeholder="<?php echo $user_info->password->PlaceHolder ?>" value="<?php echo $user_info->password->EditValue ?>"<?php echo $user_info->password->EditAttributes() ?>>
</span>
<?php echo $user_info->password->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user_info->avatar->Visible) { // avatar ?>
	<tr id="r_avatar">
		<td><span id="elh_user_info_avatar"><?php echo $user_info->avatar->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $user_info->avatar->CellAttributes() ?>>
<div id="el_user_info_avatar" class="control-group">
<span id="fd_x_avatar">
<span class="btn btn-small fileinput-button"<?php if ($user_info->avatar->ReadOnly || $user_info->avatar->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_avatar" name="x_avatar" id="x_avatar">
</span>
<input type="hidden" name="fn_x_avatar" id= "fn_x_avatar" value="<?php echo $user_info->avatar->Upload->FileName ?>">
<?php if (@$_POST["fa_x_avatar"] == "0") { ?>
<input type="hidden" name="fa_x_avatar" id= "fa_x_avatar" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_avatar" id= "fa_x_avatar" value="1">
<?php } ?>
<input type="hidden" name="fs_x_avatar" id= "fs_x_avatar" value="0">
</span>
<table id="ft_x_avatar" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</div>
<?php echo $user_info->avatar->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($user_info->describe->Visible) { // describe ?>
	<tr id="r_describe">
		<td><span id="elh_user_info_describe"><?php echo $user_info->describe->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $user_info->describe->CellAttributes() ?>>
<span id="el_user_info_describe" class="control-group">
<textarea data-field="x_describe" class="editor" name="x_describe" id="x_describe" cols="35" rows="4" placeholder="<?php echo $user_info->describe->PlaceHolder ?>"<?php echo $user_info->describe->EditAttributes() ?>><?php echo $user_info->describe->EditValue ?></textarea>
</span>
<?php echo $user_info->describe->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fuser_infoedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$user_info_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$user_info_edit->Page_Terminate();
?>

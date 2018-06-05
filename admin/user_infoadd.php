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

$user_info_add = NULL; // Initialize page object first

class cuser_info_add extends cuser_info {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{BB44000F-AEA7-4717-AEF5-CDDD7A526D64}";

	// Table name
	var $TableName = 'user_info';

	// Page object name
	var $PageObjName = 'user_info_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id"] != "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->setKey("id", $this->id->CurrentValue); // Set up key
			} else {
				$this->setKey("id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("user_infolist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "user_infoview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Load default values
	function LoadDefaultValues() {
		$this->account->CurrentValue = NULL;
		$this->account->OldValue = $this->account->CurrentValue;
		$this->nickname->CurrentValue = NULL;
		$this->nickname->OldValue = $this->nickname->CurrentValue;
		$this->password->CurrentValue = NULL;
		$this->password->OldValue = $this->password->CurrentValue;
		$this->avatar->Upload->DbValue = NULL;
		$this->avatar->OldValue = $this->avatar->Upload->DbValue;
		$this->avatar->CurrentValue = NULL; // Clear file related field
		$this->create_time->CurrentValue = NULL;
		$this->create_time->OldValue = $this->create_time->CurrentValue;
		$this->login_time->CurrentValue = NULL;
		$this->login_time->OldValue = $this->login_time->CurrentValue;
		$this->describe->CurrentValue = NULL;
		$this->describe->OldValue = $this->describe->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
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
		$this->LoadOldRecord();
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id")) <> "")
			$this->id->CurrentValue = $this->getKey("id"); // id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->avatar);

			// create_time
			// login_time
			// describe

			$this->describe->EditCustomAttributes = "";
			$this->describe->EditValue = $this->describe->CurrentValue;
			$this->describe->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->describe->FldCaption()));

			// Edit refer script
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// account
		$this->account->SetDbValueDef($rsnew, $this->account->CurrentValue, "", FALSE);

		// nickname
		$this->nickname->SetDbValueDef($rsnew, $this->nickname->CurrentValue, NULL, FALSE);

		// password
		$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, "", FALSE);

		// avatar
		if (!$this->avatar->Upload->KeepFile) {
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
		$this->describe->SetDbValueDef($rsnew, $this->describe->CurrentValue, NULL, FALSE);
		if (!$this->avatar->Upload->KeepFile) {
			if (!ew_Empty($this->avatar->Upload->Value)) {
				$rsnew['avatar'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->avatar->UploadPath), $rsnew['avatar']); // Get new file name
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
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
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->id->setDbValue($conn->Insert_ID());
			$rsnew['id'] = $this->id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// avatar
		ew_CleanUploadTempPath($this->avatar, $this->avatar->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "user_infolist.php", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, ew_CurrentUrl());
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
if (!isset($user_info_add)) $user_info_add = new cuser_info_add();

// Page init
$user_info_add->Page_Init();

// Page main
$user_info_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$user_info_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var user_info_add = new ew_Page("user_info_add");
user_info_add.PageID = "add"; // Page ID
var EW_PAGE_ID = user_info_add.PageID; // For backward compatibility

// Form object
var fuser_infoadd = new ew_Form("fuser_infoadd");

// Validate form
fuser_infoadd.Validate = function() {
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
fuser_infoadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuser_infoadd.ValidateRequired = true;
<?php } else { ?>
fuser_infoadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $user_info_add->ShowPageHeader(); ?>
<?php
$user_info_add->ShowMessage();
?>
<form name="fuser_infoadd" id="fuser_infoadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="user_info">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_user_infoadd" class="table table-bordered table-striped">
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
<input type="hidden" name="fa_x_avatar" id= "fa_x_avatar" value="0">
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
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fuser_infoadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$user_info_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$user_info_add->Page_Terminate();
?>

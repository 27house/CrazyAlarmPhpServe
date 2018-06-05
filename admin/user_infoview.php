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

$user_info_view = NULL; // Initialize page object first

class cuser_info_view extends cuser_info {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{BB44000F-AEA7-4717-AEF5-CDDD7A526D64}";

	// Table name
	var $TableName = 'user_info';

	// Page object name
	var $PageObjName = 'user_info_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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
		$KeyUrl = "";
		if (@$_GET["id"] <> "") {
			$this->RecKey["id"] = $_GET["id"];
			$KeyUrl .= "&amp;id=" . urlencode($this->RecKey["id"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'user_info', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;
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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["id"] <> "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->RecKey["id"] = $this->id->QueryStringValue;
			} else {
				$sReturnUrl = "user_infolist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "user_infolist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "user_infolist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "");

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "");

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"ewAction ewCopy\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "");

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "");

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
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
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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
			$this->avatar->UploadPath = ”upload/avatar“;
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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "user_infolist.php", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, ew_CurrentUrl());
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($user_info_view)) $user_info_view = new cuser_info_view();

// Page init
$user_info_view->Page_Init();

// Page main
$user_info_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$user_info_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var user_info_view = new ew_Page("user_info_view");
user_info_view.PageID = "view"; // Page ID
var EW_PAGE_ID = user_info_view.PageID; // For backward compatibility

// Form object
var fuser_infoview = new ew_Form("fuser_infoview");

// Form_CustomValidate event
fuser_infoview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuser_infoview.ValidateRequired = true;
<?php } else { ?>
fuser_infoview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $user_info_view->ExportOptions->Render("body") ?>
<?php if (!$user_info_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($user_info_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $user_info_view->ShowPageHeader(); ?>
<?php
$user_info_view->ShowMessage();
?>
<form name="fuser_infoview" id="fuser_infoview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="user_info">
<table class="ewGrid"><tr><td>
<table id="tbl_user_infoview" class="table table-bordered table-striped">
<?php if ($user_info->id->Visible) { // id ?>
	<tr id="r_id">
		<td><span id="elh_user_info_id"><?php echo $user_info->id->FldCaption() ?></span></td>
		<td<?php echo $user_info->id->CellAttributes() ?>>
<span id="el_user_info_id" class="control-group">
<span<?php echo $user_info->id->ViewAttributes() ?>>
<?php echo $user_info->id->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->account->Visible) { // account ?>
	<tr id="r_account">
		<td><span id="elh_user_info_account"><?php echo $user_info->account->FldCaption() ?></span></td>
		<td<?php echo $user_info->account->CellAttributes() ?>>
<span id="el_user_info_account" class="control-group">
<span<?php echo $user_info->account->ViewAttributes() ?>>
<?php echo $user_info->account->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->nickname->Visible) { // nickname ?>
	<tr id="r_nickname">
		<td><span id="elh_user_info_nickname"><?php echo $user_info->nickname->FldCaption() ?></span></td>
		<td<?php echo $user_info->nickname->CellAttributes() ?>>
<span id="el_user_info_nickname" class="control-group">
<span<?php echo $user_info->nickname->ViewAttributes() ?>>
<?php echo $user_info->nickname->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->password->Visible) { // password ?>
	<tr id="r_password">
		<td><span id="elh_user_info_password"><?php echo $user_info->password->FldCaption() ?></span></td>
		<td<?php echo $user_info->password->CellAttributes() ?>>
<span id="el_user_info_password" class="control-group">
<span<?php echo $user_info->password->ViewAttributes() ?>>
<?php echo $user_info->password->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->avatar->Visible) { // avatar ?>
	<tr id="r_avatar">
		<td><span id="elh_user_info_avatar"><?php echo $user_info->avatar->FldCaption() ?></span></td>
		<td<?php echo $user_info->avatar->CellAttributes() ?>>
<span id="el_user_info_avatar" class="control-group">
<span>
<?php if ($user_info->avatar->LinkAttributes() <> "") { ?>
<?php if (!empty($user_info->avatar->Upload->DbValue)) { ?>
<img src="<?php echo $user_info->avatar->ViewValue ?>" alt=""<?php echo $user_info->avatar->ViewAttributes() ?>>
<?php } elseif (!in_array($user_info->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($user_info->avatar->Upload->DbValue)) { ?>
<img src="<?php echo $user_info->avatar->ViewValue ?>" alt=""<?php echo $user_info->avatar->ViewAttributes() ?>>
<?php } elseif (!in_array($user_info->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->create_time->Visible) { // create_time ?>
	<tr id="r_create_time">
		<td><span id="elh_user_info_create_time"><?php echo $user_info->create_time->FldCaption() ?></span></td>
		<td<?php echo $user_info->create_time->CellAttributes() ?>>
<span id="el_user_info_create_time" class="control-group">
<span<?php echo $user_info->create_time->ViewAttributes() ?>>
<?php echo $user_info->create_time->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->login_time->Visible) { // login_time ?>
	<tr id="r_login_time">
		<td><span id="elh_user_info_login_time"><?php echo $user_info->login_time->FldCaption() ?></span></td>
		<td<?php echo $user_info->login_time->CellAttributes() ?>>
<span id="el_user_info_login_time" class="control-group">
<span<?php echo $user_info->login_time->ViewAttributes() ?>>
<?php echo $user_info->login_time->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($user_info->describe->Visible) { // describe ?>
	<tr id="r_describe">
		<td><span id="elh_user_info_describe"><?php echo $user_info->describe->FldCaption() ?></span></td>
		<td<?php echo $user_info->describe->CellAttributes() ?>>
<span id="el_user_info_describe" class="control-group">
<span<?php echo $user_info->describe->ViewAttributes() ?>>
<?php echo $user_info->describe->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
fuser_infoview.Init();
</script>
<?php
$user_info_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$user_info_view->Page_Terminate();
?>

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

$user_info_delete = NULL; // Initialize page object first

class cuser_info_delete extends cuser_info {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{BB44000F-AEA7-4717-AEF5-CDDD7A526D64}";

	// Table name
	var $TableName = 'user_info';

	// Page object name
	var $PageObjName = 'user_info_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("user_infolist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in user_info class, user_infoinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['id'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "user_infolist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
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
if (!isset($user_info_delete)) $user_info_delete = new cuser_info_delete();

// Page init
$user_info_delete->Page_Init();

// Page main
$user_info_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$user_info_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var user_info_delete = new ew_Page("user_info_delete");
user_info_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = user_info_delete.PageID; // For backward compatibility

// Form object
var fuser_infodelete = new ew_Form("fuser_infodelete");

// Form_CustomValidate event
fuser_infodelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuser_infodelete.ValidateRequired = true;
<?php } else { ?>
fuser_infodelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($user_info_delete->Recordset = $user_info_delete->LoadRecordset())
	$user_info_deleteTotalRecs = $user_info_delete->Recordset->RecordCount(); // Get record count
if ($user_info_deleteTotalRecs <= 0) { // No record found, exit
	if ($user_info_delete->Recordset)
		$user_info_delete->Recordset->Close();
	$user_info_delete->Page_Terminate("user_infolist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $user_info_delete->ShowPageHeader(); ?>
<?php
$user_info_delete->ShowMessage();
?>
<form name="fuser_infodelete" id="fuser_infodelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="user_info">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($user_info_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_user_infodelete"<?php if (ew_IsMobile()) { ?> class="ewTable ewTableSeparate"<?php } else { echo ' class="ewTable table table-striped table-bordered table-hover" ';}?>>
<?php echo $user_info->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($user_info->id->Visible) { // id ?>
		<td><span id="elh_user_info_id" class="user_info_id"><?php echo $user_info->id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->account->Visible) { // account ?>
		<td><span id="elh_user_info_account" class="user_info_account"><?php echo $user_info->account->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->nickname->Visible) { // nickname ?>
		<td><span id="elh_user_info_nickname" class="user_info_nickname"><?php echo $user_info->nickname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->avatar->Visible) { // avatar ?>
		<td><span id="elh_user_info_avatar" class="user_info_avatar"><?php echo $user_info->avatar->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->create_time->Visible) { // create_time ?>
		<td><span id="elh_user_info_create_time" class="user_info_create_time"><?php echo $user_info->create_time->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->login_time->Visible) { // login_time ?>
		<td><span id="elh_user_info_login_time" class="user_info_login_time"><?php echo $user_info->login_time->FldCaption() ?></span></td>
<?php } ?>
<?php if ($user_info->describe->Visible) { // describe ?>
		<td><span id="elh_user_info_describe" class="user_info_describe"><?php echo $user_info->describe->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$user_info_delete->RecCnt = 0;
$i = 0;
while (!$user_info_delete->Recordset->EOF) {
	$user_info_delete->RecCnt++;
	$user_info_delete->RowCnt++;

	// Set row properties
	$user_info->ResetAttrs();
	$user_info->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$user_info_delete->LoadRowValues($user_info_delete->Recordset);

	// Render row
	$user_info_delete->RenderRow();
?>
	<tr<?php echo $user_info->RowAttributes() ?>>
<?php if ($user_info->id->Visible) { // id ?>
		<td<?php echo $user_info->id->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_id" class="control-group user_info_id">
<span<?php echo $user_info->id->ViewAttributes() ?>>
<?php echo $user_info->id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($user_info->account->Visible) { // account ?>
		<td<?php echo $user_info->account->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_account" class="control-group user_info_account">
<span<?php echo $user_info->account->ViewAttributes() ?>>
<?php echo $user_info->account->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($user_info->nickname->Visible) { // nickname ?>
		<td<?php echo $user_info->nickname->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_nickname" class="control-group user_info_nickname">
<span<?php echo $user_info->nickname->ViewAttributes() ?>>
<?php echo $user_info->nickname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($user_info->avatar->Visible) { // avatar ?>
		<td<?php echo $user_info->avatar->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_avatar" class="control-group user_info_avatar">
<span>
<?php if ($user_info->avatar->LinkAttributes() <> "") { ?>
<?php if (!empty($user_info->avatar->Upload->DbValue)) { ?>
<img src="<?php echo $user_info->avatar->ListViewValue() ?>" alt=""<?php echo $user_info->avatar->ViewAttributes() ?>>
<?php } elseif (!in_array($user_info->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($user_info->avatar->Upload->DbValue)) { ?>
<img src="<?php echo $user_info->avatar->ListViewValue() ?>" alt=""<?php echo $user_info->avatar->ViewAttributes() ?>>
<?php } elseif (!in_array($user_info->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($user_info->create_time->Visible) { // create_time ?>
		<td<?php echo $user_info->create_time->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_create_time" class="control-group user_info_create_time">
<span<?php echo $user_info->create_time->ViewAttributes() ?>>
<?php echo $user_info->create_time->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($user_info->login_time->Visible) { // login_time ?>
		<td<?php echo $user_info->login_time->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_login_time" class="control-group user_info_login_time">
<span<?php echo $user_info->login_time->ViewAttributes() ?>>
<?php echo $user_info->login_time->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($user_info->describe->Visible) { // describe ?>
		<td<?php echo $user_info->describe->CellAttributes() ?>>
<span id="el<?php echo $user_info_delete->RowCnt ?>_user_info_describe" class="control-group user_info_describe">
<span<?php echo $user_info->describe->ViewAttributes() ?>>
<?php echo $user_info->describe->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$user_info_delete->Recordset->MoveNext();
}
$user_info_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fuser_infodelete.Init();
</script>
<?php
$user_info_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$user_info_delete->Page_Terminate();
?>

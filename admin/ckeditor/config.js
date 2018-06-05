/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
CKEDITOR.config.menu_groups = 'clipboard,' +
	'form,' +
	'tablecell,tablecellproperties,tablerow,tablecolumn,table,' +
	'anchor,link,image,flash,' +
	'checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea,div';
	
CKEDITOR.config.toolbarGroups = [
    { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },     
    { name: 'links' },     
    { name: 'insert' },     
    { name: 'forms' },     
    { name: 'tools' },
    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },     
    { name: 'others' },  
     '/',
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ] },    
    { name: 'styles' },     
    { name: 'colors' },     
    { name: 'about' }
];

config.extraPlugins = 'justify,showblocks,font,panelbutton,floatpanel,colorbutton,multiimg,video,smiley';
	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'p;div;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	config.image_previewText = ' '; 
	config.filebrowserUploadUrl = "ckeditor/upload.php";
	
	// editorIndicates whether the contents to be edited are being input as a full HTML page. 
    // A full page includes the <html>, <head>, and <body> elements. 
    // The final output will also reflect this setting, including the <body> contents only if this setting is disabled.
    config.fullPage = false; 
    
    // set editor html no display auto filter
    config.allowedContent= true;
	
};

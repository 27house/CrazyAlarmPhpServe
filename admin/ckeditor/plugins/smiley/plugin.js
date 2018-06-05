﻿/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.plugins.add( 'smiley', {
	requires: 'dialog',
	lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
	icons: 'smiley', // %REMOVE_LINE_CORE%
	hidpi: true, // %REMOVE_LINE_CORE%
	init: function( editor ) {
		editor.config.smiley_path = editor.config.smiley_path || ( this.path + 'images/' );
		editor.addCommand( 'smiley', new CKEDITOR.dialogCommand( 'smiley', {
			allowedContent: 'img[alt,height,!src,title,width]',
			requiredContent: 'img'
		} ) );
		editor.ui.addButton && editor.ui.addButton( 'Smiley', {
			label: editor.lang.smiley.toolbar,
			command: 'smiley',
			toolbar: 'insert,50'
		});
		CKEDITOR.dialog.add( 'smiley', this.path + 'dialogs/smiley.js' );
	}
});

/**
 * The base path used to build the URL for the smiley images. It must end with a slash.
 *
 *		config.smiley_path = 'http://www.example.com/images/smileys/';
 *
 *		config.smiley_path = '/images/smileys/';
 *
 * @cfg {String} [smiley_path=CKEDITOR.basePath + 'plugins/smiley/images/']
 * @member CKEDITOR.config
 */

/**
 * The file names for the smileys to be displayed. These files must be
 * contained inside the URL path defined with the {@link #smiley_path} setting.
 *
 *		// This is actually the default value.
 *		config.smiley_images = [
 *			'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tongue_smile.gif',
 *			'embarrassed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif',
 *			'devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif',
 *			'broken_heart.gif','kiss.gif','envelope.gif'
 *		];
 *
 * @cfg
 * @member CKEDITOR.config
 */
CKEDITOR.config.smiley_images = [
	'regular_smile.gif', 'sad_smile.gif', 'wink_smile.gif', 'teeth_smile.gif', 'confused_smile.gif', 'tongue_smile.gif',
	'embarrassed_smile.gif', 'omg_smile.gif', 'whatchutalkingabout_smile.gif', 'angry_smile.gif', 'angel_smile.gif', 'shades_smile.gif',
	'devil_smile.gif', 'cry_smile.gif', 'lightbulb.gif', 'thumbs_down.gif', 'thumbs_up.gif', 'heart.gif',
	'broken_heart.gif', 'kiss.gif', 'envelope.gif','e100.gif','e101.gif','e102.gif','e103.gif','e104.gif','e105.gif','e106.gif','e107.gif','e108.gif','e109.gif','e110.gif','e111.gif','e112.gif','e113.gif','e114.gif','e115.gif','e116.gif',
	                           'e117.gif','e118.gif','e119.gif','e120.gif','e121.gif','e122.gif','e123.gif','e124.gif','e125.gif','e126.gif','e127.gif','e128.gif','e129.gif','e130.gif',
	                            
	                           'e131.gif','e132.gif','e133.gif','e134.gif','e135.gif','e136.gif','e137.gif','e138.gif','e139.gif','e140.gif','e141.gif','e142.gif','e143.gif','e144.gif',
	                            
	                           'e145.gif','e146.gif',"e147.gif",'e148.gif','e149.gif','e150.gif','e151.gif','e152.gif','e153.gif','e154.gif','e163.gif','e164.gif','e179.gif','e180.gif','e181.gif','e182.gif','e183.gif','e184.gif','e185.gif','e186.gif','e187.gif','e188.gif','e189.gif',
	                           'y1.gif','y2.gif','y3.gif','y4.gif','y5.gif','y6.gif','y7.gif','y8gif','y9.gif','y10.gif','y11.gif','y12.gif','y13.gif','y14.gif','y15.gif' ];

/**
 * The description to be used for each of the smileys defined in the
 * {@link CKEDITOR.config#smiley_images} setting. Each entry in this array list
 * must match its relative pair in the {@link CKEDITOR.config#smiley_images}
 * setting.
 *
 *		// Default settings.
 *		config.smiley_descriptions = [
 *			'smiley', 'sad', 'wink', 'laugh', 'frown', 'cheeky', 'blush', 'surprise',
 *			'indecision', 'angry', 'angel', 'cool', 'devil', 'crying', 'enlightened', 'no',
 *			'yes', 'heart', 'broken heart', 'kiss', 'mail'
 *		];
 *
 *		// Use textual emoticons as description.
 *		config.smiley_descriptions = [
 *			':)', ':(', ';)', ':D', ':/', ':P', ':*)', ':-o',
 *			':|', '>:(', 'o:)', '8-)', '>:-)', ';(', '', '', '',
 *			'', '', ':-*', ''
 *		];
 *
 * @cfg
 * @member CKEDITOR.config
 */
CKEDITOR.config.smiley_descriptions = [
	'smiley', 'sad', 'wink', 'laugh', 'frown', 'cheeky', 'blush', 'surprise',
	'indecision', 'angry', 'angel', 'cool', 'devil', 'crying', 'enlightened', 'no',
	'yes', 'heart', 'broken heart', 'kiss', 'mail'
];

/**
 * The number of columns to be generated by the smilies matrix.
 *
 *		config.smiley_columns = 6;
 *
 * @since 3.3.2
 * @cfg {Number} [smiley_columns=8]
 * @member CKEDITOR.config
 */
